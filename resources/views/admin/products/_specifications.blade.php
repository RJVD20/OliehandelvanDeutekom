@php
    $initialSpecifications = old('specifications', isset($product) ? ($product->specifications ?? []) : []);
@endphp

<div
    class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm"
    x-data="productSpecificationsEditor(@js($initialSpecifications))"
>
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400">Productspecificaties</h2>
            <p class="mt-1 text-xs text-gray-500">Bijvoorbeeld ‘Vermogen’ met als waarde ‘4800 W’.</p>
        </div>
        <button type="button" @click="addSpecification()" class="rounded-lg border border-green-200 px-3 py-2 text-xs font-semibold text-green-700 hover:bg-green-50">
            + Specificatie toevoegen
        </button>
    </div>

    <div class="mt-4 space-y-3">
        <template x-for="(specification, index) in specifications" :key="index">
            <div class="relative grid gap-3 rounded-xl bg-gray-50 p-3 pr-12 sm:grid-cols-2">
                <label class="text-xs font-semibold text-gray-700">
                    Eigenschap
                    <input x-model="specification.name" :name="`specifications[${index}][name]`" required placeholder="Bijv. Vermogen" class="mt-1.5 w-full rounded-lg border-gray-300 bg-white text-sm">
                </label>
                <label class="text-xs font-semibold text-gray-700">
                    Waarde
                    <input x-model="specification.value" :name="`specifications[${index}][value]`" required placeholder="Bijv. 4800 W" class="mt-1.5 w-full rounded-lg border-gray-300 bg-white text-sm">
                </label>
                <button type="button" @click="removeSpecification(index)" class="absolute right-2 top-2 flex h-9 w-9 items-center justify-center rounded text-xl text-gray-400 hover:bg-red-50 hover:text-red-600" aria-label="Specificatie verwijderen">×</button>
            </div>
        </template>

        <p x-show="specifications.length === 0" class="rounded-lg bg-gray-50 p-4 text-sm text-gray-500">
            Nog geen specificaties toegevoegd.
        </p>
    </div>
</div>

@once
    <script>
        function productSpecificationsEditor(initialSpecifications) {
            return {
                specifications: initialSpecifications ?? [],
                addSpecification() {
                    this.specifications.push({ name: '', value: '' });
                },
                removeSpecification(index) {
                    this.specifications.splice(index, 1);
                },
            };
        }
    </script>
@endonce
