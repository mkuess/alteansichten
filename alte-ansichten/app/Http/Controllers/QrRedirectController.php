<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Date;

class QrRedirectController extends Controller
{
    public function redirect(string $code): RedirectResponse
    {
        $qrCode = QrCode::where('code', $code)->first();

        if (! $qrCode) {
            abort(404);
        }

        $qrCode->increment('scan_count');
        $qrCode->update(['last_scanned_at' => Date::now()]);

        return redirect($qrCode->target_url);
    }
}
