<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    private const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/search';
    private const TIMEOUT = 5;

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
                    'User-Agent' => 'AlteAnsichten/1.0 (historical archive; contact@example.com)',
                    'Accept-Language' => 'de,en',
                ])
                ->get(self::NOMINATIM_URL, [
                    'q'      => $query,
                    'format' => 'json',
                    'limit'  => 1,
                ]);

            if (! $response->successful()) {
                return null;
            }

            $results = $response->json();

            if (empty($results) || ! isset($results[0]['lat'], $results[0]['lon'])) {
                return null;
            }

            return [
                'latitude'  => (float) $results[0]['lat'],
                'longitude' => (float) $results[0]['lon'],
            ];
        } catch (\Throwable $e) {
            Log::warning('GeocodingService: geocoding failed', [
                'query'   => $query,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
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
