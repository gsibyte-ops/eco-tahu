<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForbidAdminCustomerAction
{
    /**
     * Memblokir akun Admin agar bersifat Read-Only di aplikasi mobile
     * (tidak boleh checkout, cancel, upload bukti, review/komentar, like, atau hapus akun).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user) {
            $user->loadMissing('role');
            if ($user->isAdmin() || (int) $user->role_id === 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mode Pemantauan Admin (Read-Only): Akun Admin tidak dapat melakukan transaksi, like, ulasan, atau hapus akun di aplikasi mobile.',
                ], 403);
            }
        }

        return $next($request);
    }
}
