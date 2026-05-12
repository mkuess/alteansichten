<?php

namespace App\Filament\Resources\PlaceResource\Pages;

use App\Filament\Resources\PlaceResource;
use App\Models\Municipality;
use App\Services\GeocodingService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePlace extends CreateRecord
{
    protected static string $resource = PlaceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['title'] ?? '');

        if (empty($data['latitude']) && empty($data['longitude'])) {
            $coords = app(GeocodingService::class)->resolveCoordinates(
                $this->buildAddressParts($data)
            );

            if ($coords) {
                $data['latitude']  = $coords['latitude'];
                $data['longitude'] = $coords['longitude'];
            }
        }

        return $data;
    }

    private function buildAddressParts(array $data): array
    {
        $city = null;
        if (! empty($data['municipality_id'])) {
            $municipality = Municipality::find($data['municipality_id']);
            $city = $municipality?->name;
        }

        return [
            'street'       => $data['street'] ?? null,
            'house_number' => $data['house_number'] ?? null,
            'postal_code'  => $data['postal_code'] ?? null,
            'city'         => $city ?? ($data['address_text'] ?? null),
            'country'      => null,
        ];
    }
}
