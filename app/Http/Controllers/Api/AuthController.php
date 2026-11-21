<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Notifications\ResetPasswordMobileNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * GET /api/user
     * Ambil data user yang sedang login beserta relasi role.
     */
    public function me(Request $request)
    {
        return response()->json($request->user()->load('role'));
    }

    /**
     * POST /api/register
     * Registrasi pelanggan baru & dapetin token.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|string|email|max:150|unique:users,email',
            'no_telepon'            => 'nullable|string|max:20',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'role_id'    => 2, // Pelanggan
            'username'   => $request->name,
            'email'      => $request->email,
            'no_telepon' => $request->no_telepon,
            'password'   => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'      => true,
            'message'      => 'Registrasi berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user->load('role'),
        ], 201);
    }

    /**
     * POST /api/login
     * Login & dapetin token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        // Hapus token lama (opsional, biar gak numpuk)
        $user->tokens()->delete();

        // Bikin token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'      => true,
            'message'      => 'Login berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user->load('role'),
        ], 200);
    }

    /**
     * POST /api/auth/google
     * Login / Registrasi via Google OAuth (Mobile / Web App).
     */
    public function googleAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email|max:150',
            'name'      => 'nullable|string|max:100',
            'google_id' => 'nullable|string|max:100',
            'avatar'    => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data Google tidak valid.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $email = $request->email;
        $user  = User::where('email', $email)->first();

        if ($user) {
            // Update google_id & avatar jika belum ada
            $user->update([
                'google_id' => $user->google_id ?: $request->google_id,
                'avatar'    => $user->avatar ?: $request->avatar,
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);
        } else {
            // Buat user baru dengan default role pelanggan
            $defaultRoleId = Role::where('nama_role', 'pelanggan')->value('id')
                          ?? Role::where('nama_role', 'user')->value('id')
                          ?? 2;

            $baseUsername = $request->name ?: explode('@', $email)[0];
            $username = Str::slug($baseUsername, '_');
            if (empty($username)) $username = 'user_' . rand(1000, 9999);
            $cleanUsername = $username;
            $counter = 1;
            while (User::where('username', $cleanUsername)->exists()) {
                $cleanUsername = $username . '_' . $counter;
                $counter++;
            }

            $user = User::create([
                'username'          => $cleanUsername,
                'email'             => $email,
                'google_id'         => $request->google_id,
                'avatar'            => $request->avatar,
                'password'          => null,
                'no_telepon'        => null,
                'role_id'           => $defaultRoleId,
                'email_verified_at' => now(),
            ]);
        }

        // Bikin token baru
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'      => true,
            'message'      => 'Login Google berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'         => $user->id,
                'name'       => $user->username,
                'email'      => $user->email,
                'no_telepon' => $user->no_telepon,
                'role'       => $user->role ? $user->role->nama_role : 'pelanggan',
                'avatar'     => $user->avatar,
            ],
        ], 200);
    }

    /**
     * POST /api/forgot-password
     * Kirim link & token reset password ke email.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email wajib diisi dan harus berformat valid.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar dalam sistem EcoTahu.',
            ], 404);
        }

        try {
            // Generate token reset password
            $token = Password::createToken($user);

            // Kirim notifikasi email kustom yang menyertakan deep link aplikasi mobile
            $user->notify(new ResetPasswordMobileNotification($token));

            return response()->json([
                'success' => true,
                'message' => 'Link dan kode reset password telah dikirim ke email Anda.',
                'token'   => $token,
                'email'   => $user->email,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'message' => 'Permintaan reset password berhasil dikirim. Silakan periksa inbox email Anda.',
                'token'   => isset($token) ? $token : null,
                'email'   => $user->email,
            ], 200);
        }
    }

    /**
     * POST /api/reset-password
     * Eksekusi reset password baru dengan token dari email.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'                 => 'required|string',
            'email'                 => 'required|email',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal. Pastikan password minimal 8 karakter dan konfirmasi cocok.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password'       => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset! Silakan login dengan password baru Anda.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => __($status) ?: 'Token reset tidak valid atau telah kadaluarsa.',
            ], 400);
        }
    }

    /**
     * PUT /api/user/profile
     * Update profil pengguna (nama, email, no_telepon, alamat).
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'       => 'nullable|string|max:100',
            'username'   => 'nullable|string|max:100',
            'email'      => 'required|string|email|max:150|unique:users,email,' . $user->id,
            'no_telepon' => 'nullable|string|max:20',
            'alamat'     => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $username = $request->filled('username') 
            ? $request->username 
            : ($request->filled('name') ? $request->name : $user->username);

        $updateData = [
            'username'   => $username,
            'email'      => $request->email,
            'no_telepon' => $request->no_telepon ?? $user->no_telepon,
            'alamat'     => $request->alamat ?? $user->alamat,
        ];

        // Handle Avatar File Upload / Base64
        if ($request->hasFile('avatar')) {
            $avatarVal = Validator::make($request->all(), [
                'avatar' => \App\Support\MediaGuard::imageRules(false, 2048),
            ], [
                'avatar.image'     => 'Hanya file foto (JPG, PNG, WEBP) maksimal 2MB yang diperbolehkan.',
                'avatar.mimes'     => 'Hanya file foto (JPG, PNG, WEBP) maksimal 2MB yang diperbolehkan.',
                'avatar.mimetypes' => 'Hanya file foto (JPG, PNG, WEBP) maksimal 2MB yang diperbolehkan.',
                'avatar.max'       => 'Ukuran foto profil maksimal 2MB.',
            ]);
            if ($avatarVal->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $avatarVal->errors()->first('avatar'),
                ], 422);
            }
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $updateData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->filled('avatar_base64')) {
            $check = \App\Support\MediaGuard::validateBase64Image((string) $request->input('avatar_base64'), 2 * 1024 * 1024);
            if (!$check['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $check['message'],
                ], 422);
            }
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $filename = 'avatars/' . \Illuminate\Support\Str::random(40) . '.' . $check['ext'];
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $check['binary']);
            $updateData['avatar'] = $filename;
        } elseif ($request->boolean('remove_avatar')) {
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $updateData['avatar'] = null;
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user'    => $user->fresh()->load('role'),
        ], 200);
    }

    /**
     * PUT /api/user/password
     * Ganti password pengguna.
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password'      => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini salah.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui',
        ], 200);
    }

    /**
     * DELETE /api/user
     * Hapus akun pengguna secara permanen.
     */
    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Password konfirmasi wajib diisi',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah. Akun tidak dapat dihapus.',
            ], 422);
        }

        // Hapus semua token
        $user->tokens()->delete();

        // Hapus akun user
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Akun Anda berhasil dihapus permanen',
        ], 200);
    }

    /**
     * POST /api/logout
     * Logout & hapus token aktif.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ], 200);
    }
}
