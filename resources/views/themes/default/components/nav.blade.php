<nav class="relative" x-data="{ open: false, userOpen: false }">
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
                            <img src="/images/logovd.png" alt="Logo" class="h-10 w-auto">
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
                    <a href="{{ route('products.index') }}" class="hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-4.35-4.35M11 6a5 5 0 1 0 0 10 5 5 0 0 0 0-10Z" />
                        </svg>
                    </a>

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
                    @else
                        <a href="{{ route('login') }}" class="hover:text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.5 20.5a6.5 6.5 0 0 1 13 0M12 12.5a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                            </svg>
                            <span class="text-sm">Login</span>
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
                                class="absolute -top-2 -right-2 bg-rose-600 text-white text-xs font-semibold rounded-full px-1.5 py-0.5"
                            ></span>
                        </template>
                    </div>
                </div>

                <div class="md:hidden flex items-center gap-3 text-white/90">
                    <a href="{{ route('products.index') }}" class="hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-4.35-4.35M11 6a5 5 0 1 0 0 10 5 5 0 0 0 0-10Z" />
                        </svg>
                    </a>
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
                @else
                    <a href="{{ route('login') }}" class="block">Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
