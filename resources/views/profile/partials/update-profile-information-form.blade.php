<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profielinformatie') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Werk je profielgegevens en e-mailadres bij.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Naam')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Je e-mailadres is nog niet geverifieerd.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Klik hier om de verificatiemail opnieuw te versturen.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Er is een nieuwe verificatielink naar je e-mailadres verstuurd.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="__('Telefoon')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="address" :value="__('Adres')" />
            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->address)" />
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="postcode" :value="__('Postcode')" />
                <x-text-input id="postcode" name="postcode" type="text" class="mt-1 block w-full" :value="old('postcode', $user->postcode)" />
                <x-input-error class="mt-2" :messages="$errors->get('postcode')" />
            </div>

            <div>
                <x-input-label for="city" :value="__('Plaats')" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->city)" />
                <x-input-error class="mt-2" :messages="$errors->get('city')" />
            </div>
        </div>

        <div>
            <x-input-label for="province" :value="__('Provincie')" />
            <select
                id="province"
                name="province"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required
            >
                <option value="" disabled {{ old('province', $user->province) ? '' : 'selected' }}>{{ __('Kies je provincie') }}</option>
                @foreach($provinces as $province)
                    <option value="{{ $province }}" @selected(old('province', $user->province) === $province)>
                        {{ $province }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('province')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Opslaan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Opgeslagen.') }}</p>
            @endif
        </div>
    </form>
</section>
