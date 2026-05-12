<?php

namespace App\Filament\Resources\MediaItemResource\Pages;

use App\Filament\Resources\MediaItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMediaItem extends EditRecord
{
    protected static string $resource = MediaItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
