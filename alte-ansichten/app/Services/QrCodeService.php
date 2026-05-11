<?php

namespace App\Services;

use App\Models\Place;
use App\Models\QrCode;
use Endroid\QrCode\QrCode as QrCodeData;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Storage;

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

    public function generateImages(QrCode $qrCode): void
    {
        $url = url('/qr/' . $qrCode->code);

        Storage::disk('public')->makeDirectory('qr-codes');

        $qrData = new QrCodeData(data: $url, size: 300, margin: 10);

        $pngWriter = new PngWriter();
        $pngResult = $pngWriter->write($qrData);

        $svgWriter = new SvgWriter();
        $svgResult = $svgWriter->write($qrData);

        $pngRelPath = 'qr-codes/' . $qrCode->code . '.png';
        $svgRelPath = 'qr-codes/' . $qrCode->code . '.svg';

        Storage::disk('public')->put($pngRelPath, $pngResult->getString());
        Storage::disk('public')->put($svgRelPath, $svgResult->getString());

        $qrCode->update([
            'png_path' => 'storage/' . $pngRelPath,
            'svg_path' => 'storage/' . $svgRelPath,
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
