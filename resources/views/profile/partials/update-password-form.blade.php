<section>
    <header>
        <h2 class="text-lg font-bold text-gray-800">
            {{ __('Update Password') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password"
                   class="glass-input w-full px-4 py-2.5 text-sm text-gray-800"
                   autocomplete="current-password">
            @if ($errors->updatePassword->get('current_password'))
                <p class="mt-2 text-xs text-red-600">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password"
                   class="glass-input w-full px-4 py-2.5 text-sm text-gray-800"
                   autocomplete="new-password">
            @if ($errors->updatePassword->get('password'))
                <p class="mt-2 text-xs text-red-600">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   class="glass-input w-full px-4 py-2.5 text-sm text-gray-800"
                   autocomplete="new-password">
            @if ($errors->updatePassword->get('password_confirmation'))
                <p class="mt-2 text-xs text-red-600">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="glass-btn-primary">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-emerald-600 font-medium">
                    ✓ {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>