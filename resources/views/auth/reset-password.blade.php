<x-guest-layout>
    <div class="turbo-login-heading">
        <span class="turbo-login-heading__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M15.5 8.5a4.95 4.95 0 0 0-9.3 1.8L3 13.5 5.5 16l3.2-3.2a5 5 0 0 0 6.8-4.3Z" stroke-width="1.8" stroke-linejoin="round"/><circle cx="14.5" cy="7.5" r="1" fill="currentColor" stroke="none"/><path d="m5.5 11 2 2" stroke-width="1.8"/></svg>
        </span>
        <div>
            <p>Account herstellen</p>
            <h1>Kies een nieuw wachtwoord</h1>
        </div>
    </div>

    <p class="turbo-login-subtitle">Je bent bijna klaar. Kies hieronder een sterk wachtwoord dat je niet voor andere accounts gebruikt.</p>

    <form method="POST" action="{{ route('password.store') }}" class="turbo-login-fields" x-data="{ showPassword: false, showConfirmation: false }">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="E-mailadres" />
            <div class="turbo-login-input-wrap">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16v12H4zM4 7l8 6 8-6" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Nieuw wachtwoord" />
            <div class="turbo-login-input-wrap">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.7"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke-width="1.7" stroke-linecap="round"/></svg>
                <x-text-input id="password" class="block w-full" x-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="Minimaal 8 tekens" />
                <button type="button" class="turbo-password-toggle" @click="showPassword = !showPassword" x-bind:aria-label="showPassword ? 'Wachtwoord verbergen' : 'Wachtwoord tonen'">Tonen</button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Herhaal nieuw wachtwoord" />
            <div class="turbo-login-input-wrap">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.7"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke-width="1.7" stroke-linecap="round"/></svg>
                <x-text-input id="password_confirmation" class="block w-full" x-bind:type="showConfirmation ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="Herhaal je nieuwe wachtwoord" />
                <button type="button" class="turbo-password-toggle" @click="showConfirmation = !showConfirmation" x-bind:aria-label="showConfirmation ? 'Wachtwoord verbergen' : 'Wachtwoord tonen'">Tonen</button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <p class="turbo-reset-hint">
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 8v4m0 4h.01M4 19h16L12 4 4 19Z" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Gebruik minimaal 8 tekens en combineer bij voorkeur letters, cijfers en symbolen.
        </p>

        <x-primary-button class="turbo-login-submit">
            Wachtwoord wijzigen
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14m-5-5 5 5-5 5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </x-primary-button>

        <p class="turbo-login-register"><a href="{{ route('login') }}">Terug naar inloggen</a></p>
    </form>
</x-guest-layout>
