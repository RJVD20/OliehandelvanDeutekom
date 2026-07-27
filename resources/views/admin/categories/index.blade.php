@extends('admin.layouts.app')

@section('title', 'Categoriebeheer')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Categoriebeheer</h1>
    <a
        href="{{ route('admin.categories.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nieuwe categorie
    </a>
</div>

@if(session('toast'))
    <div class="mb-5 rounded-lg border px-4 py-3 text-sm
        {{ str_contains(session('toast'), 'niet') || str_contains(session('toast'), 'Kan') ? 'border-red-200 bg-red-50 text-red-700' : 'border-green-200 bg-green-50 text-green-700' }}">
        {{ session('toast') }}
    </div>
@endif

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <th class="px-5 py-3 text-left">Naam</th>
                <th class="px-5 py-3 text-left hidden sm:table-cell">Slug</th>
                <th class="px-5 py-3 text-left hidden sm:table-cell">Type</th>
                <th class="px-5 py-3 text-center">Producten</th>
                <th class="px-5 py-3 text-right">Acties</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($categories as $category)
                <tr class="hover:bg-gray-50 transition-colors" x-data="{ confirmDelete: false }">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $category->name }}</td>
                    <td class="px-5 py-3 text-gray-400 font-mono text-xs hidden sm:table-cell">/{{ $category->slug }}</td>
                    <td class="px-5 py-3 hidden sm:table-cell">
                        @if($category->type)
                            @php $typeColors = ['kachel' => 'bg-orange-100 text-orange-700', 'vloeistof' => 'bg-blue-100 text-blue-700', 'pellet' => 'bg-green-100 text-green-700', 'accessoire' => 'bg-purple-100 text-purple-700']; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$category->type] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($category->type) }}
                            </span>
                        @else
                            <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $category->products_count > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $category->products_count }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.categories.edit', $category) }}"
                               class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Bewerken
                            </a>

                            <div x-show="!confirmDelete">
                                <button type="button" @click="confirmDelete = true"
                                    class="text-sm font-medium {{ $category->products_count > 0 ? 'text-gray-300 cursor-not-allowed' : 'text-red-500 hover:text-red-700' }}"
                                    @if($category->products_count > 0) disabled title="Eerst alle producten ontkoppelen" @endif>
                                    Verwijderen
                                </button>
                            </div>

                            <div x-show="confirmDelete" x-cloak class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">Zeker?</span>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">Ja</button>
                                </form>
                                <button type="button" @click="confirmDelete = false"
                                    class="text-xs font-semibold text-gray-500 hover:text-gray-700">Nee</button>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-gray-400">
                        Nog geen categorieën aangemaakt.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
