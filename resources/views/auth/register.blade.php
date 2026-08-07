<x-guest-layout>
    <div class="turbo-login-heading">
        <span class="turbo-login-heading__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M15 7a4 4 0 1 1-7.3 2.3A4 4 0 0 1 15 7Zm-10 14a7 7 0 0 1 14 0m1-11v6m-3-3h6" stroke-width="1.8" stroke-linecap="round"/></svg>
        </span>
        <div>
            <p>Mijn account</p>
            <h1>Account aanmaken</h1>
        </div>
    </div>

    <p class="turbo-login-subtitle">Vul je gegevens in. Na registratie sturen we je een e-mail om je account veilig te bevestigen.</p>

    <form
        method="POST"
        action="{{ route('register') }}"
        class="turbo-register-form"
        x-data="{
            postcode: '{{ old('postcode') }}',
            huisnummer: '{{ old('huisnummer') }}',
            straat: '{{ old('straat') }}',
            stad: '{{ old('city') }}',
            provincie: '{{ old('province') }}',
            loading: false,
            fout: null,
            showPassword: false,
            showConfirmation: false,
            async lookup() {
                if (!this.postcode || !this.huisnummer) return;
                this.loading = true;
                this.fout = null;
                try {
                    const params = new URLSearchParams({ postcode: this.postcode, huisnummer: this.huisnummer });
                    const res = await fetch('/api/postcode-lookup?' + params);
                    const data = await res.json();
                    if (!res.ok) {
                        this.fout = data.message || 'Postcode niet gevonden';
                    } else {
                        this.straat = data.straat;
                        this.stad = data.stad;
                        this.provincie = data.provincie;
                    }
                } catch (e) {
                    this.fout = 'Verbindingsfout, vul je adres handmatig in';
                } finally {
                    this.loading = false;
                }
            }
        }"
        @submit="if (huisnummer && straat) $el.querySelector('[name=address]').value = straat + ' ' + huisnummer"
    >
        @csrf

        <section class="turbo-register-section">
            <div class="turbo-register-section__heading">
                <span>1</span>
                <div><h2>Persoonlijke gegevens</h2><p>Hiermee houden we je op de hoogte van je bestelling.</p></div>
            </div>
            <div class="turbo-register-grid">
                <div class="turbo-register-grid__wide">
                    <x-input-label for="name" value="Naam" />
                    <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Voor- en achternaam" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" value="E-mailadres" />
                    <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autocomplete="email" placeholder="naam@voorbeeld.nl" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="phone" value="Telefoonnummer" />
                    <x-text-input id="phone" class="block w-full" type="tel" name="phone" :value="old('phone')" required autocomplete="tel" placeholder="06 12 34 56 78" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
            </div>
        </section>

        <input type="hidden" name="address" value="{{ old('address') }}">

        <section class="turbo-register-section">
            <div class="turbo-register-section__heading">
                <span>2</span>
                <div><h2>Adresgegevens</h2><p>Postcode en huisnummer vullen je adres automatisch aan.</p></div>
            </div>
            <div class="turbo-register-address-grid">
                <div>
                    <x-input-label for="postcode" value="Postcode" />
                    <input id="postcode" name="postcode" x-model="postcode" placeholder="1234 AB" required autocomplete="postal-code" @blur="lookup()">
                    <x-input-error :messages="$errors->get('postcode')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="huisnummer" value="Huisnummer" />
                    <input id="huisnummer" name="huisnummer" x-model="huisnummer" placeholder="10" required @blur="lookup()">
                    <p x-show="fout" x-text="fout" class="mt-1 text-xs text-red-600"></p>
                </div>
                <div class="turbo-register-address-grid__wide">
                    <x-input-label for="straat" value="Straat" />
                    <input id="straat" name="straat" x-model="straat" :placeholder="loading ? 'Adres ophalen…' : 'Straatnaam'" required autocomplete="street-address">
                </div>
                <div>
                    <x-input-label for="city" value="Plaats" />
                    <input id="city" name="city" x-model="stad" :placeholder="loading ? 'Adres ophalen…' : 'Plaats'" required autocomplete="address-level2">
                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="province" value="Provincie" />
                    <select id="province" name="province" x-model="provincie" required autocomplete="address-level1">
                        <option value="">Kies je provincie</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}">{{ $province }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('province')" class="mt-2" />
                </div>
            </div>
        </section>

        <section class="turbo-register-section">
            <div class="turbo-register-section__heading">
                <span>3</span>
                <div><h2>Kies een wachtwoord</h2><p>Gebruik een uniek wachtwoord voor een veilig account.</p></div>
            </div>
            <div class="turbo-register-grid">
                <div>
                    <x-input-label for="password" value="Wachtwoord" />
                    <div class="turbo-register-password">
                        <x-text-input id="password" class="block w-full" x-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="Minimaal 8 tekens" />
                        <button type="button" @click="showPassword = !showPassword" x-bind:aria-label="showPassword ? 'Wachtwoord verbergen' : 'Wachtwoord tonen'">Tonen</button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="password_confirmation" value="Herhaal wachtwoord" />
                    <div class="turbo-register-password">
                        <x-text-input id="password_confirmation" class="block w-full" x-bind:type="showConfirmation ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="Herhaal je wachtwoord" />
                        <button type="button" @click="showConfirmation = !showConfirmation" x-bind:aria-label="showConfirmation ? 'Wachtwoord verbergen' : 'Wachtwoord tonen'">Tonen</button>
                    </div>
                </div>
            </div>
        </section>

        <x-primary-button class="turbo-login-submit">
            Account aanmaken
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14m-5-5 5 5-5 5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </x-primary-button>

        <p class="turbo-login-register">Al een account? <a href="{{ route('login') }}">Log hier in</a></p>
    </form>
</x-guest-layout>
