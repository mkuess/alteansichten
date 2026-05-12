<?php

namespace App\Filament\Widgets;

use App\Models\MediaItem;
use App\Models\Place;
use App\Models\Submission;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $now    = Carbon::now();
        $week   = $now->copy()->subDays(7);
        $week2  = $now->copy()->subDays(14);

        $placesTotal      = Place::count();
        $placesThisWeek   = Place::where('created_at', '>=', $week)->count();
        $placesLastWeek   = Place::whereBetween('created_at', [$week2, $week])->count();
        $placesDelta      = $placesThisWeek - $placesLastWeek;

        $mediaTotal       = MediaItem::count();
        $mediaThisWeek    = MediaItem::where('created_at', '>=', $week)->count();
        $mediaLastWeek    = MediaItem::whereBetween('created_at', [$week2, $week])->count();
        $mediaDelta       = $mediaThisWeek - $mediaLastWeek;

        $submissionsOpen  = Submission::whereIn('status', ['pending', 'open'])->count();

        $usersTotal       = User::count();
        $usersThisWeek    = User::where('created_at', '>=', $week)->count();

        return [
            Stat::make('Standorte', number_format($placesTotal, 0, ',', '.'))
                ->description($this->deltaLabel($placesDelta) . ' / 7 Tage')
                ->descriptionIcon($placesDelta >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($placesDelta >= 0 ? 'success' : 'warning')
                ->icon('heroicon-o-map-pin'),

            Stat::make('Medien', number_format($mediaTotal, 0, ',', '.'))
                ->description($this->deltaLabel($mediaDelta) . ' / 7 Tage')
                ->descriptionIcon($mediaDelta >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($mediaDelta >= 0 ? 'success' : 'warning')
                ->icon('heroicon-o-photo'),

            Stat::make('Einreichungen offen', $submissionsOpen)
                ->description($submissionsOpen > 0 ? 'Warten auf Prüfung' : 'Keine offenen Einreichungen')
                ->descriptionIcon($submissionsOpen > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-circle')
                ->color($submissionsOpen > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-inbox-arrow-down'),

            Stat::make('Nutzer', $usersTotal)
                ->description(($usersThisWeek > 0 ? '+' . $usersThisWeek : 'Keine neuen') . ' diese Woche')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('gray')
                ->icon('heroicon-o-users'),
        ];
    }

    private function deltaLabel(int $delta): string
    {
        if ($delta > 0) {
            return '+' . $delta;
        }
        if ($delta < 0) {
            return (string) $delta;
        }

        return '±0';
    }
}
