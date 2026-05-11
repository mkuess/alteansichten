<?php

namespace App\Filament\Resources\ContentReportResource\Pages;

use App\Filament\Resources\ContentReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentReports extends ListRecords
{
    protected static string $resource = ContentReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
