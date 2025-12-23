@extends('admin.layouts.app')

@section('title', 'Producten')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-500">Producten</p>
        <p class="text-2xl font-bold">{{ $totalProducts }}</p>
    </div>

    <div class="bg-green-50 rounded shadow p-4">
        <p class="text-xs text-green-700">Actief</p>
        <p class="text-2xl font-bold text-green-700">{{ $activeProducts }}</p>
    </div>

    <div class="bg-gray-100 rounded shadow p-4">
        <p class="text-xs text-gray-600">Inactief</p>
        <p class="text-2xl font-bold">{{ $inactiveProducts }}</p>
    </div>

    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-500">Bestellingen</p>
        <p class="text-2xl font-bold">{{ $totalOrders }}</p>
    </div>
</div>


<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Producten</h1>

    <a
        href="{{ route('admin.products.create') }}"
        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
    >
        + Nieuw product
    </a>
</div>

<table class="w-full bg-white rounded shadow text-sm">
    <thead>
        <tr class="border-b bg-gray-50">
            <th class="p-3 text-left">Naam</th>
            <th class="p-3 text-left">Categorie</th>
            <th class="p-3 text-right">Prijs</th>
            <th class="p-3 text-center">Actief</th>
            <th class="p-3 text-center">Uitgelicht</th>
            <th class="p-3 text-right">Acties</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-medium">
                    {{ $product->name }}
                </td>

                <td class="p-3">
                    {{ $product->category->name ?? '-' }}
                </td>

                <td class="p-3 text-right">
                    € {{ number_format($product->price, 2, ',', '.') }}
                </td>

                <td class="p-3 text-center">
                    <button
                        type="button"
                        onclick="toggleActive({{ $product->id }}, this)"
                        class="px-2 py-1 text-xs rounded transition
                            {{ $product->active
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-200 text-gray-600' }}"
                    >
                        {{ $product->active ? 'Actief' : 'Inactief' }}
                    </button>
                </td>

                <td class="p-3 text-center">
                    <button
                        type="button"
                        onclick="toggleFeatured({{ $product->id }}, this)"
                        class="px-2 py-1 text-xs rounded transition
                            {{ $product->featured
                                ? 'bg-yellow-100 text-yellow-700'
                                : 'bg-gray-200 text-gray-600' }}"
                    >
                        {{ $product->featured ? 'Uitgelicht' : 'Niet' }}
                    </button>
                </td>

                <td class="p-3 text-right space-x-3">
                    <a
                        href="{{ route('admin.products.edit', $product) }}"
                        class="text-green-700 hover:underline"
                    >
                        Bewerken
                    </a>

                    <form
                        method="POST"
                        action="{{ route('admin.products.destroy', $product) }}"
                        class="inline"
                        onsubmit="return confirm('Weet je zeker dat je dit product wilt verwijderen?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button class="text-red-600 hover:underline">
                            Verwijderen
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="p-6 text-center text-gray-500">
                    Geen producten gevonden.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-6">
    {{ $products->links() }}
</div>

@endsection

@section('scripts')
<script>
function toggleActive(productId, button) {
    fetch(`/admin/products/${productId}/toggle-active`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.active) {
            button.textContent = 'Actief';
            button.className = 'px-2 py-1 text-xs rounded bg-green-100 text-green-700';
        } else {
            button.textContent = 'Inactief';
            button.className = 'px-2 py-1 text-xs rounded bg-gray-200 text-gray-600';
        }
    })
    .catch(() => {
        alert('Status wijzigen mislukt');
    });
}

function toggleFeatured(productId, button) {
    fetch(`/admin/products/${productId}/toggle-featured`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.featured) {
            button.textContent = 'Uitgelicht';
            button.className = 'px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700';
        } else {
            button.textContent = 'Niet';
            button.className = 'px-2 py-1 text-xs rounded bg-gray-200 text-gray-600';
        }
    })
    .catch(() => {
        alert('Wijzigen mislukt');
    });
}
</script>
@endsection
