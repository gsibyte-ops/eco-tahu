<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('Google OAuth web login failed', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Login Google gagal atau dibatalkan. Silakan coba lagi.');
        }

        $email = $googleUser->getEmail();
        if (!$email) {
            return redirect()->route('login')->with('error', 'Email Google tidak tersedia.');
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            if ($user->isAdmin() && (empty($user->google_id) || $user->google_id !== $googleUser->getId())) {
                return redirect()->route('login')->with('error', 'Akun admin wajib login menggunakan email dan kata sandi.');
            }

            // Update google_id & avatar kalau belum ada
            $user->update([
                'google_id' => $user->google_id ?: $googleUser->getId(),
                'avatar'    => $user->avatar ?: $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);
        } else {
            // Buat user baru — WAJIB role pelanggan (jangan pernah fallback ke ID 1 / admin)
            $defaultRoleId = Role::where('nama_role', 'pelanggan')->value('id')
                          ?? Role::where('nama_role', 'user')->value('id');

            if (!$defaultRoleId) {
                Log::error('Google OAuth failed: pelanggan role missing in roles table');
                return redirect()->route('login')->with('error', 'Registrasi sedang tidak tersedia. Hubungi admin.');
            }

            $user = User::create([
                'username'          => $googleUser->getName() ?: 'Google User',
                'email'             => $email,
                'google_id'         => $googleUser->getId(),
                'avatar'            => $googleUser->getAvatar(),
                'password'          => null,
                'no_telepon'        => null,
                'role_id'           => $defaultRoleId,
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        // Redirect ke dashboard kalau admin, home kalau user
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('home'));
    }
}