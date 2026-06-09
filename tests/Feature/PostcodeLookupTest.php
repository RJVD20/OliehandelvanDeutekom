<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PostcodeLookupTest extends TestCase
{
    public function test_returns_street_city_and_province_for_valid_postcode(): void
    {
        Http::fake([
            'postcode.tech/*' => Http::response([
                'street'   => 'Kerkstraat',
                'city'     => 'Amsterdam',
                'province' => 'Noord-Holland',
            ], 200),
        ]);

        $response = $this->getJson('/api/postcode-lookup?postcode=1234AB&huisnummer=10');

        $response->assertOk()->assertJson([
            'straat'    => 'Kerkstraat',
            'stad'      => 'Amsterdam',
            'provincie' => 'Noord-Holland',
        ]);
    }

    public function test_returns_422_when_postcode_not_found(): void
    {
        Http::fake([
            'postcode.tech/*' => Http::response(null, 404),
        ]);

        $response = $this->getJson('/api/postcode-lookup?postcode=9999ZZ&huisnummer=1');

        $response->assertStatus(422)->assertJson([
            'message' => 'Postcode niet gevonden',
        ]);
    }

    public function test_returns_422_when_api_is_unreachable(): void
    {
        Http::fake([
            'postcode.tech/*' => Http::response(null, 500),
        ]);

        $response = $this->getJson('/api/postcode-lookup?postcode=1234AB&huisnummer=10');

        $response->assertStatus(422)->assertJson([
            'message' => 'Postcode niet gevonden',
        ]);
    }

    public function test_returns_validation_error_when_postcode_missing(): void
    {
        $response = $this->getJson('/api/postcode-lookup?huisnummer=10');

        $response->assertStatus(422);
    }

    public function test_returns_validation_error_when_huisnummer_missing(): void
    {
        $response = $this->getJson('/api/postcode-lookup?postcode=1234AB');

        $response->assertStatus(422);
    }
}
