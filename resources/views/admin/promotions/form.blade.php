@extends('admin.layouts.app')
@section('title', $promotion->exists ? 'Actie bewerken' : 'Nieuwe actie')
@section('content')
@php
    $itemRows = old('items', $promotion->items?->map(fn($item) => ['product_id' => $item->product_id, 'quantity' => $item->quantity, 'role' => $item->role, 'label' => $item->label])->values()->all() ?? []);
@endphp
<div class="mb-6"><a href="{{ route('admin.promotions.index') }}" class="text-sm font-bold text-gray-500">← Terug naar acties</a><h1 class="mt-2 text-2xl font-bold">{{ $promotion->exists ? 'Actie bewerken' : 'Nieuwe actie' }}</h1></div>
@if($errors->any())<div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"><strong>Controleer de invoer.</strong><ul class="mt-2 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" enctype="multipart/form-data" action="{{ $promotion->exists ? route('admin.promotions.update', $promotion) : route('admin.promotions.store') }}" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]" x-data="{ items: {{ Illuminate\Support\Js::from($itemRows) }}, imagePreview: null, removeImage: false }">
    @csrf @if($promotion->exists) @method('PUT') @endif
    <div class="space-y-6">
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"><h2 class="font-bold">Campagne</h2><div class="mt-4 grid gap-4 sm:grid-cols-2">
            <label class="sm:col-span-2 text-sm font-bold">Interne naam<input name="name" value="{{ old('name', $promotion->name) }}" required class="mt-1 w-full rounded-xl border-gray-200"></label>
            <label class="text-sm font-bold">URL-slug <x-admin.field-help text="Het laatste deel van het actie-webadres. Leeg laten maakt automatisch een veilige slug van de interne naam." /><input name="slug" value="{{ old('slug', $promotion->slug) }}" class="mt-1 w-full rounded-xl border-gray-200" placeholder="automatisch"></label>
            <label class="text-sm font-bold">Publieke titel<input name="title" value="{{ old('title', $promotion->title) }}" required class="mt-1 w-full rounded-xl border-gray-200"></label>
            <label class="sm:col-span-2 text-sm font-bold">Korte omschrijving<textarea name="short_description" rows="2" class="mt-1 w-full rounded-xl border-gray-200">{{ old('short_description', $promotion->short_description) }}</textarea></label>
            <label class="sm:col-span-2 text-sm font-bold">Uitgebreide omschrijving<textarea name="description" rows="5" class="mt-1 w-full rounded-xl border-gray-200">{{ old('description', $promotion->description) }}</textarea></label>
        </div></section>
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"><h2 class="font-bold">Bundel en prijs</h2><div class="mt-4 grid gap-4 sm:grid-cols-2">
            <label class="sm:col-span-2 text-sm font-bold">Hoofdproduct<select name="main_product_id" required class="mt-1 w-full rounded-xl border-gray-200"><option value="">Kies product</option>@foreach($products as $product)<option value="{{ $product->id }}" @selected((int)old('main_product_id', $promotion->main_product_id)===$product->id)>{{ $product->name }} — € {{ number_format($product->price,2,',','.') }}</option>@endforeach</select></label>
            <label class="text-sm font-bold">Vaste bundelprijs (€) <x-admin.field-help text="Het totaalbedrag dat de klant voor de volledige actiecombinatie betaalt." /><input type="number" step="0.01" min="0.01" name="fixed_price" value="{{ old('fixed_price', $promotion->fixed_price) }}" required class="mt-1 w-full rounded-xl border-gray-200"></label>
            <label class="self-end rounded-xl bg-amber-50 p-3 text-sm font-bold"><span class="flex items-center gap-2"><input type="checkbox" name="free_shipping" value="1" @checked(old('free_shipping', $promotion->free_shipping))> Gratis standaardverzending</span><x-admin.field-help text="Wanneer aangevinkt betaalt de klant geen normale bezorgkosten voor deze actie." /></label>
        </div>
        <div class="mt-5 border-t pt-5"><div class="flex items-center justify-between"><div><h3 class="font-bold">Inbegrepen producten</h3><p class="text-xs text-gray-500">Deze worden als aparte orderregels vastgelegd.</p></div><button type="button" @click="items.push({product_id:'',quantity:1,role:'included',label:''})" class="rounded-lg border px-3 py-2 text-xs font-bold">+ Product</button></div>
            <template x-for="(item,index) in items" :key="index"><div class="mt-3 grid gap-2 rounded-xl bg-gray-50 p-3 sm:grid-cols-[1fr_6rem_8rem_auto]"><select :name="`items[${index}][product_id]`" x-model="item.product_id" required class="rounded-lg border-gray-200"><option value="">Product</option>@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name }}</option>@endforeach</select><input type="number" min="1" :name="`items[${index}][quantity]`" x-model="item.quantity" class="rounded-lg border-gray-200"><select :name="`items[${index}][role]`" x-model="item.role" class="rounded-lg border-gray-200"><option value="included">Inbegrepen</option><option value="free">Gratis</option></select><button type="button" @click="items.splice(index,1)" class="px-2 text-red-600">✕</button><input :name="`items[${index}][label]`" x-model="item.label" class="rounded-lg border-gray-200 sm:col-span-4" placeholder="Optioneel label, bijvoorbeeld Gratis hevelpomp"></div></template>
        </div></section>
    </div>
    <aside class="space-y-6">
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"><h2 class="font-bold">Publicatie</h2><div class="mt-4 space-y-3">
            @foreach(['active'=>'Actief','show_home'=>'Homepage','show_product'=>'Productpagina','show_cart'=>'Winkelmand'] as $field=>$label)<label class="flex items-center gap-2 text-sm font-bold"><input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $promotion->{$field}))>{{ $label }}</label>@endforeach
            <x-admin.field-help text="‘Actief’ zet de actie aan. De andere vinkjes bepalen op welke plekken bezoekers de actie zien." />
            <label class="block text-sm font-bold">Start <x-admin.field-help text="Vanaf dit moment wordt een actieve actie zichtbaar. Leeg betekent: direct beginnen." /><input type="datetime-local" name="starts_at" value="{{ old('starts_at', $promotion->starts_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-xl border-gray-200"></label>
            <label class="block text-sm font-bold">Einde <x-admin.field-help text="Na dit moment stopt de actie automatisch. Leeg betekent dat de actie geen automatische einddatum heeft." /><input type="datetime-local" name="ends_at" value="{{ old('ends_at', $promotion->ends_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-xl border-gray-200"></label>
            <label class="block text-sm font-bold">Volgorde <x-admin.field-help text="Een lager getal wordt eerder getoond wanneer meerdere acties tegelijk zichtbaar zijn." /><input type="number" name="sort_order" min="0" value="{{ old('sort_order', $promotion->sort_order ?? 0) }}" class="mt-1 w-full rounded-xl border-gray-200"></label>
        </div></section>
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="font-bold">Actieafbeelding</h2>
            <p class="mt-1 text-xs leading-5 text-gray-500">JPG, PNG of WebP — max. 8 MB. Gebruik bij voorkeur een liggende afbeelding zonder belangrijke tekst dicht langs de randen.</p>

            @if($promotion->imageUrl())
                <div x-show="!imagePreview && !removeImage" class="mt-4 space-y-2">
                    <p class="text-xs font-semibold text-gray-500">Huidige afbeelding</p>
                    <img src="{{ $promotion->imageUrl() }}" alt="{{ $promotion->image_alt }}" class="max-h-64 w-full rounded-xl border border-gray-100 bg-gray-50 object-contain">
                    <button type="button" @click="removeImage = true" class="text-xs font-semibold text-red-600 hover:text-red-800">Afbeelding verwijderen</button>
                </div>
                <div x-cloak x-show="removeImage && !imagePreview" class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-700">De huidige afbeelding wordt verwijderd wanneer je opslaat. <button type="button" @click="removeImage = false" class="font-bold underline">Ongedaan maken</button></div>
            @endif

            <div x-cloak x-show="imagePreview" class="mt-4 space-y-2">
                <p class="text-xs font-semibold text-green-700">Nieuwe afbeelding — voorbeeld</p>
                <img :src="imagePreview" alt="Voorbeeld van de gekozen afbeelding" class="max-h-64 w-full rounded-xl border border-green-200 bg-gray-50 object-contain">
                <button type="button" @click="imagePreview = null; $refs.promotionImage.value = ''; removeImage = false" class="text-xs font-semibold text-red-600">Keuze annuleren</button>
            </div>

            <button type="button" @click="$refs.promotionImage.click()" class="mt-4 w-full rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 px-4 py-5 text-sm font-semibold text-gray-600 hover:border-green-400 hover:bg-green-50">{{ $promotion->imageUrl() ? 'Andere afbeelding kiezen' : 'Afbeelding kiezen' }}</button>
            <input x-ref="promotionImage" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="hidden" @change="imagePreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null; removeImage = false">
            <input type="hidden" name="remove_image" :value="removeImage ? 1 : 0">
            @error('image')<p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror

            <label class="mt-4 block text-sm font-bold">Alt-tekst <x-admin.field-help text="Een korte beschrijving van de afbeelding voor slechtzienden en wanneer de afbeelding niet laadt." /><input name="image_alt" value="{{ old('image_alt', $promotion->image_alt) }}" class="mt-1 w-full rounded-xl border-gray-200"></label>
        </section>
        <button class="w-full rounded-xl bg-emerald-600 px-5 py-3 font-bold text-white">Actie opslaan</button>
        @if($promotion->exists)<button form="delete-promotion" type="submit" class="w-full text-sm font-bold text-red-600" onclick="return confirm('Actie definitief verwijderen?')">Actie verwijderen</button>@endif
    </aside>
</form>
@if($promotion->exists)<form id="delete-promotion" method="POST" action="{{ route('admin.promotions.destroy',$promotion) }}">@csrf @method('DELETE')</form>@endif
@endsection
