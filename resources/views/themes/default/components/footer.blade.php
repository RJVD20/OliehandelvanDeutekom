<footer class="mt-16 bg-neutral-900 text-white/80 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ open: { info: true, links: true, contact: true } }">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
            <div class="space-y-3">
                <div class="text-lg font-semibold text-white">Oliehandel van Deutekom</div>
                <p class="text-sm text-white/70 leading-relaxed">
                    Kachelvloeistoffen, kachels en toebehoren met focus op kwaliteit en service.
                </p>
            </div>

            <div class="border-t border-white/10 pt-4 md:border-0 md:pt-0">
                <button
                    type="button"
                    class="flex w-full items-center justify-between md:cursor-default text-sm font-semibold text-white"
                    @click="if(window.innerWidth < 768) { open.links = !open.links }"
                >
                    <span>Snelle links</span>
                    <span class="md:hidden text-white/60">{{ __('›') }}</span>
                </button>
                <ul
                    class="mt-3 space-y-2 text-sm text-white/70"
                    x-show="open.links"
                    x-collapse
                >
                    <li><a href="{{ url('/') }}" class="hover:text-white transition">Home</a></li>
                    <li><a href="{{ route('over-ons') }}" class="hover:text-white transition">Over ons</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-white transition">Producten</a></li>
                    <li><a href="{{ route('locaties') }}" class="hover:text-white transition">Locaties</a></li>
                    <li><a href="{{ route('informatie') }}" class="hover:text-white transition">Informatie</a></li>
                </ul>
            </div>

            <div class="border-t border-white/10 pt-4 md:border-0 md:pt-0">
                <button
                    type="button"
                    class="flex w-full items-center justify-between md:cursor-default text-sm font-semibold text-white"
                    @click="if(window.innerWidth < 768) { open.contact = !open.contact }"
                >
                    <span>Contact</span>
                    <span class="md:hidden text-white/60">{{ __('›') }}</span>
                </button>
                <ul
                    class="mt-3 space-y-2 text-sm text-white/70"
                    x-show="open.contact"
                    x-collapse
                >
                    <li>
                        <a href="mailto:info@oliehandelvandeutekom.nl" class="hover:text-white transition">
                            info@oliehandelvandeutekom.nl
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-wrap items-center gap-6 lg:gap-10 border-t border-white/10 pt-8 text-sm text-white/80">
            <div class="flex items-start gap-3 flex-1 min-w-[240px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#b5c4a2] mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h1.5M3 12h1.5M3 17h1.5M7 5h14v14H7z" />
                </svg>
                <div class="leading-snug">Op werkdagen voor 17.00u besteld, binnen 2 werkdagen thuis</div>
            </div>
            <div class="flex items-start gap-3 flex-1 min-w-[240px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#b5c4a2] mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16l-1.5 9h-13z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 16h6" />
                </svg>
                <div class="leading-snug">Gratis verzending v.a. € 100,- bij max. 24 kg</div>
            </div>
            <div class="flex items-start gap-3 flex-1 min-w-[240px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#b5c4a2] mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <div class="leading-snug">Al 75 jaar een begrip in Den Helder</div>
            </div>
            <div class="flex items-start gap-3 flex-1 min-w-[240px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#b5c4a2] mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 7h14v10H5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 11h14M9 15h2" />
                </svg>
                <div class="leading-snug">Betaal makkelijk en veilig met iDEAL</div>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-white/10 pt-6 text-xs text-white/70">
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-white/5 border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M5 6h9a5 5 0 0 1 0 10H5z" />
                    <path d="M9 9h4" />
                </svg>
                <span>iDEAL</span>
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-white/5 border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M4 7h16v10H4z" />
                    <path d="M9 11h6" />
                </svg>
                <span>Bancontact</span>
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-white/5 border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="4" y="7" width="16" height="10" rx="2" />
                    <path d="M7 12h10" />
                </svg>
                <span>VISA</span>
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-white/5 border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="4" y="7" width="16" height="10" rx="2" />
                    <path d="M9 10h6M9 14h6" />
                </svg>
                <span>Mastercard</span>
            </span>
        </div>

        <div class="mt-10 pt-8 border-t border-white/10 text-sm text-white/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>© {{ date('Y') }} Oliehandel van Deutekom</div>
            <div>Alle rechten voorbehouden.</div>
        </div>
    </div>
</footer>
