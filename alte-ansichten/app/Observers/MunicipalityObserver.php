<?php

namespace App\Observers;

use App\Models\Municipality;
use App\Services\GeocodingService;

class MunicipalityObserver
{
    public function saving(Municipality $municipality): void
    {
        if (!empty($municipality->latitude) && !empty($municipality->longitude)) {
            return;
        }

        if (empty($municipality->name)) {
            return;
        }

        $service = app(GeocodingService::class);
        $coords = $service->geocodeMunicipality(
            $municipality->name,
            $municipality->postal_code
        );

        if ($coords) {
            $municipality->latitude  = $coords['latitude'];
            $municipality->longitude = $coords['longitude'];
        }
    }
}
