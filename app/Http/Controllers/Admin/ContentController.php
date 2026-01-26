<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContentController extends Controller
{
    private function defaults(): array
    {
        return [
            // Home
            'home_hero_title' => 'Bakker Brandstoffen in Den Helder',
            'home_hero_intro' => 'Al meer dan 75 jaar een begrip in Den Helder en omstreken. Voor advies en service op gebied van gasapparatuur zoals propaan gaskachels, kooktoestellen, campingartikelen, lasapparatuur, barbecues en terrasverwarming.',
            'home_hero_cta_label' => 'Naar de webshop',
            'home_products_title' => 'Populaire producten',
            'home_products_intro' => 'Rustige, zorgvuldig gekozen items met focus op kwaliteit en betrouwbaarheid.',
            'home_categories_title' => 'Shop per categorie',
            'home_faq_title' => 'Veelgestelde vragen',
            'home_faq_1_q' => 'Wat zijn de bezorgmogelijkheden?',
            'home_faq_1_a' => 'We stemmen de bezorging in overleg met je af. Zo weet je vooraf waar je aan toe bent en wanneer we kunnen leveren.',
            'home_faq_2_q' => 'Hoe snel wordt mijn bestelling geleverd?',
            'home_faq_2_a' => 'De levertijd hangt af van je locatie en de routeplanning. Na je bestelling nemen we contact op om een passend moment te plannen.',
            'home_faq_3_q' => 'Welke betaalmethoden kan ik gebruiken?',
            'home_faq_3_a' => 'Je kunt veilig afrekenen via de beschikbare betaalopties in de checkout. Indien van toepassing bespreken we dit ook bij levering.',
            'home_faq_4_q' => 'Kan ik ook afhalen?',
            'home_faq_4_a' => 'Afhalen is mogelijk bij onze ophaallocaties. Bekijk de locatiespagina voor actuele informatie.',
            'home_faq_5_q' => 'Hoe werkt retourneren?',
            'home_faq_5_a' => 'Niet tevreden? Neem contact met ons op, dan kijken we samen naar een passende oplossing volgens de geldende voorwaarden.',

            // Informatie
            'informatie_title' => 'De ideale verwarmingsoplossing: De pelletkachel',
            'informatie_intro' => 'Een snelle vergelijking van populaire kacheltypes, met focus op rendement, gebruiksgemak en betrouwbaarheid.',
            'informatie_block_1_title' => 'De ideale verwarmingsoplossing',
            'informatie_block_1_text' => 'In de zoektocht naar de ideale verwarmingsoplossing voor je woonkamer, serre, tuinhuis of andere ruimtes, sta je voor de keuze tussen verschillende soorten kachels. Laserkachels, kouskachels, gaskachels en pelletkachels bieden elk unieke voordelen.',
            'informatie_block_2_title' => 'Laserkachels (Zibro & Qlima)',
            'informatie_block_2_text' => 'Laserkachels beschikken over een elektronisch gestuurde brander voor snelle opstart en nauwkeurige temperatuurregeling. Met thermostaat en timer stel je eenvoudig de gewenste warmte in. Ze werken efficiënt en geurloos met Petroleum C of witte GTL.',
            'informatie_block_3_title' => 'Kouskachels',
            'informatie_block_3_text' => 'Kouskachels werken zonder externe stroombron en zijn ideaal voor hobbyruimtes, schuren of campinggebruik. De verbranding is zichtbaar en de ontsteking gebeurt via een gloeispiraal op batterijen, waardoor de kachel blijft werken bij stroomuitval.',
            'informatie_block_4_title' => 'Gevelkachels',
            'informatie_block_4_text' => 'Gevelkachels van Zibro (Toyotomi) maken gebruik van een “pijp-in-een-pijp” rookafvoersysteem. Hiermee kunnen ruimtes tot 450 m³ veilig worden verwarmd zonder uitlaatgassen in de kamer.',
            'informatie_block_5_title' => 'De pelletkachel',
            'informatie_block_5_text' => 'Een pelletkachel brandt op houtpellets en regelt automatisch de toevoer. Dankzij het hoge rendement – tot wel 97% – verbruik je minder brandstof voor dezelfde hoeveelheid warmte.',
            'informatie_block_6_title' => 'Efficiënt, modern en betrouwbaar',
            'informatie_block_6_text' => 'Pelletkachels zijn moderne, computergestuurde apparaten die automatisch temperatuur, pellettoevoer en rookgasafvoer regelen. Bij storingen staat Oliehandel van Deutekom klaar met service op maat.',

            // Over ons
            'over_hero_title' => 'Betrouwbare warmte, eerlijk advies',
            'over_hero_intro' => 'Oliehandel van Deutekom is een familiebedrijf met passie voor warmte-oplossingen. We leveren kachelvloeistoffen, kachels en toebehoren met focus op kwaliteit, duidelijke communicatie en service.',
            'over_usp_1_title' => 'Bezorging op afspraak',
            'over_usp_1_text' => 'We stemmen de levering af zodat je weet waar je aan toe bent.',
            'over_usp_2_title' => 'Klantgericht advies',
            'over_usp_2_text' => 'Hulp nodig bij de juiste keuze? We denken graag met je mee.',
            'over_usp_3_title' => 'Kwaliteit & transparantie',
            'over_usp_3_text' => 'Heldere informatie, eerlijke prijzen en producten waar we achter staan.',
            'over_usp_4_title' => 'Voor particulieren & bedrijven',
            'over_usp_4_text' => 'Voor elke situatie een passende oplossing—van brandstof tot toebehoren.',
            'over_story_1_title' => 'Wat je van ons mag verwachten',
            'over_story_1_text_1' => 'We houden het graag simpel: goede producten, duidelijke afspraken en service. Of je nu zoekt naar kachelvloeistof, een kachel of de juiste toebehoren— wij helpen je op weg.',
            'over_story_1_text_2' => 'We werken met een overzichtelijk assortiment en kijken met je mee naar de beste keuze voor jouw situatie.',
            'over_story_2_title' => 'Duurzaamheid en hergebruik',
            'over_story_2_text_1' => 'We streven naar zo min mogelijk verspilling en denken graag mee in praktische oplossingen. Heb je vragen over gebruik, opslag of de beste toepassing van producten? Neem contact op.',
            'over_story_2_text_2' => 'Meer weten? Bekijk ook onze informatiepagina met uitleg over verschillende soorten kachels.',
            'over_cta_title' => 'Heb je een vraag?',
            'over_cta_text' => 'Mail ons gerust. We reageren zo snel mogelijk.',
            'over_cta_button' => 'Neem contact op',

            // Locaties
            'locaties_title' => 'Ophaallocaties',
            'locaties_intro' => 'Hier vind je al onze afhaallocaties met adres, openingstijden en een kaart.',
        ];
    }

    private array $fields = [
        // Home
        'home_hero_title',
        'home_hero_intro',
        'home_hero_cta_label',
        'home_products_title',
        'home_products_intro',
        'home_categories_title',
        'home_faq_title',
        'home_faq_1_q',
        'home_faq_1_a',
        'home_faq_2_q',
        'home_faq_2_a',
        'home_faq_3_q',
        'home_faq_3_a',
        'home_faq_4_q',
        'home_faq_4_a',
        'home_faq_5_q',
        'home_faq_5_a',

        // Informatie
        'informatie_title',
        'informatie_intro',
        'informatie_block_1_title',
        'informatie_block_1_text',
        'informatie_block_2_title',
        'informatie_block_2_text',
        'informatie_block_3_title',
        'informatie_block_3_text',
        'informatie_block_4_title',
        'informatie_block_4_text',
        'informatie_block_5_title',
        'informatie_block_5_text',
        'informatie_block_6_title',
        'informatie_block_6_text',

        // Over ons
        'over_hero_title',
        'over_hero_intro',
        'over_usp_1_title',
        'over_usp_1_text',
        'over_usp_2_title',
        'over_usp_2_text',
        'over_usp_3_title',
        'over_usp_3_text',
        'over_usp_4_title',
        'over_usp_4_text',
        'over_story_1_title',
        'over_story_1_text_1',
        'over_story_1_text_2',
        'over_story_2_title',
        'over_story_2_text_1',
        'over_story_2_text_2',
        'over_cta_title',
        'over_cta_text',
        'over_cta_button',

        // Locaties
        'locaties_title',
        'locaties_intro',
    ];

    public function edit(): View
    {
        $defaults = $this->defaults();
        $values = [];
        foreach ($this->fields as $key) {
            $stored = Setting::get($key, '');
            $values[$key] = $stored !== '' ? $stored : ($defaults[$key] ?? '');
        }

        $locations = Location::orderBy('name')->get();

        return view('admin.content.edit', compact('values', 'locations'));
    }

    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->only($this->fields);

        foreach ($data as $key => $value) {
            Setting::set($key, is_string($value) ? trim($value) : $value);
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('toast', 'Teksten opgeslagen');
    }
}
