<x-guest-layout>
    <div class="turbo-login-heading">
        <span class="turbo-login-heading__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M15 7a4 4 0 1 1-7.7 1.5A4 4 0 0 1 15 7Zm-10 14a7 7 0 0 1 14 0" stroke-width="1.8" stroke-linecap="round"/></svg>
        </span>
        <div>
            <p>Mijn account</p>
            <h1>Fijn je weer te zien</h1>
        </div>
    </div>

    <p class="turbo-login-subtitle">Log in met het e-mailadres van je account.</p>

    <x-auth-session-status class="turbo-login-status" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="turbo-login-fields" x-data="{ showPassword: false }">
        @csrf

        <div>
            <x-input-label for="email" value="E-mailadres" />
            <div class="turbo-login-input-wrap">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16v12H4zM4 7l8 6 8-6" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="naam@voorbeeld.nl" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="turbo-login-label-row">
                <x-input-label for="password" value="Wachtwoord" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Wachtwoord vergeten?</a>
                @endif
            </div>
            <div class="turbo-login-input-wrap">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.7"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke-width="1.7" stroke-linecap="round"/></svg>
                <x-text-input id="password" class="block w-full" x-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="Vul je wachtwoord in" />
                <button type="button" class="turbo-password-toggle" @click="showPassword = !showPassword" x-bind:aria-label="showPassword ? 'Wachtwoord verbergen' : 'Wachtwoord tonen'">
                    <svg x-show="!showPassword" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke-width="1.7"/><circle cx="12" cy="12" r="2.5" stroke-width="1.7"/></svg>
                    <svg x-cloak x-show="showPassword" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m4 4 16 16M10.6 6.1A9 9 0 0 1 12 6c6 0 9.5 6 9.5 6a16 16 0 0 1-2.1 2.8M6.2 6.3C3.8 8 2.5 12 2.5 12s3.5 6 9.5 6a9 9 0 0 0 3-.5" stroke-width="1.7" stroke-linecap="round"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="remember_me" class="turbo-login-remember">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Onthoud mij op dit apparaat</span>
            </label>
        </div>

        <div>
            <x-primary-button class="turbo-login-submit">
                Inloggen
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14m-5-5 5 5-5 5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </x-primary-button>
        </div>

        <p class="turbo-login-register">
            Nog geen account?
            <a href="{{ route('register') }}">Gratis account aanmaken</a>
        </p>
    </form>
</x-guest-layout>
