@extends('themes.default.layouts.app')

@php
    use App\Models\Setting;
    use Illuminate\Support\Str;

    abort_unless(in_array($page, ['privacy', 'terms', 'returns', 'cookies'], true), 404);

    $company = [
        'name' => Setting::get('company_name', 'Kachelvloeistof.nl') ?: 'Kachelvloeistof.nl',
        'email' => Setting::get('company_email', 'info@kachelvloeistof.nl') ?: 'info@kachelvloeistof.nl',
        'phone' => Setting::get('company_phone', ''),
        'address' => Setting::get('company_address', ''),
        'kvk' => Setting::get('company_kvk', '77355431') ?: '77355431',
        'vat' => Setting::get('company_vat', 'NL003184350B48') ?: 'NL003184350B48',
    ];

    $title = Setting::get("legal_{$page}_title", config("legal.{$page}.title"))
        ?: config("legal.{$page}.title");
    $content = Setting::get("legal_{$page}_content", config("legal.{$page}.content"))
        ?: config("legal.{$page}.content");
    $content = strtr($content, [
        '{company_name}' => $company['name'],
        '{company_email}' => $company['email'],
        '{returns_url}' => route('returns'),
    ]);
    $contentHtml = Str::markdown($content, [
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
    ]);
@endphp

@section('title', $title)
@section('meta')
    <meta name="description" content="{{ $title }} van {{ $company['name'] }}.">
@endsection

@section('content')
<div class="mx-auto max-w-4xl">
    <header class="mb-6 rounded-2xl bg-turbo-navy px-6 py-8 text-white sm:px-9 sm:py-10">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-turbo-gold">Juridische informatie</p>
        <h1 class="mt-2 text-3xl font-bold sm:text-4xl">{{ $title }}</h1>
        <p class="mt-3 text-sm text-white/65">Laatst bijgewerkt: {{ now()->format('d-m-Y') }}</p>
    </header>

    <article class="turbo-card p-6 sm:p-9">
        <div class="prose max-w-none prose-headings:text-turbo-ink prose-a:text-turbo-blue">
            {!! $contentHtml !!}

            <h2>Contact en bedrijfsgegevens</h2>
            <address class="not-italic">
                <strong>{{ $company['name'] }}</strong><br>
                @if($company['address']){{ $company['address'] }}<br>@endif
                E-mail: <a href="mailto:{{ $company['email'] }}">{{ $company['email'] }}</a><br>
                @if($company['phone'])Telefoon: {{ $company['phone'] }}<br>@endif
                @if($company['kvk'])KvK: {{ $company['kvk'] }}<br>@endif
                @if($company['vat'])Btw-id: {{ $company['vat'] }}@endif
            </address>
        </div>
    </article>
</div>
@endsection
