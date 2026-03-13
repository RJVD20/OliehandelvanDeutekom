@extends('admin.layouts.app')

@section('title', 'Locatie toevoegen')

@section('content')
<h1 class="text-2xl font-bold mb-6">Locatie toevoegen</h1>

<form method="POST" action="{{ route('admin.locations.store') }}" class="space-y-6 max-w-2xl">
    @csrf

    <div>
        <label class="text-sm font-medium">Naam</label>
        <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg border px-3 py-2" required>
    </div>

    <div>
        <label class="text-sm font-medium">Straat</label>
        <input type="text" name="street" value="{{ old('street') }}" class="w-full rounded-lg border px-3 py-2">
    </div>

    <div>
        <label class="text-sm font-medium">Postcode + plaats</label>
        <input type="text" name="postcode_city" value="{{ old('postcode_city') }}" class="w-full rounded-lg border px-3 py-2">
    </div>

    <div>
        <label class="text-sm font-medium">Openingstijden</label>
        <textarea name="opening" rows="4" class="w-full rounded-lg border px-3 py-2">{{ old('opening') }}</textarea>
        <p class="text-xs text-gray-500 mt-1">Gebruik nieuwe regels voor meerdere regels.</p>
    </div>

    <div>
        <label class="text-sm font-medium">Telefoon</label>
        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border px-3 py-2">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-medium">Latitude</label>
            <input type="text" name="lat" value="{{ old('lat') }}" class="w-full rounded-lg border px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Longitude</label>
            <input type="text" name="lng" value="{{ old('lng') }}" class="w-full rounded-lg border px-3 py-2">
        </div>
    </div>

    <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-sm font-medium text-gray-800">
        <input type="checkbox" name="show_on_map" value="1" {{ old('show_on_map') ? 'checked' : '' }} class="h-5 w-5 rounded border-gray-300">
        <span>Toon op kaart</span>
    </label>

    <div>
        <label class="text-sm font-medium">Opmerking</label>
        <input type="text" name="remark" value="{{ old('remark') }}" class="w-full rounded-lg border px-3 py-2">
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="px-4 py-2 rounded-lg bg-green-600 text-white font-semibold">Opslaan</button>
        <a href="{{ route('admin.locations.index') }}" class="px-3 py-2 rounded-lg border">Terug</a>
    </div>
</form>
@endsection
