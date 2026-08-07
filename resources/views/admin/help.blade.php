@extends('admin.layouts.app')

@section('title', 'Help & uitleg')

@section('content')
<div class="mx-auto max-w-5xl">
    <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-700">Beheerhandleiding</p>
    <h1 class="mt-1 text-3xl font-bold text-gray-900">Help & uitleg</h1>
    <p class="mt-2 max-w-3xl text-gray-600">Een praktisch overzicht van de dagelijkse werkzaamheden. Open op iedere beheerpagina ook <strong>Hoe werkt dit?</strong> voor uitleg die bij die pagina hoort.</p>

    <section class="mt-8 rounded-2xl border border-green-200 bg-green-50 p-5">
        <h2 class="font-bold text-green-950">Aanbevolen dagelijkse volgorde</h2>
        <ol class="mt-3 grid gap-3 text-sm text-green-950 md:grid-cols-2">
            <li><strong>1.</strong> Controleer de taken op het dashboard.</li><li><strong>2.</strong> Verwerk nieuwe bestellingen en betalingen.</li>
            <li><strong>3.</strong> Plan leveringen en controleer de laadlijst.</li><li><strong>4.</strong> Werk contante betalingen direct bij.</li>
        </ol>
    </section>

    <div class="mt-6 grid gap-4 md:grid-cols-2">
        @foreach([
            ['Bestellingen', 'Controleer klantgegevens, artikelen, bezorgwijze en betaalstatus. Rond pas af wanneer levering én betaling correct zijn verwerkt.', 'admin.orders.index'],
            ['Betalingen', 'Openstaand is nog niet ontvangen. Markeer contant geld pas als betaald nadat het werkelijk is aangenomen.', 'admin.payments.index'],
            ['Routes', 'Plan alleen bezorgbestellingen, wijs een chauffeur toe en gebruik vóór vertrek de laadcontrole.', 'admin.routes.smart'],
            ['Producten', 'Actief maakt een product zichtbaar en bestelbaar. Controleer prijs, voorraadtekst en afbeelding vóór opslaan.', 'admin.products.index'],
            ['Acties', 'Een actieve actie kan direct op de website verschijnen. Controleer actieprijs, inbegrepen producten, kanalen en einddatum.', 'admin.promotions.index'],
            ['Nieuwsbrieven', 'Controleer ontvangers en inhoud met een testmail. Verzenden kan niet ongedaan worden gemaakt.', 'admin.newsletters.index'],
            ['CMS & locaties', 'Deze teksten en locaties zijn zichtbaar voor bezoekers. Bekijk na een wijziging altijd de openbare website.', 'admin.content.edit'],
            ['Gebruikers & auditlog', 'Geef beheerrechten alleen aan vertrouwde medewerkers. In het auditlog zie je wie belangrijke wijzigingen uitvoerde.', 'admin.users.index'],
        ] as [$title, $text, $route])
            <a href="{{ route($route) }}" class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:border-blue-200 hover:shadow-md">
                <h2 class="font-bold text-gray-900">{{ $title }}</h2><p class="mt-2 text-sm leading-6 text-gray-600">{{ $text }}</p><span class="mt-3 inline-block text-sm font-semibold text-blue-700">Open onderdeel →</span>
            </a>
        @endforeach
    </div>

    <section class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-900">
        <h2 class="font-bold">Handelingen die extra aandacht vragen</h2>
        <p class="mt-2 leading-6">Verwijderen, een nieuwsbrief verzenden, onderhoudsmodus inschakelen en een bestelling of betaling definitief afronden hebben direct gevolgen. Lees steeds de bevestiging en controleer het juiste record.</p>
    </section>
</div>
@endsection
