<?php

namespace App\Filament\Widgets;

use App\Models\MediaItem;
use App\Models\Place;
use App\Models\QrCode;
use App\Models\Submission;
use Filament\Widgets\Widget;

class MaintenanceAlertsWidget extends Widget
{
    protected static ?string $heading = 'Wartungshinweise';

    protected static string $view = 'filament.widgets.maintenance-alerts-widget';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    public function getViewData(): array
    {
        $alerts = [];

        $mediaWithoutLinks = MediaItem::whereDoesntHave('mediaLinks')->count();
        if ($mediaWithoutLinks > 0) {
            $alerts[] = [
                'level'   => 'warning',
                'icon'    => 'heroicon-o-photo',
                'message' => $mediaWithoutLinks . ' ' . ($mediaWithoutLinks === 1 ? 'Medium' : 'Medien') . ' ohne Standortverknüpfung',
                'action'  => '/admin/media-items',
                'label'   => 'Prüfen',
            ];
        }

        $placesWithoutMedia = Place::whereDoesntHave('placeMediaLinks')->count();
        if ($placesWithoutMedia > 0) {
            $alerts[] = [
                'level'   => 'info',
                'icon'    => 'heroicon-o-map-pin',
                'message' => $placesWithoutMedia . ' ' . ($placesWithoutMedia === 1 ? 'Standort' : 'Standorte') . ' ohne Medien',
                'action'  => '/admin/places',
                'label'   => 'Anzeigen',
            ];
        }

        $pendingSubmissions = Submission::whereIn('status', ['pending', 'open'])->count();
        if ($pendingSubmissions > 0) {
            $alerts[] = [
                'level'   => 'warning',
                'icon'    => 'heroicon-o-inbox-arrow-down',
                'message' => $pendingSubmissions . ' ' . ($pendingSubmissions === 1 ? 'Einreichung' : 'Einreichungen') . ' warten auf Prüfung',
                'action'  => '/admin/submissions',
                'label'   => 'Öffnen',
            ];
        }

        $placesWithoutQr = Place::whereDoesntHave('qrCode')->where('status', 'published')->count();
        if ($placesWithoutQr > 0) {
            $alerts[] = [
                'level'   => 'info',
                'icon'    => 'heroicon-o-qr-code',
                'message' => $placesWithoutQr . ' publizierte ' . ($placesWithoutQr === 1 ? 'Seite' : 'Seiten') . ' ohne QR-Code',
                'action'  => '/admin/places',
                'label'   => 'Anzeigen',
            ];
        }

        if (empty($alerts)) {
            $alerts[] = [
                'level'   => 'success',
                'icon'    => 'heroicon-o-check-circle',
                'message' => 'Keine offenen Wartungsaufgaben.',
                'action'  => null,
                'label'   => null,
            ];
        }

        return ['alerts' => $alerts];
    }
}
