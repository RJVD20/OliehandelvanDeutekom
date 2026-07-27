<div
    class="turbo-nav"
    x-data="{ open: false, userOpen: false, searchOpen: false }"
    x-effect="document.body.classList.toggle('overflow-hidden', open || searchOpen)"
    @keydown.escape.window="searchOpen = false; open = false"
>
<nav class="sticky top-0 z-[12000]">
    <div class="turbo-nav__utility hidden lg:block text-[13px] border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-center gap-4 py-1.5">
                <div class="flex items-center gap-2 px-3 py-1 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-turbo-gold-light" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 1 0 0-4h14a2 2 0 1 0 0 4M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8" />
                    </svg>
                    <span class="font-semibold">Gratis geleverd vanaf 5 jerrycans</span>
                </div>
                <div class="flex items-center gap-2 px-3 py-1 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-turbo-gold-light" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    <span class="font-semibold">Bezorging binnen 4 tot 8 werkdagen</span>
                </div>
                <div class="flex items-center gap-2 px-3 py-1 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-turbo-gold-light" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />
                    </svg>
                    <span class="font-semibold">Specialist in kachels en kachelvloeistof</span>
                </div>
            </div>
        </div>
    </div>

    <div class="turbo-nav__header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <!-- Mobile bar -->
            <div class="lg:hidden relative h-14 flex items-center justify-between">
                <button
                    class="turbo-icon-button inline-flex h-9 w-9 items-center justify-center rounded-xl focus:outline-none"
                    @click="open = !open"
                    aria-label="Open menu"
                    :aria-expanded="open"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- Previous logo block with white background kept for quick rollback
                <a href="/" class="absolute left-1/2 -translate-x-1/2 inline-flex items-center justify-center px-1.5 py-1 bg-white rounded-lg shadow-md border border-white/60">
                    <img src="/images/logovd.png" alt="Logo" class="h-6 w-auto object-contain">
                </a>
                --}}
                <a href="/" class="absolute left-1/2 -translate-x-1/2 inline-flex items-center justify-center">
                    <img src="/images/logo-kachels-vloeistoffen.webp" alt="Kachels & Vloeistoffen" class="h-24 w-auto max-w-none translate-y-1 object-contain">
                </a>

                <div class="ml-auto flex items-center gap-1">
                    <button
                        type="button"
                        class="turbo-icon-button inline-flex h-9 w-9 items-center justify-center rounded-xl"
                        aria-label="Zoek producten"
                        @click="searchOpen = true; $nextTick(() => document.getElementById('nav-search-input')?.focus())"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-4.35-4.35M11 6a5 5 0 1 0 0 10 5 5 0 0 0 0-10Z" />
                        </svg>
                    </button>

                    @auth
                        <a href="{{ route('account.dashboard') }}" class="turbo-icon-button inline-flex h-9 w-9 items-center justify-center rounded-xl" aria-label="Account">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.5 20.5a6.5 6.5 0 0 1 13 0M12 12.5a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="turbo-icon-button inline-flex h-9 w-9 items-center justify-center rounded-xl" aria-label="Inloggen">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.5 20.5a6.5 6.5 0 0 1 13 0M12 12.5a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                            </svg>
                        </a>
                    @endauth

                    <a href="{{ route('cart.index') }}" class="turbo-icon-button relative inline-flex h-9 w-9 items-center justify-center rounded-xl" aria-label="Winkelwagen">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h2l1 14h12l1-10H6" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11H7" />
                            <circle cx="9" cy="20" r="1.2" />
                            <circle cx="17" cy="20" r="1.2" />
                        </svg>
                        <span
                            x-data="{ count: {{ collect(session('cart', []))->sum('quantity') }} }"
                            @cart-updated.window="count = $event.detail"
                            x-show="count > 0"
                            x-text="count"
                            class="turbo-cart-badge absolute -top-1.5 -right-1.5 text-[10px] font-semibold rounded-full px-1.5 py-0.5"
                            style="display: none;"
                        ></span>
                    </a>
                </div>
            </div>

            <!-- Desktop bar -->
            <div class="hidden lg:flex items-center justify-between min-h-28 gap-4 py-2">
                <div class="flex shrink-0 items-center gap-3">
                    {{-- Previous desktop logo block with white background retained for quick rollback
                    <a href="/" class="relative block">
                        <span class="absolute inset-0 -z-10 -translate-y-3">
                            <span class="block h-full w-full bg-white"></span>
                        </span>
                        <span class="relative block bg-white px-4 py-3 shadow-md">
                            <img src="/images/logovd.png" alt="Logo" class="h-10 w-auto">
                        </span>
                    </a>
                    --}}
                    <a href="/" class="relative block">
                        <span class="relative block">
                            <img src="/images/logo-kachels-vloeistoffen.webp" alt="Kachels & Vloeistoffen" class="h-20 w-auto max-w-[12rem] origin-left scale-[1.4375] xl:h-24 xl:max-w-[14rem] object-contain">
                        </span>
                    </a>
                </div>

                <div class="hidden lg:flex flex-1 justify-center items-center space-x-7 text-sm font-semibold">
                    <a href="/" class="turbo-nav-link {{ request()->routeIs('home') ? 'turbo-nav-link--active' : '' }}">Home</a>
                    <a href="{{ route('products.heaters') }}" class="turbo-nav-link {{ request()->routeIs('products.heaters') ? 'turbo-nav-link--active' : '' }}">Kachels</a>
                    <a href="{{ route('products.liquids') }}" class="turbo-nav-link {{ request()->routeIs('products.liquids') ? 'turbo-nav-link--active' : '' }}">Vloeistoffen</a>
                    <a href="{{ route('products.index') }}" class="turbo-nav-link {{ request()->routeIs('products.index') ? 'turbo-nav-link--active' : '' }}">Overige producten</a>
                    <a href="{{ route('locaties') }}" class="turbo-nav-link {{ request()->routeIs('locaties') ? 'turbo-nav-link--active' : '' }}">Locaties</a>
                    <a href="{{ route('over-ons') }}" class="turbo-nav-link {{ request()->routeIs('over-ons') ? 'turbo-nav-link--active' : '' }}">Over ons</a>
                </div>

                <div class="hidden lg:flex items-center gap-6">
                    <button
                        type="button"
                        class="hover:text-white"
                        aria-label="Zoek producten"
                        @click="searchOpen = true; $nextTick(() => document.getElementById('nav-search-input')?.focus())"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-4.35-4.35M11 6a5 5 0 1 0 0 10 5 5 0 0 0 0-10Z" />
                        </svg>
                    </button>

                    @auth
                        <div class="relative" x-data="{ openUser: false }">
                            <button @click="openUser = !openUser" class="hover:text-white flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.5 20.5a6.5 6.5 0 0 1 13 0M12 12.5a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                                </svg>
                                <span class="text-sm">{{ auth()->user()->name }}</span>
                            </button>
                            <div
                                x-show="openUser"
                                @click.outside="openUser = false"
                                x-transition
                                class="absolute right-0 mt-2 w-44 bg-white text-neutral-800 border border-neutral-200 rounded-lg shadow-lg z-50"
                                style="display: none;"
                            >
                                @if(auth()->user()->is_admin)
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-neutral-100">Admin paneel</a>
                                @endif
                                <a href="{{ route('account.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-neutral-100">Account</a>
                                <a href="{{ route('account.orders') }}" class="block px-4 py-2 text-sm hover:bg-neutral-100">Bestellingen</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-neutral-100">Uitloggen</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.5 20.5a6.5 6.5 0 0 1 13 0M12 12.5a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                            </svg>
                            <span class="text-sm">Inloggen</span>
                        </a>
                    @endauth

                    <div
                        x-data="{ count: {{ collect(session('cart', []))->sum('quantity') }} }"
                        @cart-updated.window="count = $event.detail"
                        class="relative"
                    >
                        <a href="{{ route('cart.index') }}" class="hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h2l1 14h12l1-10H6" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11H7" />
                                <circle cx="9" cy="20" r="1.2" />
                                <circle cx="17" cy="20" r="1.2" />
                            </svg>
                        </a>
                        <template x-if="count > 0">
                            <span
                                x-text="count"
                                class="turbo-cart-badge absolute -top-2 -right-2 text-xs font-semibold rounded-full px-1.5 py-0.5"
                            ></span>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div
        class="turbo-mobile-menu lg:hidden fixed inset-0 z-[9999] bg-turbo-navy/70 backdrop-blur-sm"
        x-show="open"
        x-transition.opacity
        @click.self="open = false"
        @keydown.escape.window="open = false"
        style="display:none;"
    >
        <div
            class="turbo-mobile-menu__panel absolute inset-y-0 left-0 w-[min(88vw,22rem)] overflow-y-auto text-white/90 shadow-2xl"
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
        >
            <div class="px-5 py-5 flex items-center justify-between border-b border-white/10">
                <a href="/" class="flex min-w-0 items-center gap-3" @click="open = false">
                    <span class="inline-flex h-12 w-24 items-center justify-center overflow-hidden rounded-lg bg-white p-1">
                        <img src="/images/logo-kachels-vloeistoffen.webp" alt="" class="h-full w-full object-contain">
                    </span>
                    <span>
                        <span class="block text-[11px] font-bold uppercase tracking-[0.16em] text-turbo-gold-light">Navigatie</span>
                        <span class="block text-sm font-semibold text-white">Kachels & Vloeistoffen</span>
                    </span>
                </a>
                <button @click="open = false" aria-label="Menu sluiten" class="ml-3 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-white/80 hover:bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-4 py-5 grid gap-2 text-sm">
                <a href="/" @if(request()->routeIs('home')) aria-current="page" @endif class="turbo-mobile-link"><span>Home</span><span aria-hidden="true">›</span></a>
                <a href="{{ route('products.heaters') }}" @if(request()->routeIs('products.heaters')) aria-current="page" @endif class="turbo-mobile-link"><span>Kachels</span><span aria-hidden="true">›</span></a>
                <a href="{{ route('products.liquids') }}" @if(request()->routeIs('products.liquids')) aria-current="page" @endif class="turbo-mobile-link"><span>Vloeistoffen</span><span aria-hidden="true">›</span></a>
                <a href="{{ route('products.index') }}" @if(request()->routeIs('products.index')) aria-current="page" @endif class="turbo-mobile-link"><span>Overige producten</span><span aria-hidden="true">›</span></a>
                <a href="{{ route('locaties') }}" @if(request()->routeIs('locaties')) aria-current="page" @endif class="turbo-mobile-link"><span>Locaties</span><span aria-hidden="true">›</span></a>
                <a href="{{ route('over-ons') }}" @if(request()->routeIs('over-ons')) aria-current="page" @endif class="turbo-mobile-link"><span>Over ons</span><span aria-hidden="true">›</span></a>

                <div class="mt-2 border-t border-white/10 pt-4 space-y-2">
                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="turbo-mobile-link">Admin paneel</a>
                        @endif
                        <a href="{{ route('account.dashboard') }}" class="turbo-mobile-link">Account</a>
                        <a href="{{ route('account.orders') }}" class="turbo-mobile-link">Bestellingen</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left rounded-xl bg-rose-600 px-4 py-3 font-semibold hover:bg-rose-500">Uitloggen</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="turbo-mobile-link justify-center border-turbo-gold/40 text-turbo-gold-light">Inloggen</a>
                    @endauth
                </div>
            </div>

            <div class="mx-4 mb-5 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-xs leading-5 text-white/65">
                Gratis geleverd vanaf 5 jerrycans
            </div>
        </div>
    </div>
</nav>

<!-- Search overlay -->
<div
    x-show="searchOpen"
    x-transition.opacity
    class="fixed inset-0 z-50 flex justify-center px-4 sm:px-6"
    style="display: none;"
>
    <div class="absolute inset-0 bg-black/40" @click="searchOpen = false"></div>

    <div
        class="relative w-full max-w-5xl mt-24 md:mt-28"
        x-data="{
            query: {{ json_encode(request('q', '')) }},
            loading: false,
            timer: null,
            minChars: 2,
            results: { products: [], categories: [] },
            baseSearch: {{ json_encode(route('products.index')) }},
            baseCategory: {{ json_encode(url('/categories')) }},
            baseProduct: {{ json_encode(url('/product')) }},
            handleInput(event) {
                this.query = event.target.value;
                clearTimeout(this.timer);
                if (this.query.length < this.minChars) {
                    this.results = { products: [], categories: [] };
                    return;
                }
                this.timer = setTimeout(() => this.fetchResults(), 250);
            },
            async fetchResults() {
                this.loading = true;
                try {
                    const res = await fetch({{ json_encode(route('search.suggest')) }} + '?q=' + encodeURIComponent(this.query));
                    if (!res.ok) throw new Error('Netwerkfout');
                    this.results = await res.json();
                } catch (e) {
                    this.results = { products: [], categories: [] };
                } finally {
                    this.loading = false;
                }
            },
            formatPrice(value) {
                if (value === null || value === undefined) return '';
                return new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(value);
            }
        }"
        x-init="if (query && query.length >= minChars) { fetchResults(); }"
    >
        <div class="bg-white border border-turbo-gold/35 rounded-2xl shadow-xl overflow-hidden">
            <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b border-gray-100">
                <div class="text-sm font-semibold text-gray-800">Zoeken</div>
                <button class="p-2 text-gray-500 hover:text-gray-700" @click="searchOpen = false" aria-label="Sluit zoekvenster">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-4 sm:px-6 py-5 space-y-6">
                <form method="GET" action="{{ route('products.index') }}" class="relative">
                    <span class="absolute left-4 inset-y-0 flex items-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 6a5 5 0 1 0 0 10 5 5 0 0 0 0-10Z" />
                        </svg>
                    </span>
                    <input
                        id="nav-search-input"
                        x-ref="searchInput"
                        type="search"
                        name="q"
                        :value="query"
                        @input="handleInput"
                        class="w-full rounded-full border border-turbo-blue/20 bg-white py-3.5 pl-12 pr-4 text-sm md:text-base shadow-inner focus:border-turbo-gold focus:ring-2 focus:ring-turbo-gold/20"
                        placeholder="Zoeken naar producten"
                    >
                </form>

                <div class="bg-white border border-gray-200 rounded-xl shadow-inner p-4 sm:p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-800">Trending</p>
                        <p class="text-xs text-gray-500" x-show="query.length < minChars">Minimaal 2 tekens</p>
                        <p class="text-xs text-gray-500" x-show="loading">Bezig met zoeken...</p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @php
                            $trending = ['Gasfles', 'Petroleum', 'Aanmaak', 'Pellets'];
                        @endphp
                        @foreach($trending as $term)
                            <button
                                type="button"
                                class="inline-flex items-center rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:border-green-600 hover:text-green-700"
                                @click="query='{{ $term }}'; handleInput({ target: { value: '{{ $term }}' } })"
                            >
                                {{ $term }}
                            </button>
                        @endforeach
                    </div>

                    <div class="space-y-4 max-h-[360px] overflow-y-auto">
                        <template x-if="!loading && query.length >= minChars && results.categories.length === 0 && results.products.length === 0">
                            <p class="text-sm text-gray-500">Geen resultaten gevonden.</p>
                        </template>

                        <template x-if="results.categories.length">
                            <div class="space-y-2">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Categorieen</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="category in results.categories" :key="category.id">
                                        <a
                                            class="inline-flex items-center gap-2 rounded-full bg-green-50 text-green-700 border border-green-100 px-3 py-1.5 text-sm font-semibold hover:border-green-200"
                                            :href="baseCategory + '/' + category.slug"
                                        >
                                            <span x-text="category.name"></span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="results.products.length">
                            <div class="space-y-3">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Producten</p>
                                <template x-for="product in results.products" :key="product.slug">
                                    <a
                                        class="flex items-center gap-4 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50/30 p-3 transition"
                                        :href="baseProduct + '/' + product.slug"
                                    >
                                        <div class="h-14 w-14 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                                            <img
                                                :src="product.image || '/images/logovd.png'"
                                                :alt="product.name"
                                                class="h-full w-full object-cover"
                                            >
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate" x-text="product.name"></p>
                                            <p class="text-xs text-gray-500" x-text="product.category"></p>
                                        </div>
                                        <div class="text-sm font-semibold text-green-700" x-text="formatPrice(product.price)"></div>
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex justify-end">
                        <a
                            class="text-sm font-semibold text-green-700 hover:text-green-800"
                            :href="baseSearch + '?q=' + encodeURIComponent(query)"
                            x-show="query.length >= minChars"
                        >
                            Bekijk alle resultaten
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
