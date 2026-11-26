<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Notifications\ResetPasswordMobileNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        $user = $request->user()->load('role');
        $payload = $user->toArray();
        $payload['name'] = $user->username;
        return response()->json($payload);
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
            'no_telepon'            => ['nullable', 'string', 'max:20', 'regex:/^(\+62|62|0)[0-9]{8,14}$/'],
            'password'              => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'confirmed'],
        ], [
            'no_telepon.regex'   => 'Nomor telepon harus berupa angka yang valid (contoh: 081234567890).',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.regex'     => 'Kata sandi harus mengandung minimal 1 huruf besar (A-Z) dan 1 angka (0-9).',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
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

        if (!$user || empty($user->password) || !Hash::check($request->password, $user->password)) {
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
     * Wajib menyertakan token terverifikasi (id_token / access_token Google, atau firebase_id_token).
     */
    public function googleAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token'          => 'nullable|string|max:4096',
            'access_token'      => 'nullable|string|max:4096',
            'firebase_id_token' => 'nullable|string|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Token Google tidak valid.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $verified = $this->verifyGoogleIdentity(
            $request->input('id_token'),
            $request->input('access_token'),
            $request->input('firebase_id_token')
        );

        if (!$verified || empty($verified['email'])) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi token Google gagal atau sudah kedaluwarsa. Silakan login ulang.',
            ], 401);
        }

        $email    = strtolower(trim($verified['email']));
        $googleId = $verified['sub'] ?? null;
        $name     = $verified['name'] ?? null;
        $avatar   = $verified['picture'] ?? null;

        $user = User::where('email', $email)->first();

        if ($user) {
            // Lindungi akun admin agar tidak bisa di-link otomatis dari Google mobile kecuali sudah terdaftar google_id yang cocok
            if ($user->isAdmin() && (empty($user->google_id) || $user->google_id !== $googleId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun admin wajib login menggunakan email dan kata sandi.',
                ], 403);
            }

            $user->update([
                'google_id'         => $user->google_id ?: $googleId,
                'avatar'            => $user->avatar ?: $avatar,
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);
        } else {
            $defaultRoleId = Role::where('nama_role', 'pelanggan')->value('id')
                          ?? Role::where('nama_role', 'user')->value('id');

            if (!$defaultRoleId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konfigurasi role pelanggan tidak ditemukan.',
                ], 500);
            }

            $baseUsername = $name ?: explode('@', $email)[0];
            $username = Str::slug($baseUsername, '_');
            if (empty($username)) {
                $username = 'user_' . random_int(1000, 9999);
            }
            $cleanUsername = $username;
            $counter = 1;
            while (User::where('username', $cleanUsername)->exists()) {
                $cleanUsername = $username . '_' . $counter;
                $counter++;
            }

            $user = User::create([
                'username'          => $cleanUsername,
                'email'             => $email,
                'google_id'         => $googleId,
                'avatar'            => $avatar,
                'password'          => null,
                'no_telepon'        => null,
                'role_id'           => $defaultRoleId,
                'email_verified_at' => now(),
            ]);
        }

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
     * Verifikasi kriptografis token Google / Firebase agar email tidak bisa dipalsukan client.
     */
    private function verifyGoogleIdentity(?string $idToken, ?string $accessToken, ?string $firebaseToken): ?array
    {
        // 1. Verifikasi Google OAuth id_token via tokeninfo resmi Google
        if (!empty($idToken)) {
            try {
                $resp = Http::timeout(8)->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $idToken,
                ]);
                if ($resp->successful()) {
                    $data = $resp->json();
                    $emailVerified = ($data['email_verified'] ?? false) === true || ($data['email_verified'] ?? '') === 'true';
                    $exp = (int) ($data['exp'] ?? 0);
                    $expectedAud = config('services.google.client_id');
                    $audOk = empty($expectedAud)
                        || ($data['aud'] ?? '') === $expectedAud
                        || ($data['azp'] ?? '') === $expectedAud;

                    if ($emailVerified && $exp > time() && $audOk && !empty($data['email'])) {
                        return [
                            'email'   => $data['email'],
                            'sub'     => $data['sub'] ?? null,
                            'name'    => $data['name'] ?? null,
                            'picture' => $data['picture'] ?? null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Google id_token verification failed', ['error' => $e->getMessage()]);
            }
        }

        // 2. Verifikasi Google OAuth access_token via userinfo endpoint resmi Google
        if (!empty($accessToken)) {
            try {
                $resp = Http::timeout(8)->withToken($accessToken)
                    ->get('https://www.googleapis.com/oauth2/v3/userinfo');
                if ($resp->successful()) {
                    $data = $resp->json();
                    $emailVerified = ($data['email_verified'] ?? false) === true || ($data['email_verified'] ?? '') === 'true';
                    if ($emailVerified && !empty($data['email'])) {
                        return [
                            'email'   => $data['email'],
                            'sub'     => $data['sub'] ?? null,
                            'name'    => $data['name'] ?? null,
                            'picture' => $data['picture'] ?? null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Google access_token verification failed', ['error' => $e->getMessage()]);
            }
        }

        // 3. Verifikasi Firebase ID Token (RS256) langsung terhadap sertifikat publik x509 Google
        if (!empty($firebaseToken)) {
            return $this->verifyFirebaseJwt($firebaseToken);
        }

        return null;
    }

    /**
     * Verifikasi signature RS256 dan klaim Firebase ID Token terhadap sertifikat publik Google.
     */
    private function verifyFirebaseJwt(string $jwt): ?array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null;
        }

        $b64Decode = static function (string $input): string {
            $rem = strlen($input) % 4;
            if ($rem > 0) {
                $input .= str_repeat('=', 4 - $rem);
            }
            return (string) base64_decode(strtr($input, '-_', '+/'), true);
        };

        $header = json_decode($b64Decode($parts[0]), true);
        $payload = json_decode($b64Decode($parts[1]), true);
        $signature = $b64Decode($parts[2]);

        if (!is_array($header) || !is_array($payload) || ($header['alg'] ?? '') !== 'RS256' || empty($header['kid'])) {
            return null;
        }

        $projectId = (string) env('FIREBASE_PROJECT_ID', 'ecotahu-ed1e0');
        $now = time();
        if (
            ($payload['aud'] ?? '') !== $projectId
            || ($payload['iss'] ?? '') !== "https://securetoken.google.com/{$projectId}"
            || empty($payload['sub'])
            || (int) ($payload['exp'] ?? 0) <= $now
            || (int) ($payload['iat'] ?? 0) > $now + 60
            || empty($payload['email'])
            || ($payload['email_verified'] ?? false) !== true
        ) {
            return null;
        }

        try {
            $certs = Cache::remember('firebase_x509_certs', 3600, function () {
                $res = Http::timeout(8)->get('https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com');
                return $res->successful() ? $res->json() : [];
            });

            $certPem = $certs[$header['kid']] ?? null;
            if (!$certPem) {
                return null;
            }

            $pubKey = openssl_pkey_get_public($certPem);
            if ($pubKey === false) {
                return null;
            }

            $valid = openssl_verify($parts[0] . '.' . $parts[1], $signature, $pubKey, OPENSSL_ALGO_SHA256);
            if ($valid !== 1) {
                return null;
            }

            return [
                'email'   => $payload['email'],
                'sub'     => $payload['sub'],
                'name'    => $payload['name'] ?? null,
                'picture' => $payload['picture'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::warning('Firebase JWT verification failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * POST /api/forgot-password
     * Kirim link & token reset password ke email (TIDAK pernah mengembalikan token ke response JSON).
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

        $genericMessage = 'Jika email terdaftar dalam sistem EcoTahu, link dan kode reset password telah dikirim ke inbox email Anda.';

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => $genericMessage,
            ], 200);
        }

        try {
            $token = Password::createToken($user);
            $user->notify(new ResetPasswordMobileNotification($token));
        } catch (\Throwable $e) {
            Log::error('Failed to send mobile password reset email', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'message' => $genericMessage,
        ], 200);
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

                // Cabut semua token lama setelah reset password
                $user->tokens()->delete();

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
            'no_telepon' => ['nullable', 'string', 'max:20', 'regex:/^(\+62|62|0)[0-9]{8,14}$/'],
            'alamat'     => 'nullable|string|max:255',
        ], [
            'no_telepon.regex' => 'Nomor telepon harus berupa angka yang valid (contoh: 081234567890).',
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
            'password'              => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'confirmed'],
        ], [
            'password.min'       => 'Kata sandi baru minimal 8 karakter.',
            'password.regex'     => 'Kata sandi baru harus mengandung minimal 1 huruf besar (A-Z) dan 1 angka (0-9).',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (empty($user->password) || !Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini salah.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Cabut semua token perangkat lain (sisakan token sesi aktif saat ini)
        $currentTokenId = $request->user()->currentAccessToken()?->id;
        $user->tokens()->when($currentTokenId, fn ($q) => $q->where('id', '!=', $currentTokenId))->delete();

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

        if (empty($user->password) || !Hash::check($request->password, $user->password)) {
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
