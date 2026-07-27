@extends('themes.default.layouts.app')

@section('title', 'Pagina niet gevonden')

@section('content')
<section class="mx-auto max-w-2xl py-16 text-center sm:py-24">
    <p class="turbo-section-label">Fout 404</p>
    <h1 class="mt-3 text-3xl font-bold sm:text-4xl">Deze pagina is niet gevonden</h1>
    <p class="mx-auto mt-4 max-w-lg text-gray-600">
        De pagina bestaat niet meer of het adres is niet juist. Ga terug naar de homepage of bekijk ons assortiment.
    </p>
    <div class="mt-8 flex flex-wrap justify-center gap-3">
        <a href="{{ route('home') }}" class="turbo-button px-5 py-3">Naar de homepage</a>
        <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-lg border border-turbo-gold px-5 py-3 font-semibold text-turbo-ink hover:bg-turbo-gray">Bekijk producten</a>
    </div>
</section>
@endsection
