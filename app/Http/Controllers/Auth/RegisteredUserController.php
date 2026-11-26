<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'no_telepon' => ['nullable', 'string', 'max:20', 'regex:/^(\+62|62|0)[0-9]{8,14}$/'],
            'password' => ['required', 'confirmed', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
        ], [
            'no_telepon.regex' => 'Nomor telepon harus berupa angka yang valid (contoh: 081234567890).',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus mengandung minimal 1 huruf besar (A-Z) dan 1 angka (0-9).',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'role_id' => 2, // 2 = pelanggan
            'username' => $request->name,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Selamat datang di EcoTahu, ' . $user->username . '!');
    }
}