<?php

namespace App\Services;

use App\Models\Place;
use App\Models\QrCode;

class QrCodeService
{
    public function createForPlace(Place $place): QrCode|false
    {
        if ($place->qrCode()->exists()) {
            return false;
        }

        $code = $this->generateCode($place);

        return QrCode::create([
            'place_id'   => $place->id,
            'code'       => $code,
            'target_url' => '/orte/' . $place->slug,
            'scan_count' => 0,
        ]);
    }

    private function generateCode(Place $place): string
    {
        $base = $place->slug ?: 'place-' . $place->id;

        if (! QrCode::where('code', $base)->exists()) {
            return $base;
        }

        return $base . '-' . substr(uniqid(), -6);
    }
}
