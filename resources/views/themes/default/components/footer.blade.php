<footer class="border-t bg-white mt-16">

    <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div>
                <div class="text-lg font-semibold text-gray-900">
                    Oliehandel van Deutekom
                </div>
                <p class="text-sm text-gray-600 mt-3 leading-relaxed">
                    Kachelvloeistoffen, kachels en toebehoren met focus op kwaliteit en service.
                </p>
            </div>

            <div>
                <div class="text-sm font-semibold text-gray-900">Snelle links</div>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ url('/') }}" class="text-gray-600 hover:text-green-700 transition">Home</a></li>
                    <li><a href="{{ route('over-ons') }}" class="text-gray-600 hover:text-green-700 transition">Over ons</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-gray-600 hover:text-green-700 transition">Producten</a></li>
                    <li><a href="{{ route('locaties') }}" class="text-gray-600 hover:text-green-700 transition">Locaties</a></li>
                    <li><a href="{{ route('informatie') }}" class="text-gray-600 hover:text-green-700 transition">Informatie</a></li>
                </ul>
            </div>

            <div>
                <div class="text-sm font-semibold text-gray-900">Contact</div>
                <ul class="mt-3 space-y-2 text-sm text-gray-600">
                    <li>
                        <a href="mailto:info@oliehandelvandeutekom.nl" class="hover:text-green-700 transition">
                            info@oliehandelvandeutekom.nl
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-10 pt-8 border-t text-sm text-gray-500 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>© {{ date('Y') }} Oliehandel van Deutekom</div>
            <div class="text-gray-500">Alle rechten voorbehouden.</div>
        </div>
    </div>
</footer>
