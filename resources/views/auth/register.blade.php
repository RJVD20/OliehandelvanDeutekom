<x-guest-layout>
    <h1 class="text-2xl font-bold text-green-700 mb-6">
        Account aanmaken
    </h1>

        <form
            method="POST"
            action="{{ route('register') }}"
            class="space-y-4"
            x-data="{
                postcode: '{{ old('postcode') }}',
                huisnummer: '{{ old('huisnummer') }}',
                straat: '{{ old('straat') }}',
                stad: '{{ old('city') }}',
                provincie: '{{ old('province') }}',
                loading: false,
                fout: null,
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
                        this.fout = 'Verbindingsfout, vul handmatig in';
                    } finally {
                        this.loading = false;
                    }
                }
            }"
            @submit="if (huisnummer && straat) $el.querySelector('[name=address]').value = straat + ' ' + huisnummer"
        >
            @csrf

            <!-- Naam -->
            <div>
                <x-input-label for="name" value="Naam" />
                <x-text-input
                    id="name"
                    class="block mt-1 w-full"
                    type="text"
                    name="name"
                    :value="old('name')"
                    required
                    autofocus
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

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
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Telefoon -->
            <div>
                <x-input-label for="phone" value="Telefoonnummer" />
                <x-text-input
                    id="phone"
                    class="block mt-1 w-full"
                    type="text"
                    name="phone"
                    :value="old('phone')"
                    required
                />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            {{-- Verborgen address veld: wordt gevuld bij submit --}}
            <input type="hidden" name="address" value="{{ old('address') }}">

            <!-- Postcode + Huisnummer -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="postcode" value="Postcode" />
                    <input
                        id="postcode"
                        name="postcode"
                        x-model="postcode"
                        placeholder="1234 AB"
                        required
                        class="block mt-1 w-full border-gray-300 focus:border-green-600 focus:ring-green-600 rounded-md shadow-sm"
                    >
                    <x-input-error :messages="$errors->get('postcode')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="huisnummer" value="Huisnummer" />
                    <input
                        id="huisnummer"
                        name="huisnummer"
                        x-model="huisnummer"
                        placeholder="10"
                        required
                        @blur="lookup()"
                        class="block mt-1 w-full border-gray-300 focus:border-green-600 focus:ring-green-600 rounded-md shadow-sm"
                    >
                    <p x-show="fout" x-text="fout" class="mt-1 text-xs text-red-600"></p>
                </div>
            </div>

            <!-- Straat (auto-ingevuld) -->
            <div>
                <x-input-label for="straat" value="Straat" />
                <input
                    id="straat"
                    name="straat"
                    x-model="straat"
                    :placeholder="loading ? '...' : 'Wordt automatisch ingevuld'"
                    required
                    class="block mt-1 w-full border-gray-300 focus:border-green-600 focus:ring-green-600 rounded-md shadow-sm"
                >
            </div>

            <!-- Plaats + Provincie -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="city" value="Plaats" />
                    <input
                        id="city"
                        name="city"
                        x-model="stad"
                        :placeholder="loading ? '...' : 'Wordt automatisch ingevuld'"
                        required
                        class="block mt-1 w-full border-gray-300 focus:border-green-600 focus:ring-green-600 rounded-md shadow-sm"
                    >
                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="province" value="Provincie" />
                    <select
                        id="province"
                        name="province"
                        x-model="provincie"
                        required
                        class="block mt-1 w-full border-gray-300 focus:border-green-600 focus:ring-green-600 rounded-md shadow-sm"
                    >
                        <option value="">Kies je provincie</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}">{{ $province }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('province')" class="mt-2" />
                </div>
            </div>

            <!-- Wachtwoord -->
            <div>
                <x-input-label for="password" value="Wachtwoord" />
                <x-text-input
                    id="password"
                    class="block mt-1 w-full"
                    type="password"
                    name="password"
                    required
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Bevestig wachtwoord -->
            <div>
                <x-input-label for="password_confirmation" value="Bevestig wachtwoord" />
                <x-text-input
                    id="password_confirmation"
                    class="block mt-1 w-full"
                    type="password"
                    name="password_confirmation"
                    required
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4">
                <a
                    href="{{ route('login') }}"
                    class="text-sm text-gray-600 hover:text-green-700"
                >
                    Al een account?
                </a>

                <x-primary-button>
                    Registreren
                </x-primary-button>
            </div>
        </form>
</x-guest-layout>
