@php $hasProductImage = isset($product) && filled($product->image); @endphp

<section class="rounded-xl border-2 border-blue-100 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="product-image-heading">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 id="product-image-heading" class="text-base font-bold text-gray-900">Productafbeelding</h2>
            <p class="mt-1 text-xs leading-5 text-gray-500">Deze foto wordt in het productoverzicht en op de productpagina getoond.</p>
        </div>
        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">JPG, PNG of WebP · max. 8 MB</span>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:items-center">
        <div class="flex min-h-44 items-center justify-center rounded-xl border border-gray-200 bg-gray-50 p-3">
            <img x-cloak x-show="imagePreview" :src="imagePreview" alt="Voorbeeld van de gekozen productafbeelding" class="max-h-40 max-w-full object-contain">

            @if($hasProductImage)
                <img x-show="!imagePreview && !removeImage" src="{{ asset('storage/' . $product->image) }}" alt="Huidige afbeelding van {{ $product->name }}" class="max-h-40 max-w-full object-contain">
                <div x-cloak x-show="!imagePreview && removeImage" class="px-3 text-center text-xs font-semibold leading-5 text-red-600">De huidige afbeelding wordt verwijderd wanneer je opslaat.</div>
            @else
                <div x-show="!imagePreview" class="px-3 text-center text-gray-400"><span class="block text-3xl" aria-hidden="true">🖼️</span><span class="mt-2 block text-xs font-semibold">Nog geen afbeelding ingesteld</span></div>
            @endif
        </div>

        <div>
            @if($hasProductImage)
                <p x-show="!imagePreview && !removeImage" class="mb-3 text-sm font-semibold text-green-700">Huidige afbeelding</p>
            @endif
            <button type="button" @click="$refs.fileInput.click()" class="w-full rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700 sm:w-auto">{{ $hasProductImage ? 'Afbeelding vervangen' : 'Afbeelding toevoegen' }}</button>
            <button x-cloak x-show="imagePreview" type="button" @click="imagePreview = null; $refs.fileInput.value = ''" class="mt-2 block text-xs font-semibold text-red-600">Gekozen afbeelding annuleren</button>
            @if($hasProductImage)
                <button x-show="!imagePreview && !removeImage" type="button" @click="removeImage = true" class="mt-2 block text-xs font-semibold text-red-600">Huidige afbeelding verwijderen</button>
                <button x-cloak x-show="!imagePreview && removeImage" type="button" @click="removeImage = false" class="mt-2 block text-xs font-semibold text-blue-700">Verwijderen ongedaan maken</button>
                <input type="hidden" name="remove_image" :value="removeImage ? 1 : 0">
            @endif
            <p class="mt-3 text-xs leading-5 text-gray-500">Gebruik bij voorkeur een scherpe, vierkante afbeelding met rustige achtergrond. Je ziet de gekozen foto direct links als voorbeeld.</p>
        </div>
    </div>

    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" x-ref="fileInput" class="sr-only" @change="imagePreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null; @if($hasProductImage) removeImage = false @endif">
    @error('image')<p class="mt-3 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
</section>
