@extends('admin.layouts.app')

@section('title', 'Locaties')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Locaties</h1>
    <a href="{{ route('admin.locations.create') }}" class="px-4 py-2 rounded-lg bg-green-600 text-white font-semibold">Nieuwe locatie</a>
</div>

@if(session('toast'))
    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">
        {{ session('toast') }}
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="border-b">
                <th class="p-3">Naam</th>
                <th class="p-3">Adres</th>
                <th class="p-3">Telefoon</th>
                <th class="p-3">Kaart</th>
                <th class="p-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($locations as $location)
                <tr class="border-b">
                    <td class="p-3 font-medium">{{ $location->name }}</td>
                    <td class="p-3 text-gray-600">{{ $location->street }}<br>{{ $location->postcode_city }}</td>
                    <td class="p-3 text-gray-600">{{ $location->phone ?? '-' }}</td>
                    <td class="p-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $location->show_on_map ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $location->show_on_map ? 'Ja' : 'Nee' }}
                        </span>
                    </td>
                    <td class="p-3 text-right">
                        <a href="{{ route('admin.locations.edit', $location) }}" class="text-blue-600 mr-3">Bewerk</a>
                        <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" class="inline" onsubmit="return confirm('Locatie verwijderen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Verwijder</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="p-3 text-gray-500" colspan="5">Geen locaties gevonden.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
