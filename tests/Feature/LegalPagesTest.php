<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    public function test_legal_pages_are_publicly_available(): void
    {
        $pages = [
            'privacy' => 'Privacyverklaring',
            'terms' => 'Algemene voorwaarden',
            'returns' => 'Retourneren en herroepingsrecht',
            'cookies' => 'Cookieverklaring',
        ];

        foreach ($pages as $route => $heading) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee($heading)
                ->assertSee('info@kachelvloeistof.nl');
        }
    }

    public function test_footer_links_to_all_legal_pages(): void
    {
        $response = $this->get(route('home'))->assertOk();

        foreach (['privacy', 'terms', 'returns', 'cookies'] as $route) {
            $response->assertSee(route($route), false);
        }
    }
}
