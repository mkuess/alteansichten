<?php

namespace App\Filament\Resources\PlaceResource\Pages;

use App\Filament\Resources\PlaceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePlace extends CreateRecord
{
    protected static string $resource = PlaceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['title'] ?? '');

        return $data;
    }
}
