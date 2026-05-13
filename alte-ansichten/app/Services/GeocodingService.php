<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    // Photon (by Komoot) — uses OpenStreetMap data, no API key required.
    // GeoJSON response: coordinates are [longitude, latitude].
    private const PHOTON_URL = 'https://photon.komoot.io/api/';
    private const TIMEOUT = 8;

    /**
     * Resolve coordinates from address parts.
     *
     * @param  array<string, string|null>  $parts  Keys: street, house_number, postal_code, city, country
     * @return array{latitude: float, longitude: float}|null
     */
    public function resolveCoordinates(array $parts): ?array
    {
        $query = $this->buildQuery($parts);

        if (empty($query)) {
            return null;
        }

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->withHeaders([
                    'User-Agent'      => 'AlteAnsichten/1.0',
                    'Accept-Language' => 'de,en',
                ])
                ->get(self::PHOTON_URL, [
                    'q'     => $query,
                    'limit' => 1,
                    'lang'  => 'de',
                ]);

            if (! $response->successful()) {
                Log::warning('GeocodingService: HTTP error', [
                    'query'  => $query,
                    'status' => $response->status(),
                ]);
                return null;
            }

            $body    = $response->json();
            $features = $body['features'] ?? [];

            if (empty($features)) {
                Log::info('GeocodingService: no results', ['query' => $query]);
                return null;
            }

            // GeoJSON coordinates: [longitude, latitude]
            $coords = $features[0]['geometry']['coordinates'] ?? null;
            if (! is_array($coords) || count($coords) < 2) {
                return null;
            }

            $lng = (float) $coords[0];
            $lat = (float) $coords[1];

            Log::info('GeocodingService: resolved', [
                'query'     => $query,
                'latitude'  => $lat,
                'longitude' => $lng,
            ]);

            return [
                'latitude'  => $lat,
                'longitude' => $lng,
            ];
        } catch (\Throwable $e) {
            Log::warning('GeocodingService: exception', [
                'query'   => $query,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function geocodeMunicipality(string $name, ?string $postalCode = null, string $country = 'Austria'): ?array
    {
        return $this->resolveCoordinates([
            'city'        => $name,
            'postal_code' => $postalCode,
            'country'     => $country,
        ]);
    }

    private function buildQuery(array $parts): string
    {
        $segments = [];

        $street = trim(($parts['street'] ?? '') . ' ' . ($parts['house_number'] ?? ''));
        if ($street !== '') {
            $segments[] = $street;
        }

        if (! empty($parts['postal_code'])) {
            $segments[] = $parts['postal_code'];
        }

        if (! empty($parts['city'])) {
            $segments[] = $parts['city'];
        }

        if (! empty($parts['country'])) {
            $segments[] = $parts['country'];
        }

        return implode(', ', $segments);
    }
}
