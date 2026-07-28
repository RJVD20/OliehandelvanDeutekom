@extends('admin.layouts.app')

@section('title', 'Bestaande routes')

@section('content')
<div class="mb-6">
    <p class="text-xs font-bold uppercase tracking-[0.18em] text-turbo-gold">Routebeheer</p>
    <h1 class="mt-1 text-2xl font-bold">Bestaande routes</h1>
    <p class="mt-1 text-sm text-gray-500">Beheer chauffeurs, stops, routekaarten en verzendmails.</p>
</div>

@if(session('toast'))
    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
        {{ session('toast') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        {{ $errors->first() }}
    </div>
@endif

@include('admin.routes._manager')
@endsection
