<?php

namespace App\Filament\Pages;

use App\Models\Place;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class Karte extends Page
{
    protected static ?string $navigationLabel = 'Karte';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Inhalte';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.karte';

    protected static ?string $title = 'Karte';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public function getViewData(): array
    {
        $places = Place::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['municipality.district', 'primaryMediaItems'])
            ->get();

        $mapData = $places->map(function (Place $place) {
            $media = $place->primaryMediaItems->map(function ($item) {
                $thumbUrl = null;
                if ($item->file_path) {
                    $thumbUrl = Storage::disk('public')->url($item->file_path);
                }

                return [
                    'id'        => $item->id,
                    'title'     => $item->title,
                    'year'      => $item->year,
                    'status'    => $item->status,
                    'thumb_url' => $thumbUrl,
                    'edit_url'  => route('filament.admin.resources.media-items.edit', $item),
                ];
            })->values()->all();

            return [
                'id'           => $place->id,
                'title'        => $place->title,
                'lat'          => (float) $place->latitude,
                'lng'          => (float) $place->longitude,
                'municipality' => $place->municipality?->name,
                'district'     => $place->municipality?->district?->name,
                'street'       => $place->street,
                'house_number' => $place->house_number,
                'postal_code'  => $place->postal_code,
                'address_text' => $place->address_text,
                'media_count'  => count($media),
                'media'        => $media,
                'edit_url'     => route('filament.admin.resources.places.edit', $place),
            ];
        })->values()->all();

        return ['mapData' => $mapData];
    }
}
