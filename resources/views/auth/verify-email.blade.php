<x-guest-layout>
    <div class="turbo-verify">
        <div class="turbo-verify-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3.5 6.5h17v12h-17zM4 7l8 6 8-6" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="m15.5 16 1.6 1.6 3.4-3.7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>

        <p class="turbo-verify-eyebrow">Controleer je inbox</p>
        <h1>Bevestig je e-mailadres</h1>
        <p class="turbo-verify-copy">We hebben een e-mail met een bevestigingsknop gestuurd naar:</p>

        <div class="turbo-verify-address">
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16v12H4zM4 7l8 6 8-6" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <strong>{{ auth()->user()->email }}</strong>
        </div>

        <p class="turbo-verify-help">Klik op de knop in de e-mail om je account te activeren. Controleer ook je map met ongewenste e-mail als je niets ziet.</p>

        @if (session('status') == 'verification-link-sent')
            <div class="turbo-verify-success" role="status">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m5 12 4 4L19 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Een nieuwe verificatielink is verzonden. Controleer je inbox.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" x-data="{ sending: false }" @submit="sending = true">
            @csrf
            <x-primary-button class="turbo-login-submit" x-bind:disabled="sending" x-bind:class="{ 'opacity-70 cursor-wait': sending }">
                <span x-show="!sending">Verificatiemail opnieuw versturen</span>
                <span x-cloak x-show="sending">Even geduld…</span>
                <svg x-show="!sending" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 11a8 8 0 1 0-2.3 5.7M20 5v6h-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <svg x-cloak x-show="sending" class="turbo-button-spinner" aria-hidden="true" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity=".25" stroke-width="3"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
            </x-primary-button>
        </form>

        <div class="turbo-verify-divider"><span>of</span></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="turbo-verify-logout">Uitloggen en een ander account gebruiken</button>
        </form>
    </div>
</x-guest-layout>
