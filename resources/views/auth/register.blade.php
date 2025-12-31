<x-guest-layout>
    <h1 class="text-2xl font-bold text-green-700 mb-6">
        Account aanmaken
    </h1>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
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

            <!-- Adres -->
            <div>
                <x-input-label for="address" value="Adres" />
                <x-text-input
                    id="address"
                    class="block mt-1 w-full"
                    type="text"
                    name="address"
                    :value="old('address')"
                    placeholder="Straat + huisnummer"
                />
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>

            <!-- Postcode + Plaats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="postcode" value="Postcode" />
                    <x-text-input
                        id="postcode"
                        class="block mt-1 w-full"
                        type="text"
                        name="postcode"
                        :value="old('postcode')"
                    />
                    <x-input-error :messages="$errors->get('postcode')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="city" value="Plaats" />
                    <x-text-input
                        id="city"
                        class="block mt-1 w-full"
                        type="text"
                        name="city"
                        :value="old('city')"
                    />
                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                </div>
            </div>

            <!-- Provincie -->
            <div>
                <x-input-label for="province" value="Provincie" />
                <select
                    id="province"
                    name="province"
                    class="block mt-1 w-full border-gray-300 focus:border-green-600 focus:ring-green-600 rounded-md shadow-sm"
                    required
                >
                    <option value="" disabled {{ old('province') ? '' : 'selected' }}>Kies je provincie</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" @selected(old('province') === $province)>
                            {{ $province }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('province')" class="mt-2" />
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
