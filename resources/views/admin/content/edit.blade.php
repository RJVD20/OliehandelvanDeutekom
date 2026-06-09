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
<div class="flex flex-col lg:flex-row gap-6">
    <aside class="w-full lg:w-[420px] xl:w-[460px] shrink-0">
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden sticky top-6">
            <div class="px-5 py-4 border-b">
                <h1 class="text-xl font-bold">CMS teksten</h1>
                <p class="text-xs text-gray-500 mt-1">Bewerk links, preview rechts</p>
            </div>

            <div class="px-3 py-3 border-b bg-gray-50">
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" data-section="home" data-preview="{{ route('home') }}" class="section-tab px-3 py-2 rounded-lg text-sm font-semibold bg-gray-900 text-white">Home</button>
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

            <form method="POST" action="{{ route('admin.content.update') }}" id="content-form" class="p-4 space-y-6" enctype="multipart/form-data">
                @csrf

                <section class="space-y-4" data-section-panel="home">
                    <h2 class="text-base font-semibold">Homepage</h2>
                    <div class="grid gap-4">
            <div>
                <label class="text-sm font-medium">Hero titel</label>
                <input type="text" name="home_hero_title" value="{{ $values['home_hero_title'] ?? '' }}" class="w-full rounded-lg border px-3 py-2">
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

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-green-600 text-white font-semibold">Opslaan</button>
                    <span id="save-status" class="text-sm text-gray-500"></span>
                </div>
            </form>
        </div>
    </aside>

    <section class="flex-1">
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden min-h-[720px]">
            <div class="px-4 py-3 border-b flex items-center justify-between">
                <div class="text-sm text-gray-600">Live preview</div>
                <button type="button" id="refresh-preview" class="text-sm px-3 py-1 rounded border">Ververs</button>
            </div>
            <iframe id="preview-frame" src="{{ route('home') }}" class="w-full h-[calc(100vh-180px)] bg-white"></iframe>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('content-form');
    const status = document.getElementById('save-status');
    const frame = document.getElementById('preview-frame');
    const refreshBtn = document.getElementById('refresh-preview');
    const tabs = document.querySelectorAll('.section-tab');
    const panels = document.querySelectorAll('[data-section-panel]');

    let timer;

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
        }
    }

    function autosave() {
        clearTimeout(timer);
        status.textContent = 'Opslaan...';
        timer = setTimeout(async () => {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData,
            });

            status.textContent = response.ok ? 'Opgeslagen' : 'Opslaan mislukt';
            if (response.ok) {
                frame.src = frame.src;
            }
        }, 600);
    }

    form.addEventListener('input', autosave);
    refreshBtn.addEventListener('click', () => { frame.src = frame.src; });
    tabs.forEach(btn => btn.addEventListener('click', () => setActiveSection(btn.dataset.section, btn.dataset.preview)));
</script>
@endpush
