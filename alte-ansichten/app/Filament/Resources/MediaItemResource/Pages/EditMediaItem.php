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
}
