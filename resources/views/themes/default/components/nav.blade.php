<nav
    class="relative"
    x-data="{ open: false, userOpen: false, searchOpen: false, logoError: false }"
    @keydown.window.escape="searchOpen = false"
>
    <div class="bg-[#e6e1dc] text-[13px] text-neutral-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex flex-wrap items-center justify-between gap-3 py-2">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m3 5 3 1 3-1 3 1 3-1 3 1 3-1v14l-3 1-3-1-3 1-3-1-3 1-3-1z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 8v11m6-9v11m6-9v11" />
                    </svg>
                    <span class="font-semibold">Gratis levering vanaf €100</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m12 3 2.09 4.23 4.67.68-3.38 3.29.8 4.66L12 14.77 7.82 15.9l.8-4.66L5.25 7.9l4.66-.67z" />
                    </svg>
                    <span class="font-semibold">Sinds 75 jaar specialist</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-rose-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12a9 9 0 0 1 9-9 9 9 0 0 1 9 9c0 7-9 9-9 9s-9-2-9-9Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v4l2 1" />
                    </svg>
                    <span class="font-semibold">Snelle bezorging, ook regionaal</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-neutral-900 text-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-20 gap-4">
                <div class="flex items-center gap-3">
                    <button
                        class="md:hidden inline-flex items-center justify-center rounded-md p-2 text-white/80 hover:bg-white/10 focus:outline-none"
                        @click="open = !open"
                        aria-label="Open menu"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <a href="/" class="relative block">
                        <span class="absolute inset-0 -z-10 -translate-y-2 md:-translate-y-3">
                            <span class="block h-full w-full bg-white"></span>
                        </span>
                        <span class="relative block bg-white px-4 py-3 shadow-md">
                            <img
                                src="/images/logovd.png"
                                alt="Logo"
                                class="h-10 w-auto"
                                x-on:error="logoError = true"
                                x-show="!logoError"
                            >
                            <div
                                class="h-10 w-32 flex items-center justify-center text-center text-sm font-black tracking-wide text-rose-700"
                                x-show="logoError"
                            >
                                LOGO
                            </div>
                        </span>
                    </a>
                </div>

                <div class="hidden md:flex flex-1 justify-center items-center space-x-7 text-sm font-semibold">
                    <a href="/" class="hover:text-white/80">Home</a>
                    <a href="{{ route('informatie') }}" class="hover:text-white/80">Informatie</a>
                    <a href="{{ route('over-ons') }}" class="hover:text-white/80">Over ons</a>
                    <a href="{{ route('products.index') }}" class="hover:text-white/80">Producten</a>
                    <a href="{{ route('locaties') }}" class="hover:text-white/80">Locaties</a>
                </div>

                <div class="hidden md:flex items-center gap-6 text-white/90">
                    <button
                        type="button"
                        class="hover:text-white"
                        @click="searchOpen = true; $nextTick(() => $refs.searchInput?.focus())"
                        aria-label="Open zoekvenster"
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
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-neutral-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="hover:text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.5 20.5a6.5 6.5 0 0 1 13 0M12 12.5a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                            </svg>
                            <span class="text-sm">Login</span>
                        </a>
                    @endguest

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
                                class="absolute -top-2 -right-2 bg-rose-600 text-white text-xs font-semibold rounded-full px-1.5 py-0.5"
                            ></span>
                        </template>
                    </div>
                </div>

                <div class="md:hidden flex items-center gap-3 text-white/90">
                    <button
                        type="button"
                        class="hover:text-white"
                        @click="searchOpen = true; $nextTick(() => $refs.searchInput?.focus())"
                        aria-label="Open zoekvenster"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-4.35-4.35M11 6a5 5 0 1 0 0 10 5 5 0 0 0 0-10Z" />
                        </svg>
                    </button>
                    <a href="{{ route('cart.index') }}" class="hover:text-white relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                            class="absolute -top-2 -right-2 bg-rose-600 text-white text-[10px] font-semibold rounded-full px-1.5 py-0.5"
                            style="display: none;"
                        ></span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div
        class="md:hidden bg-neutral-900/95 text-white/90"
        x-show="open"
        x-transition
        @click.outside="open = false"
        style="display:none;"
    >
        <div class="px-4 py-5 space-y-4 text-sm">
            <div class="flex items-center justify-between">
                <span class="font-semibold text-white">Menu</span>
                <button @click="open = false" aria-label="Close menu" class="p-2 text-white/80 hover:bg-white/10 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <a href="/" class="block font-semibold">Home</a>
            <a href="{{ route('informatie') }}" class="block">Informatie</a>
            <a href="{{ route('over-ons') }}" class="block">Over ons</a>
            <a href="{{ route('products.index') }}" class="block">Producten</a>
            <a href="{{ route('locaties') }}" class="block">Locaties</a>

            <div class="border-t border-white/10 pt-4 space-y-3">
                @auth
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="block">Admin paneel</a>
                    @endif
                    <a href="{{ route('account.dashboard') }}" class="block">Account</a>
                    <a href="{{ route('account.orders') }}" class="block">Bestellingen</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-rose-300">Logout</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="block">Login</a>
                @endguest
            </div>
        </div>
    </div>

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
            <div class="bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden">
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
                            x-ref="searchInput"
                            type="search"
                            name="q"
                            :value="query"
                            @input="handleInput"
                            class="w-full rounded-full border border-gray-200 bg-white py-3.5 pl-12 pr-4 text-sm md:text-base shadow-inner focus:border-green-600 focus:ring-2 focus:ring-green-100"
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
</nav>
