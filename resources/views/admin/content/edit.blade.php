@extends('admin.layouts.app')

@section('title', 'CMS teksten')

@section('content')
@php
    use App\Models\Setting;
    use Illuminate\Support\Str;

    $homeHeroImageValue = Setting::get('home_hero_image');
    $homeHeroImageUrl = null;
    if (is_string($homeHeroImageValue) && trim($homeHeroImageValue) !== '') {
        $homeHeroImageValue = trim($homeHeroImageValue);
        $homeHeroImageUrl = Str::startsWith($homeHeroImageValue, ['http://', 'https://', '/'])
            ? $homeHeroImageValue
            : asset('storage/' . $homeHeroImageValue);
    }
@endphp
<div class="mb-6 flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
    <div>
        <div class="mb-2 inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Website-inhoud</div>
        <h1 class="text-2xl font-bold text-gray-900">Content beheren</h1>
        <p class="mt-1 text-sm text-gray-500">Pas teksten aan en controleer het resultaat direct in de live preview.</p>
    </div>
    @if($lastContentChange)
        <div class="rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-600">
            <span class="block text-xs font-semibold uppercase tracking-wide text-gray-400">Laatste wijziging</span>
            <span class="mt-1 block font-medium text-gray-800">{{ $lastContentChange->user?->name ?? 'Onbekende gebruiker' }}</span>
            <span class="text-xs">{{ $lastContentChange->created_at->diffForHumans() }}</span>
        </div>
    @endif
</div>

<div class="flex flex-col lg:flex-row gap-6">
    <aside class="w-full lg:w-[420px] xl:w-[460px] shrink-0">
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden lg:sticky lg:top-6 lg:max-h-[calc(100vh-3rem)] lg:flex lg:flex-col">
            <div class="px-5 py-4 border-b">
                <label for="content-search" class="text-xs font-semibold uppercase tracking-wide text-gray-500">Zoeken in content</label>
                <div class="relative mt-2">
                    <input id="content-search" type="search" placeholder="Zoek bijvoorbeeld ‘hero’ of ‘bezorging’…" class="w-full rounded-xl border border-gray-300 bg-gray-50 py-2.5 pl-10 pr-3 text-sm focus:border-emerald-500 focus:bg-white focus:ring-emerald-500">
                    <svg class="pointer-events-none absolute left-3.5 top-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>
                </div>
                <p id="search-result" class="mt-2 hidden text-xs text-gray-500"></p>
            </div>

            <div class="px-3 py-3 border-b bg-gray-50">
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" data-section="bedrijf" data-preview="{{ route('privacy') }}" class="section-tab px-3 py-2 rounded-lg text-sm font-semibold bg-white border">Bedrijf</button>
                    <button type="button" data-section="juridisch" data-preview="{{ route('privacy') }}" class="section-tab px-3 py-2 rounded-lg text-sm font-semibold bg-white border">Juridisch</button>
                    <button type="button" data-section="home" data-preview="{{ route('home') }}" class="section-tab px-3 py-2 rounded-lg text-sm font-semibold bg-gray-900 text-white">Home</button>
                    <button type="button" data-section="bezorging" data-preview="{{ route('home') }}" class="section-tab px-3 py-2 rounded-lg text-sm font-semibold bg-white border">Bezorging</button>
                    <button type="button" data-section="informatie" data-preview="{{ route('informatie') }}" class="section-tab px-3 py-2 rounded-lg text-sm font-semibold bg-white border">Informatie</button>
                    <button type="button" data-section="over-ons" data-preview="{{ route('over-ons') }}" class="section-tab px-3 py-2 rounded-lg text-sm font-semibold bg-white border">Over‑ons</button>
                    <button type="button" data-section="locaties" data-preview="{{ route('locaties') }}" class="section-tab px-3 py-2 rounded-lg text-sm font-semibold bg-white border">Locaties</button>
                </div>
            </div>

            @if(session('toast'))
                <div class="mx-4 mt-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                    {{ session('toast') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.content.update') }}" id="content-form" class="space-y-6 overflow-y-auto p-4 pb-24" enctype="multipart/form-data">
                @csrf

                <section class="hidden space-y-4" data-section-panel="bedrijf">
                    <div>
                        <h2 class="text-base font-semibold">Bedrijfsgegevens</h2>
                        <p class="mt-1 text-xs text-gray-500">Deze gegevens verschijnen in de footer en op juridische pagina’s. Vul vóór livegang alle velden in.</p>
                    </div>
                    <div class="grid gap-4">
                        @foreach([
                            'company_name' => ['Bedrijfs-/handelsnaam', 'Kachelvloeistof.nl'],
                            'company_email' => ['E-mailadres', 'info@kachelvloeistof.nl'],
                            'company_phone' => ['Telefoonnummer', ''],
                            'company_address' => ['Vestigingsadres', 'Straat 1, 1234 AB Plaats'],
                            'company_kvk' => ['KvK-nummer', ''],
                            'company_vat' => ['Btw-identificatienummer', ''],
                        ] as $field => [$label, $placeholder])
                            <div>
                                <label class="text-sm font-medium">{{ $label }}</label>
                                <input type="text" name="{{ $field }}" value="{{ $values[$field] ?? '' }}" placeholder="{{ $placeholder }}" class="w-full rounded-lg border px-3 py-2">
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="hidden space-y-4" data-section-panel="juridisch">
                    <div>
                        <h2 class="text-base font-semibold">Juridische pagina’s</h2>
                        <p class="mt-1 text-xs leading-5 text-gray-500">
                            Gebruik <code>## Tussenkop</code> voor koppen, <code>- item</code> voor opsommingen en een lege regel voor een nieuwe alinea.
                            De placeholders <code>{company_name}</code>, <code>{company_email}</code> en <code>{returns_url}</code> worden automatisch ingevuld.
                        </p>
                    </div>

                    @foreach([
                        'privacy' => ['Privacyverklaring', route('privacy')],
                        'terms' => ['Algemene voorwaarden', route('terms')],
                        'returns' => ['Retourneren', route('returns')],
                        'cookies' => ['Cookieverklaring', route('cookies')],
                    ] as $legalPage => [$label, $previewUrl])
                        <details class="group overflow-hidden rounded-xl border">
                            <summary class="flex cursor-pointer select-none items-center justify-between bg-gray-50 px-4 py-3">
                                <span class="text-sm font-semibold">{{ $label }}</span>
                                <span class="text-emerald-600 transition-transform duration-200 group-open:rotate-45">+</span>
                            </summary>
                            <div class="grid gap-3 bg-white p-4">
                                <div>
                                    <label class="text-sm font-medium">Paginatitel</label>
                                    <input type="text" name="legal_{{ $legalPage }}_title" value="{{ $values['legal_'.$legalPage.'_title'] ?? '' }}" class="mt-1 w-full rounded-lg border px-3 py-2">
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Inhoud</label>
                                    <textarea name="legal_{{ $legalPage }}_content" rows="18" class="mt-1 w-full rounded-lg border px-3 py-2 font-mono text-xs leading-5">{{ $values['legal_'.$legalPage.'_content'] ?? '' }}</textarea>
                                </div>
                                <a href="{{ $previewUrl }}" target="_blank" class="text-sm font-semibold text-blue-600">Open pagina in nieuw tabblad →</a>
                            </div>
                        </details>
                    @endforeach
                </section>

                <section class="space-y-4" data-section-panel="home">
                    <h2 class="text-base font-semibold">Homepage</h2>
                    <div class="grid gap-4">
            <div>
                <label class="text-sm font-medium">Hero titel</label>
                <input type="text" name="home_hero_title" value="{{ $values['home_hero_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium">Hero tussenregel</label>
                <textarea name="home_hero_subtitle" rows="2" class="w-full rounded-lg border px-3 py-2">{{ $values['home_hero_subtitle'] ?? '' }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Kleinere tekst direct onder de grote titel.</p>
            </div>
            <div>
                <label class="text-sm font-medium">Hero intro</label>
                <textarea name="home_hero_intro" rows="3" class="w-full rounded-lg border px-3 py-2">{{ $values['home_hero_intro'] ?? '' }}</textarea>
            </div>
            <div>
                <label class="text-sm font-medium">Hero afbeelding</label>
                <input
                    type="file"
                    name="home_hero_image"
                    accept="image/*"
                    class="block w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-gray-900 file:text-white file:font-semibold hover:file:bg-gray-800 border border-gray-300 rounded-lg"
                >
                @if($homeHeroImageUrl)
                    <div class="mt-2 flex items-center gap-3">
                        <img src="{{ $homeHeroImageUrl }}" alt="Hero" class="h-16 w-28 rounded-md object-cover border">
                        <label class="inline-flex items-center gap-2 text-xs text-gray-600">
                            <input type="checkbox" name="home_hero_image_remove" value="1" class="rounded">
                            Verwijder huidige afbeelding
                        </label>
                    </div>
                @endif
                <p class="mt-1 text-xs text-gray-500">Als je niets kiest, blijft de huidige afbeelding staan.</p>
            </div>
            <div>
                <label class="text-sm font-medium">Hero knoptekst</label>
                <input type="text" name="home_hero_cta_label" value="{{ $values['home_hero_cta_label'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium">Titel productenblok</label>
                <input type="text" name="home_products_title" value="{{ $values['home_products_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium">Intro productenblok</label>
                <textarea name="home_products_intro" rows="2" class="w-full rounded-lg border px-3 py-2">{{ $values['home_products_intro'] ?? '' }}</textarea>
            </div>
            <div>
                <label class="text-sm font-medium">Melding staffelprijzen</label>
                <input type="text" name="home_tier_prices_text" value="{{ $values['home_tier_prices_text'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium">Titel categorieblok</label>
                <input type="text" name="home_categories_title" value="{{ $values['home_categories_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium">Titel FAQ</label>
                <input type="text" name="home_faq_title" value="{{ $values['home_faq_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
            </div>
                    </div>

                    <div class="mt-4">
                        <h3 class="font-semibold mb-3">FAQ items</h3>
                        <div class="space-y-3">
                            @for($i = 1; $i <= 5; $i++)
                                <details class="group border rounded-xl overflow-hidden">
                                    <summary class="cursor-pointer select-none px-4 py-3 flex items-center justify-between bg-gray-50">
                                        <span class="text-sm font-semibold">FAQ {{ $i }}</span>
                                        <span class="text-emerald-600 transition-transform duration-200 group-open:rotate-45">+</span>
                                    </summary>
                                    <div class="p-4 grid gap-2 bg-white">
                                        <label class="text-sm font-medium">Vraag</label>
                                        <input type="text" name="home_faq_{{ $i }}_q" value="{{ $values['home_faq_'.$i.'_q'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
                                        <label class="text-sm font-medium">Antwoord</label>
                                        <textarea name="home_faq_{{ $i }}_a" rows="3" class="w-full rounded-lg border px-3 py-2">{{ $values['home_faq_'.$i.'_a'] ?? '' }}</textarea>
                                    </div>
                                </details>
                            @endfor
                        </div>
                    </div>
                </section>

                <section class="space-y-4 hidden" data-section-panel="bezorging">
                    <div>
                        <h2 class="text-base font-semibold">Bezorginformatie</h2>
                        <p class="mt-1 text-xs text-gray-500">Deze teksten verschijnen bij producten, in de winkelwagen en tijdens het afrekenen.</p>
                    </div>

                    <div class="grid gap-4">
                        <div>
                            <label class="text-sm font-medium">Korte melding bij producten</label>
                            <input type="text" name="delivery_compact_text" value="{{ $values['delivery_compact_text'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Titel uitgebreid verzendblok</label>
                            <input type="text" name="delivery_title" value="{{ $values['delivery_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
                        </div>

                        <div class="space-y-3">
                            @for($i = 1; $i <= 4; $i++)
                                <div>
                                    <label class="text-sm font-medium">Bezorgregel {{ $i }}</label>
                                    <textarea name="delivery_rule_{{ $i }}" rows="2" class="w-full rounded-lg border px-3 py-2">{{ $values['delivery_rule_'.$i] ?? '' }}</textarea>
                                </div>
                            @endfor
                        </div>
                    </div>
                </section>

                <section class="space-y-4 hidden" data-section-panel="informatie">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold">Informatiepagina</h2>
                        <span class="text-xs text-gray-500">6 blokken + intro</span>
                    </div>
                    <div class="grid gap-4">
            <div>
                <label class="text-sm font-medium">Titel</label>
                <input type="text" name="informatie_title" value="{{ $values['informatie_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium">Intro</label>
                <textarea name="informatie_intro" rows="2" class="w-full rounded-lg border px-3 py-2">{{ $values['informatie_intro'] ?? '' }}</textarea>
            </div>
            <div class="space-y-3">
                @for($i = 1; $i <= 6; $i++)
                    <details class="group border rounded-xl overflow-hidden">
                        <summary class="cursor-pointer select-none px-4 py-3 flex items-center justify-between bg-gray-50">
                            <span class="text-sm font-semibold">Blok {{ $i }}</span>
                            <span class="text-emerald-600 transition-transform duration-200 group-open:rotate-45">+</span>
                        </summary>
                        <div class="p-4 grid gap-2 bg-white">
                            <label class="text-sm font-medium">Titel</label>
                            <input type="text" name="informatie_block_{{ $i }}_title" value="{{ $values['informatie_block_'.$i.'_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
                            <label class="text-sm font-medium">Tekst</label>
                            <textarea name="informatie_block_{{ $i }}_text" rows="3" class="w-full rounded-lg border px-3 py-2">{{ $values['informatie_block_'.$i.'_text'] ?? '' }}</textarea>
                        </div>
                    </details>
                @endfor
            </div>
                    </div>
                </section>

                <section class="space-y-4 hidden" data-section-panel="over-ons">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold">Over‑ons pagina</h2>
                        <span class="text-xs text-gray-500">Hero • USP • Verhaal • CTA</span>
                    </div>
                    <details class="group border rounded-xl overflow-hidden">
                        <summary class="cursor-pointer select-none px-4 py-3 flex items-center justify-between bg-gray-50">
                            <span class="text-sm font-semibold">Hero</span>
                            <span class="text-emerald-600 transition-transform duration-200 group-open:rotate-45">+</span>
                        </summary>
                        <div class="p-4 grid gap-4 bg-white">
            <div>
                <label class="text-sm font-medium">Hero titel</label>
                <input type="text" name="over_hero_title" value="{{ $values['over_hero_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium">Hero intro</label>
                <textarea name="over_hero_intro" rows="3" class="w-full rounded-lg border px-3 py-2">{{ $values['over_hero_intro'] ?? '' }}</textarea>
            </div>
                        </div>
                    </details>

                    <details class="group border rounded-xl overflow-hidden">
                        <summary class="cursor-pointer select-none px-4 py-3 flex items-center justify-between bg-gray-50">
                            <span class="text-sm font-semibold">USP blokken</span>
                            <span class="text-emerald-600 transition-transform duration-200 group-open:rotate-45">+</span>
                        </summary>
                        <div class="p-4 grid gap-3 bg-white">
                @for($i = 1; $i <= 4; $i++)
                    <div class="border rounded-lg p-3 grid gap-2">
                        <label class="text-sm font-medium">USP {{ $i }} titel</label>
                        <input type="text" name="over_usp_{{ $i }}_title" value="{{ $values['over_usp_'.$i.'_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
                        <label class="text-sm font-medium">USP {{ $i }} tekst</label>
                        <textarea name="over_usp_{{ $i }}_text" rows="2" class="w-full rounded-lg border px-3 py-2">{{ $values['over_usp_'.$i.'_text'] ?? '' }}</textarea>
                    </div>
                @endfor
                        </div>
                    </details>

                    <details class="group border rounded-xl overflow-hidden">
                        <summary class="cursor-pointer select-none px-4 py-3 flex items-center justify-between bg-gray-50">
                            <span class="text-sm font-semibold">Verhaal blok 1</span>
                            <span class="text-emerald-600 transition-transform duration-200 group-open:rotate-45">+</span>
                        </summary>
                        <div class="p-4 grid gap-2 bg-white">
                <label class="text-sm font-medium">Titel</label>
                <input type="text" name="over_story_1_title" value="{{ $values['over_story_1_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
                <label class="text-sm font-medium">Tekst 1</label>
                <textarea name="over_story_1_text_1" rows="2" class="w-full rounded-lg border px-3 py-2">{{ $values['over_story_1_text_1'] ?? '' }}</textarea>
                <label class="text-sm font-medium">Tekst 2</label>
                <textarea name="over_story_1_text_2" rows="2" class="w-full rounded-lg border px-3 py-2">{{ $values['over_story_1_text_2'] ?? '' }}</textarea>
                        </div>
                    </details>

                    <details class="group border rounded-xl overflow-hidden">
                        <summary class="cursor-pointer select-none px-4 py-3 flex items-center justify-between bg-gray-50">
                            <span class="text-sm font-semibold">Verhaal blok 2</span>
                            <span class="text-emerald-600 transition-transform duration-200 group-open:rotate-45">+</span>
                        </summary>
                        <div class="p-4 grid gap-2 bg-white">
                <label class="text-sm font-medium">Titel</label>
                <input type="text" name="over_story_2_title" value="{{ $values['over_story_2_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
                <label class="text-sm font-medium">Tekst 1</label>
                <textarea name="over_story_2_text_1" rows="2" class="w-full rounded-lg border px-3 py-2">{{ $values['over_story_2_text_1'] ?? '' }}</textarea>
                <label class="text-sm font-medium">Tekst 2</label>
                <textarea name="over_story_2_text_2" rows="2" class="w-full rounded-lg border px-3 py-2">{{ $values['over_story_2_text_2'] ?? '' }}</textarea>
                        </div>
                    </details>

                    <details class="group border rounded-xl overflow-hidden">
                        <summary class="cursor-pointer select-none px-4 py-3 flex items-center justify-between bg-gray-50">
                            <span class="text-sm font-semibold">CTA blok</span>
                            <span class="text-emerald-600 transition-transform duration-200 group-open:rotate-45">+</span>
                        </summary>
                        <div class="p-4 grid gap-2 bg-white">
                <label class="text-sm font-medium">CTA titel</label>
                <input type="text" name="over_cta_title" value="{{ $values['over_cta_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
                <label class="text-sm font-medium">CTA tekst</label>
                <textarea name="over_cta_text" rows="2" class="w-full rounded-lg border px-3 py-2">{{ $values['over_cta_text'] ?? '' }}</textarea>
                <label class="text-sm font-medium">CTA knoptekst</label>
                <input type="text" name="over_cta_button" value="{{ $values['over_cta_button'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
                        </div>
                    </details>
                </section>

                <section class="space-y-4 hidden" data-section-panel="locaties">
                    <h2 class="text-base font-semibold">Locatiespagina</h2>
                    <div class="grid gap-4">
            <div>
                <label class="text-sm font-medium">Titel</label>
                <input type="text" name="locaties_title" value="{{ $values['locaties_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium">Intro</label>
                <textarea name="locaties_intro" rows="2" class="w-full rounded-lg border px-3 py-2">{{ $values['locaties_intro'] ?? '' }}</textarea>
            </div>
                    </div>

                    <div class="mt-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold">Locaties (beheer)</h3>
                            <div class="flex items-center gap-2 text-sm">
                                <a href="{{ route('admin.locations.create') }}" class="text-emerald-700">Nieuw</a>
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('admin.locations.index') }}" class="text-blue-600">Open beheer</a>
                            </div>
                        </div>
                        <div class="border rounded-xl overflow-hidden bg-white">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50">
                                    <tr class="border-b">
                                        <th class="p-2">Naam</th>
                                        <th class="p-2">Adres</th>
                                        <th class="p-2">Kaart</th>
                                        <th class="p-2"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($locations as $location)
                                        <tr class="border-b">
                                            <td class="p-2 font-medium">{{ $location->name }}</td>
                                            <td class="p-2 text-gray-600">{{ $location->street }}<br>{{ $location->postcode_city }}</td>
                                            <td class="p-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $location->show_on_map ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                                    {{ $location->show_on_map ? 'Ja' : 'Nee' }}
                                                </span>
                                            </td>
                                            <td class="p-2 text-right">
                                                <a href="{{ route('admin.locations.edit', $location) }}" class="text-blue-600">Bewerk</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="p-2 text-gray-500" colspan="4">Geen locaties gevonden.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <div id="save-notification" class="pointer-events-none sticky bottom-3 z-10 mx-auto -mb-24 hidden w-fit max-w-[calc(100%-1rem)] items-center gap-3 rounded-xl border bg-white/95 px-4 py-3 shadow-lg backdrop-blur" role="status" aria-live="polite">
                    <span id="save-indicator" class="h-2.5 w-2.5 shrink-0 rounded-full bg-blue-500"></span>
                    <div>
                        <span id="save-status" class="block text-sm font-medium text-gray-700">Automatisch opslaan…</span>
                        <span id="save-time" class="block text-xs text-gray-400"></span>
                    </div>
                </div>
            </form>
        </div>
    </aside>

    <section class="flex-1">
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden min-h-[720px]">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b px-4 py-3">
                <div>
                    <div class="text-sm font-semibold text-gray-800">Live preview</div>
                    <div class="text-xs text-gray-400">Dit is de actuele website</div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="hidden rounded-lg bg-gray-100 p-1 sm:flex">
                        <button type="button" data-preview-width="100%" class="preview-size rounded-md bg-white px-3 py-1.5 text-xs font-semibold shadow-sm">Desktop</button>
                        <button type="button" data-preview-width="390px" class="preview-size rounded-md px-3 py-1.5 text-xs font-semibold text-gray-500">Mobiel</button>
                    </div>
                    <a id="open-preview" href="{{ route('home') }}" target="_blank" rel="noopener" class="rounded-lg border px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Open pagina ↗</a>
                    <button type="button" id="refresh-preview" class="rounded-lg border px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Ververs</button>
                </div>
            </div>
            <div class="flex justify-center overflow-auto bg-gray-100 p-3">
                <iframe id="preview-frame" src="{{ route('home') }}" class="h-[calc(100vh-230px)] min-h-[680px] w-full bg-white shadow-sm transition-[width] duration-300"></iframe>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('content-form');
    const status = document.getElementById('save-status');
    const saveTime = document.getElementById('save-time');
    const saveNotification = document.getElementById('save-notification');
    const saveIndicator = document.getElementById('save-indicator');
    const frame = document.getElementById('preview-frame');
    const refreshBtn = document.getElementById('refresh-preview');
    const openPreview = document.getElementById('open-preview');
    const search = document.getElementById('content-search');
    const searchResult = document.getElementById('search-result');
    const tabs = document.querySelectorAll('.section-tab');
    const panels = document.querySelectorAll('[data-section-panel]');
    const previewSizes = document.querySelectorAll('.preview-size');

    let timer;
    let saving = false;
    let pendingSave = false;
    let dirty = false;
    let notificationTimer;

    function showSaveNotification(state, message, detail = '') {
        clearTimeout(notificationTimer);
        saveNotification.classList.remove('hidden');
        saveNotification.classList.add('flex');
        status.textContent = message;
        saveTime.textContent = detail;

        const styles = {
            pending: ['text-amber-700', 'bg-amber-500'],
            saving: ['text-blue-700', 'bg-blue-500'],
            success: ['text-emerald-700', 'bg-emerald-500'],
            error: ['text-red-700', 'bg-red-500'],
        };

        Object.values(styles).flat().forEach(className => {
            status.classList.remove(className);
            saveIndicator.classList.remove(className);
        });
        status.classList.add(styles[state][0]);
        saveIndicator.classList.add(styles[state][1]);

        if (state === 'success') {
            notificationTimer = setTimeout(() => {
                saveNotification.classList.add('hidden');
                saveNotification.classList.remove('flex');
            }, 2200);
        }
    }

    function setActiveSection(section, previewUrl) {
        tabs.forEach(btn => {
            const active = btn.dataset.section === section;
            btn.classList.toggle('bg-gray-900', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('border', !active);
            btn.classList.toggle('bg-white', !active);
        });

        panels.forEach(panel => {
            panel.classList.toggle('hidden', panel.dataset.sectionPanel !== section);
        });

        if (previewUrl) {
            frame.src = previewUrl;
            openPreview.href = previewUrl;
        }
    }

    function markDirty() {
        dirty = true;
        showSaveNotification('pending', 'Wijziging gedetecteerd', 'Wordt automatisch opgeslagen');
    }

    function scheduleSave() {
        clearTimeout(timer);
        markDirty();
        timer = setTimeout(saveContent, 800);
    }

    async function saveContent() {
        clearTimeout(timer);

        if (saving) {
            pendingSave = true;
            return;
        }

        saving = true;
        pendingSave = false;
        dirty = false;
        let failed = false;
        showSaveNotification('saving', 'Automatisch opslaan…');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });

            if (!response.ok) {
                throw new Error('Opslaan mislukt');
            }

            showSaveNotification(
                dirty ? 'pending' : 'success',
                dirty ? 'Nieuwe wijziging gedetecteerd' : 'Wijziging automatisch opgeslagen',
                dirty
                    ? 'Wordt hierna opgeslagen'
                    : `Opgeslagen om ${new Date().toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' })}`,
            );
            frame.src = frame.src;
        } catch (error) {
            failed = true;
            dirty = true;
            showSaveNotification('error', 'Automatisch opslaan mislukt', 'Wijzig opnieuw om het nogmaals te proberen');
        } finally {
            saving = false;

            if (!failed && (pendingSave || dirty)) {
                timer = setTimeout(saveContent, 500);
            }
        }
    }

    function searchableFields() {
        return [...form.querySelectorAll('input[name]:not([type="hidden"]):not([type="file"]), textarea[name]')];
    }

    function fieldLabel(field) {
        const container = field.closest('div');
        return container?.querySelector('label')?.textContent.trim()
            || field.name.replaceAll('_', ' ');
    }

    search.addEventListener('input', () => {
        const query = search.value.trim().toLocaleLowerCase('nl-NL');
        searchResult.replaceChildren();

        if (query.length < 2) {
            searchResult.classList.add('hidden');
            return;
        }

        const matches = searchableFields().filter(field => {
            const panel = field.closest('[data-section-panel]');
            const haystack = `${fieldLabel(field)} ${field.name} ${panel?.querySelector('h2')?.textContent || ''}`;
            return haystack.toLocaleLowerCase('nl-NL').includes(query);
        }).slice(0, 8);

        searchResult.classList.remove('hidden');
        searchResult.className = 'mt-2 grid gap-1 rounded-xl border bg-white p-2 text-xs shadow-lg';

        if (!matches.length) {
            searchResult.textContent = 'Geen velden gevonden.';
            return;
        }

        matches.forEach(field => {
            const panel = field.closest('[data-section-panel]');
            const section = panel.dataset.sectionPanel;
            const tab = [...tabs].find(item => item.dataset.section === section);
            const resultButton = document.createElement('button');
            resultButton.type = 'button';
            resultButton.className = 'rounded-lg px-3 py-2 text-left hover:bg-emerald-50';
            resultButton.innerHTML = `<span class="block font-semibold text-gray-800"></span><span class="text-gray-400"></span>`;
            resultButton.children[0].textContent = fieldLabel(field);
            resultButton.children[1].textContent = tab?.textContent.trim() || section;
            resultButton.addEventListener('click', () => {
                setActiveSection(section, tab?.dataset.preview);
                search.value = '';
                searchResult.classList.add('hidden');
                field.focus();
                field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                field.classList.add('ring-2', 'ring-emerald-400');
                setTimeout(() => field.classList.remove('ring-2', 'ring-emerald-400'), 1500);
            });
            searchResult.appendChild(resultButton);
        });
    });

    form.addEventListener('input', scheduleSave);
    form.addEventListener('change', event => {
        if (event.target.matches('input[type="file"], input[type="checkbox"]')) {
            scheduleSave();
        }
    });
    form.addEventListener('submit', event => {
        event.preventDefault();
        saveContent();
    });
    refreshBtn.addEventListener('click', () => { frame.src = frame.src; });
    tabs.forEach(btn => btn.addEventListener('click', () => setActiveSection(btn.dataset.section, btn.dataset.preview)));
    previewSizes.forEach(button => button.addEventListener('click', () => {
        frame.style.width = button.dataset.previewWidth;
        previewSizes.forEach(item => {
            const active = item === button;
            item.classList.toggle('bg-white', active);
            item.classList.toggle('shadow-sm', active);
            item.classList.toggle('text-gray-500', !active);
        });
    }));
    window.addEventListener('beforeunload', event => {
        if (!dirty && !saving) return;
        event.preventDefault();
        event.returnValue = '';
    });
</script>
@endpush
