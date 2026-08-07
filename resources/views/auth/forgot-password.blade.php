<x-guest-layout>
    <div class="turbo-login-heading">
        <span class="turbo-login-heading__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.8"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 4v2" stroke-width="1.8" stroke-linecap="round"/></svg>
        </span>
        <div>
            <p>Account herstellen</p>
            <h1>Wachtwoord vergeten?</h1>
        </div>
    </div>

    <p class="turbo-login-subtitle">Geen probleem. Vul het e-mailadres van je account in en we sturen je een beveiligde link om een nieuw wachtwoord te kiezen.</p>

    <!-- Session Status -->
    <x-auth-session-status class="turbo-login-status" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="turbo-login-fields">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="E-mailadres" />
            <div class="turbo-login-input-wrap">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16v12H4zM4 7l8 6 8-6" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" placeholder="naam@voorbeeld.nl" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-primary-button class="turbo-login-submit">
                Verstuur herstellink
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14m-5-5 5 5-5 5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </x-primary-button>
        </div>

        <p class="turbo-login-register">
            Weet je je wachtwoord weer?
            <a href="{{ route('login') }}">Terug naar inloggen</a>
        </p>
    </form>
</x-guest-layout>
