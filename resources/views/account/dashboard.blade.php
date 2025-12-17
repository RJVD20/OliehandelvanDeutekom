@extends('themes.default.layouts.app')

@section('title', 'Mijn account')

@section('content')
<h1 class="text-3xl font-bold text-green-700 mb-6">
    Welkom terug, {{ auth()->user()->name }}
</h1>

<div class="grid sm:grid-cols-3 gap-6">
    <a href="{{ route('account.orders') }}" class="p-6 bg-white border rounded-lg hover:shadow">
        📦 Mijn bestellingen
    </a>

    <a href="{{ route('profile.edit') }}" class="p-6 bg-white border rounded-lg hover:shadow">
        ⚙️ Profiel
    </a>
</div>
@endsection
