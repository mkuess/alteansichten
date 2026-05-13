<?php

namespace App\Filament\Pages;

use App\Models\Municipality;
use App\Models\Place;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Storage;

class Karte extends Page
{
    protected static ?string $navigationLabel = 'Karte';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Inhalte';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.karte';

    protected static ?string $title = 'Karte';

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }

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
                'media_count'       => count($media),
                'media'             => $media,
                'edit_url'          => route('filament.admin.resources.places.edit', $place),
                'create_media_url'  => route('filament.admin.resources.media-items.create') . '?place_id=' . $place->id,
            ];
        })->values()->all();

        $municipalities = Municipality::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('public_profile_enabled', true)
            ->whereNotIn('status', ['hidden', 'archived'])
            ->withCount('places')
            ->with(['places' => function ($q) {
                $q->with(['primaryMediaItems' => function ($mq) {
                    $mq->where('status', 'approved')
                       ->select('id', 'title', 'file_path', 'year', 'type', 'primary_place_id')
                       ->limit(12);
                }])
                ->select('id', 'title', 'slug', 'municipality_id', 'latitude', 'longitude', 'status');
            }])
            ->get(['id', 'name', 'slug', 'latitude', 'longitude', 'logo_path', 'summary', 'postal_code']);

        $municipalitiesData = $municipalities->map(fn ($m) => [
            'id'          => $m->id,
            'name'        => $m->name,
            'slug'        => $m->slug,
            'lat'         => (float) $m->latitude,
            'lng'         => (float) $m->longitude,
            'logo_path'   => $m->logo_path,
            'summary'     => $m->summary,
            'postal_code' => $m->postal_code,
            'places_count' => $m->places_count,
            'places'      => $m->places->map(fn ($p) => [
                'id'    => $p->id,
                'title' => $p->title,
                'lat'   => (float) $p->latitude,
                'lng'   => (float) $p->longitude,
            ])->values()->toArray(),
            'media' => $m->places->flatMap(fn ($p) => $p->primaryMediaItems->map(fn ($mi) => [
                'id'        => $mi->id,
                'title'     => $mi->title,
                'file_path' => $mi->file_path,
                'year'      => $mi->year,
                'type'      => $mi->type,
            ]))->values()->toArray(),
        ])->values()->all();

        return ['mapData' => $mapData, 'municipalitiesData' => $municipalitiesData];
    }
}
