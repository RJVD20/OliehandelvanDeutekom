<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PostcodeLookupController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'postcode'   => ['required', 'string'],
            'huisnummer' => ['required', 'string'],
        ]);

        $postcode   = strtoupper(str_replace(' ', '', $request->postcode));
        $huisnummer = $request->huisnummer;

        $response = Http::timeout(5)
            ->withToken(config('services.postcode_tech.key'))
            ->get('https://postcode.tech/api/v1/postcode/full', [
                'postcode' => $postcode,
                'number'   => $huisnummer,
            ]);

        if (! $response->successful() || empty($response->json('street'))) {
            return response()->json(['message' => 'Postcode niet gevonden'], 422);
        }

        $data = $response->json();

        return response()->json([
            'straat'    => $data['street'],
            'stad'      => $data['city'],
            'provincie' => $data['province'],
        ]);
    }
}
