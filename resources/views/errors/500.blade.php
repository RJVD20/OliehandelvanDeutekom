@extends('themes.default.layouts.app')

@section('title', 'Er ging iets mis')

@section('content')
<section class="mx-auto max-w-2xl py-16 text-center sm:py-24">
    <p class="turbo-section-label">Fout 500</p>
    <h1 class="mt-3 text-3xl font-bold sm:text-4xl">Er ging iets mis</h1>
    <p class="mx-auto mt-4 max-w-lg text-gray-600">
        We konden je verzoek niet afronden. Probeer het later opnieuw of ga terug naar de homepage.
    </p>
    <a href="{{ route('home') }}" class="turbo-button mt-8 px-5 py-3">Naar de homepage</a>
</section>
@endsection
