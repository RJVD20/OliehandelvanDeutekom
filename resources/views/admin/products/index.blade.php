@extends('admin.layouts.app')

@section('title', 'Producten')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Producten</h1>

    <a
        href="{{ route('admin.products.create') }}"
        class="px-4 py-2 bg-green-600 text-white rounded"
    >
        + Nieuw product
    </a>
</div>

<table class="w-full bg-white rounded shadow text-sm">
    <thead>
        <tr class="border-b">
            <th class="p-3 text-left">Naam</th>
            <th class="p-3">Categorie</th>
            <th class="p-3">Prijs</th>
            <th class="p-3">Actief</th>
            <th class="p-3"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3">{{ $product->name }}</td>
                <td class="p-3 text-center">{{ $product->category->name }}</td>
                <td class="p-3 text-right">
                    € {{ number_format($product->price, 2, ',', '.') }}
                </td>
                <td class="p-3 text-center">
                    {{ $product->active ? 'Ja' : 'Nee' }}
                </td>
                <td class="p-3 text-right">
                    <a
                        href="{{ route('admin.products.edit', $product) }}"
                        class="text-green-700"
                    >
                        Bewerken →
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $products->links() }}

@endsection
