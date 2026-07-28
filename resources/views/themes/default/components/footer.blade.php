@php
    use App\Models\Setting;

    $companyName = Setting::get('company_name', 'Kachelvloeistof.nl') ?: 'Kachelvloeistof.nl';
    $companyEmail = Setting::get('company_email', 'info@kachelvloeistof.nl') ?: 'info@kachelvloeistof.nl';
    $companyPhone = Setting::get('company_phone', '');
    $companyAddress = Setting::get('company_address', '');
    $companyKvk = Setting::get('company_kvk', '');
    $companyVat = Setting::get('company_vat', '');
@endphp

<footer class="turbo-footer mt-16 text-white/80 border-t">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ open: { info: true, links: true, contact: true } }">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4 lg:gap-10">
            <div class="space-y-3">
                <div class="text-lg font-semibold text-white">{{ $companyName }}</div>
                <p class="text-sm text-white/70 leading-relaxed">
                    Kachelvloeistoffen, kachels en toebehoren met focus op kwaliteit en service.
                </p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-turbo-gold hover:text-white">
                    Bekijk assortiment <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="border-t border-white/10 pt-4 md:border-0 md:pt-0">
                <button
                    type="button"
                    class="flex w-full items-center justify-between md:cursor-default text-sm font-semibold text-white"
                    @click="if(window.innerWidth < 768) { open.links = !open.links }"
                >
                    <span>Snelle links</span>
                    <span class="md:hidden text-white/60">›</span>
                </button>
                <ul
                    class="mt-3 space-y-2 text-sm text-white/70"
                    x-show="open.links"
                    x-collapse
                >
                    <li><a href="{{ url('/') }}" class="hover:text-white transition">Home</a></li>
                    <li><a href="{{ route('over-ons') }}" class="hover:text-white transition">Over ons</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-white transition">Overige producten</a></li>
                    <li><a href="{{ route('locaties') }}" class="hover:text-white transition">Locaties</a></li>
                    <li><a href="{{ route('informatie') }}" class="hover:text-white transition">Informatie</a></li>
                </ul>
            </div>

            <div class="border-t border-white/10 pt-4 md:border-0 md:pt-0">
                <h2 class="text-sm font-semibold text-white">Klantenservice</h2>
                <ul class="mt-3 space-y-2 text-sm text-white/70">
                    <li><a href="{{ route('returns') }}" class="transition hover:text-white">Retourneren</a></li>
                    <li><a href="{{ route('terms') }}" class="transition hover:text-white">Algemene voorwaarden</a></li>
                    <li><a href="{{ route('privacy') }}" class="transition hover:text-white">Privacyverklaring</a></li>
                    <li><a href="{{ route('cookies') }}" class="transition hover:text-white">Cookies</a></li>
                </ul>
            </div>

            <div class="border-t border-white/10 pt-4 md:border-0 md:pt-0">
                <button
                    type="button"
                    class="flex w-full items-center justify-between md:cursor-default text-sm font-semibold text-white"
                    @click="if(window.innerWidth < 768) { open.contact = !open.contact }"
                >
                    <span>Contact</span>
                    <span class="md:hidden text-white/60">›</span>
                </button>
                <ul
                    class="mt-3 space-y-2 text-sm text-white/70"
                    x-show="open.contact"
                    x-collapse
                >
                    <li>
                        <a href="mailto:{{ $companyEmail }}" class="hover:text-white transition">
                            {{ $companyEmail }}
                        </a>
                    </li>
                    @if($companyPhone)
                        <li><a href="tel:{{ preg_replace('/[^0-9+]/', '', $companyPhone) }}" class="transition hover:text-white">{{ $companyPhone }}</a></li>
                    @endif
                    @if($companyAddress)<li>{{ $companyAddress }}</li>@endif
                    @if($companyKvk)<li>KvK: {{ $companyKvk }}</li>@endif
                    @if($companyVat)<li>Btw-id: {{ $companyVat }}</li>@endif
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-wrap items-center gap-6 lg:gap-10 border-t border-white/10 pt-8 text-sm text-white/80">
            <div class="flex items-start gap-3 flex-1 min-w-[240px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#b5c4a2] mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 1 0 0-4h14a2 2 0 1 0 0 4M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8" />
                </svg>
                <div class="leading-snug">Gratis geleverd vanaf 3 jerrycans</div>
            </div>
            <div class="flex items-start gap-3 flex-1 min-w-[240px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#b5c4a2] mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                </svg>
                <div class="leading-snug">Bezorging binnen 4 tot 8 werkdagen</div>
            </div>
            <div class="flex items-start gap-3 flex-1 min-w-[240px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#b5c4a2] mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />
                </svg>
                <div class="leading-snug">Specialist in kachels en kachelvloeistof</div>
            </div>
            <div class="flex items-start gap-3 flex-1 min-w-[240px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#b5c4a2] mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
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
            <div>© {{ date('Y') }} {{ $companyName }}. Alle rechten voorbehouden.</div>
            <div class="flex flex-wrap gap-x-4 gap-y-2">
                <a href="{{ route('privacy') }}" class="hover:text-white">Privacy</a>
                <a href="{{ route('terms') }}" class="hover:text-white">Voorwaarden</a>
                <a href="{{ route('cookies') }}" class="hover:text-white">Cookies</a>
            </div>
        </div>
    </div>
</footer>
