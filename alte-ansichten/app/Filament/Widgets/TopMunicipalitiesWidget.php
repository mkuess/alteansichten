<?php

namespace App\Filament\Widgets;

use App\Models\Municipality;
use Filament\Widgets\Widget;

class TopMunicipalitiesWidget extends Widget
{
    protected static ?string $heading = 'Gemeinden nach Standorten';

    protected static string $view = 'filament.widgets.top-municipalities-widget';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    protected function getViewData(): array
    {
        $municipalities = Municipality::withCount('places')
            ->orderByDesc('places_count')
            ->limit(8)
            ->get();

        $max = $municipalities->max('places_count') ?: 1;

        return [
            'municipalities' => $municipalities,
            'max'            => $max,
        ];
    }
}
