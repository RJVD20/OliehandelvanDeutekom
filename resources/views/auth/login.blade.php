<x-guest-layout>

    <h1 class="text-2xl font-bold text-green-700 mb-2">
        Inloggen
    </h1>

    <p class="text-sm text-gray-600 mb-6">
        Log in om je bestellingen te bekijken of sneller af te rekenen.
    </p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" value="E-mailadres" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Wachtwoord" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-600">
                    Onthoud mij
                </span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between mt-6">

            <div class="text-sm">
                @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="text-gray-600 hover:text-green-700 underline"
                    >
                        Wachtwoord vergeten?
                    </a>
                @endif
            </div>

            <x-primary-button>
                Log in
            </x-primary-button>
        </div>

        <!-- Register link -->
        <div class="mt-6 text-center">
            <a
                href="{{ route('register') }}"
                class="text-sm text-gray-600 hover:text-green-700"
            >
                Nog geen account? <strong>Account aanmaken</strong>
            </a>
        </div>
    </form>

</x-guest-layout>
