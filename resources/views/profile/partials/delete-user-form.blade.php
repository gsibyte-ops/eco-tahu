<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-gray-800">
            {{ __('Delete Account') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <div x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }">
        <button type="button"
                @click="open = true"
                class="px-5 py-2.5 bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-xl shadow-lg shadow-red-500/30 transition hover:-translate-y-0.5">
            {{ __('Delete Account') }}
        </button>

        {{-- MODAL --}}
        <div x-show="open" x-cloak
             @keydown.escape.window="open = false"
             class="fixed inset-0 z-[999] flex items-center justify-center p-4"
             style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(8px);">
            <div @click.away="open = false"
                 x-transition
                 class="glass-card w-full max-w-md shadow-2xl overflow-hidden">
                <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-4">
                    @csrf
                    @method('delete')

                    <h2 class="text-lg font-bold text-gray-800">
                        {{ __('Are you sure you want to delete your account?') }}
                    </h2>

                    <p class="text-sm text-gray-600 leading-relaxed">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>

                    <div>
                        <label for="password" class="sr-only">{{ __('Password') }}</label>
                        <input id="password" name="password" type="password"
                               class="glass-input w-full px-4 py-2.5 text-sm text-gray-800"
                               placeholder="{{ __('Password') }}" autocomplete="current-password">
                        @if ($errors->userDeletion->get('password'))
                            <p class="mt-2 text-xs text-red-600">{{ $errors->userDeletion->first('password') }}</p>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="open = false"
                                class="glass-btn px-5 py-2.5">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 bg-gradient-to-br from-red-500 to-red-600 text-white font-semibold rounded-xl shadow-lg shadow-red-500/30 transition hover:-translate-y-0.5">
                            {{ __('Delete Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>