<?php

namespace App\Filament\Resources\MediaItemResource\Pages;

use App\Filament\Resources\MediaItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMediaItem extends CreateRecord
{
    protected static string $resource = MediaItemResource::class;

    public function mount(): void
    {
        parent::mount();

        $placeId = request()->query('place_id');

        if (filled($placeId) && is_numeric($placeId)) {
            $this->data['primary_context'] = 'place:' . (int) $placeId;
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->resolvePrimaryContext($data);
    }

    private function resolvePrimaryContext(array $data): array
    {
        $data['primary_place_id']        = null;
        $data['primary_municipality_id'] = null;
        $data['primary_district_id']     = null;

        $context = $data['primary_context'] ?? null;

        if (filled($context)) {
            [$type, $id] = explode(':', $context, 2);
            match ($type) {
                'place'        => $data['primary_place_id']        = $id,
                'municipality' => $data['primary_municipality_id'] = $id,
                'district'     => $data['primary_district_id']     = $id,
                default        => null,
            };
        }

        unset($data['primary_context']);

        return $data;
    }
}
