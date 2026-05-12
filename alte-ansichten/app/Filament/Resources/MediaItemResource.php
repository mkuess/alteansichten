<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaItemResource\Pages;
use App\Filament\Resources\MediaItemResource\RelationManagers\MediaLinksRelationManager;
use App\Models\District;
use App\Models\MediaItem;
use App\Models\Municipality;
use App\Models\Place;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MediaItemResource extends Resource
{
    protected static ?string $model = MediaItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Inhalte';
    protected static ?string $navigationLabel = 'Medien';
    protected static ?string $modelLabel = 'Medienelement';
    protected static ?string $pluralModelLabel = 'Medien';
    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        // Build grouped options: Standorte / Gemeinden / Bezirke
        $places = Place::with('municipality')
            ->orderBy('title')
            ->get()
            ->map(fn($p) => [
                'value' => 'place:' . $p->id,
                'label' => $p->title . ($p->municipality ? ' (' . $p->municipality->name . ')' : ''),
            ]);

        $municipalities = Municipality::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($m) => [
                'value' => 'municipality:' . $m->id,
                'label' => $m->name,
            ]);

        $districts = District::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($d) => [
                'value' => 'district:' . $d->id,
                'label' => $d->name,
            ]);

        $groupedOptions = [];
        if ($places->isNotEmpty()) {
            foreach ($places as $p) {
                $groupedOptions['Standorte'][$p['value']] = $p['label'];
            }
        }
        if ($municipalities->isNotEmpty()) {
            foreach ($municipalities as $m) {
                $groupedOptions['Gemeinden'][$m['value']] = $m['label'];
            }
        }
        if ($districts->isNotEmpty()) {
            foreach ($districts as $d) {
                $groupedOptions['Bezirke'][$d['value']] = $d['label'];
            }
        }

        return $form->schema([

            Section::make('Hauptbezug')
                ->description('Wem gehört dieses Medium hauptsächlich?')
                ->schema([
                    Select::make('primary_context')
                        ->label('Standort / Gemeinde / Bezirk')
                        ->options($groupedOptions)
                        ->searchable()
                        ->nullable()
                        ->placeholder('Standort, Gemeinde oder Bezirk wählen…')
                        ->afterStateHydrated(function (Set $set, $state, $record) {
                            if (!$record) return;
                            if ($record->primary_place_id)
                                $set('primary_context', 'place:' . $record->primary_place_id);
                            elseif ($record->primary_municipality_id)
                                $set('primary_context', 'municipality:' . $record->primary_municipality_id);
                            elseif ($record->primary_district_id)
                                $set('primary_context', 'district:' . $record->primary_district_id);
                        })
                        ->dehydrated(false) // nicht direkt speichern
                        ->live(),

                    Hidden::make('primary_place_id'),
                    Hidden::make('primary_municipality_id'),
                    Hidden::make('primary_district_id'),

                    Select::make('type')
                        ->label('Typ')
                        ->options([
                            'image'             => 'Bild / Foto',
                            'document'          => 'Dokument',
                            'newspaper_article' => 'Zeitungsartikel',
                            'external_link'     => 'Externer Link',
                            'audio'             => 'Audio',
                            'video'             => 'Video',
                            'other'             => 'Sonstiges',
                        ])
                        ->default('image')
                        ->required(),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending'  => 'Ausstehend',
                            'approved' => 'Freigegeben',
                            'rejected' => 'Abgelehnt',
                            'hidden'   => 'Versteckt',
                        ])
                        ->default('pending')
                        ->required(),
                ]),

            Section::make('Details')
                ->schema([
                    TextInput::make('year')
                        ->label('Jahr')
                        ->numeric()
                        ->minValue(1800)
                        ->maxValue(2100),

                    TextInput::make('date_text')
                        ->label('Ungefähres Datum')
                        ->placeholder('z.B. um 1935, zwischen 1920–1930')
                        ->helperText('Nur ausfüllen wenn das genaue Jahr unbekannt ist')
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label('Beschreibung')
                        ->rows(3),
                ]),

            Section::make('Datei oder externer Link')
                ->schema([
                    FileUpload::make('file_path')
                        ->label('Datei hochladen')
                        ->disk('public')
                        ->directory('media-items')
                        ->acceptedFileTypes([
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            'application/pdf',
                        ])
                        ->maxSize(10240)
                        ->nullable(),

                    TextInput::make('external_url')
                        ->label('Externe URL')
                        ->url()
                        ->maxLength(500),
                ]),

            Section::make('Rechte und Quelle')
                ->schema([
                    Select::make('rights_status')
                        ->label('Rechtsstatus')
                        ->options([
                            'uploader_confirmed' => 'Uploader bestätigt',
                            'own_photo'          => 'Eigenes Foto',
                            'family_collection'  => 'Familiensammlung',
                            'permission_granted' => 'Genehmigung erhalten',
                            'public_domain'      => 'Gemeinfrei',
                            'unknown'            => 'Unbekannt',
                            'needs_review'       => 'Prüfung erforderlich',
                        ])
                        ->default('needs_review')
                        ->required(),

                    Textarea::make('source_note')
                        ->label('Quellenangabe')
                        ->placeholder('z.B. Privatarchiv Familie Huber, Gemeindearchiv Mödling')
                        ->rows(2),

                    Textarea::make('rights_note')
                        ->label('Rechtehinweis')
                        ->rows(2),
                ]),
        ]);
    }

    // Beim Speichern primary_context aufdröseln
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        return static::resolveContext($data);
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        return static::resolveContext($data);
    }

    private static function resolveContext(array $data): array
    {
        $data['primary_place_id']        = null;
        $data['primary_municipality_id'] = null;
        $data['primary_district_id']     = null;

        if (!empty($data['primary_context'])) {
            [$type, $id] = explode(':', $data['primary_context'], 2);
            match ($type) {
                'place'        => $data['primary_place_id']        = $id,
                'municipality' => $data['primary_municipality_id'] = $id,
                'district'     => $data['primary_district_id']     = $id,
                default        => null,
            };
        }

        unset($data['primary_context']);
        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Vorschau')
                    ->disk('public')
                    ->height(48)
                    ->width(64)
                    ->defaultImageUrl(null)
                    ->extraImgAttributes(['class' => 'object-cover rounded'])
                    ->checkFileExistence(false),

                TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Typ')
                    ->badge(),

                TextColumn::make('primary_context_label')
                    ->label('Hauptbezug')
                    ->getStateUsing(fn ($record) => $record->primary_context_label),

                TextColumn::make('year')
                    ->label('Jahr')
                    ->sortable(),

                TextColumn::make('rights_status')
                    ->label('Rechtsstatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public_domain', 'permission_granted' => 'success',
                        'needs_review'                        => 'warning',
                        default                               => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending'  => 'warning',
                        'rejected' => 'danger',
                        'hidden'   => 'gray',
                        default    => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Typ')
                    ->options([
                        'image'             => 'Bild',
                        'document'          => 'Dokument',
                        'newspaper_article' => 'Zeitungsartikel',
                        'external_link'     => 'Externer Link',
                        'audio'             => 'Audio',
                        'video'             => 'Video',
                        'other'             => 'Sonstiges',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Ausstehend',
                        'approved' => 'Freigegeben',
                        'rejected' => 'Abgelehnt',
                        'hidden'   => 'Versteckt',
                    ]),

                SelectFilter::make('rights_status')
                    ->label('Rechtsstatus')
                    ->options([
                        'uploader_confirmed' => 'Uploader bestätigt',
                        'own_photo'          => 'Eigenes Foto',
                        'family_collection'  => 'Familiensammlung',
                        'permission_granted' => 'Genehmigung erhalten',
                        'public_domain'      => 'Gemeinfrei',
                        'unknown'            => 'Unbekannt',
                        'needs_review'       => 'Prüfung erforderlich',
                    ]),
            ]);
    }

    public static function getRelationManagers(): array
    {
        return [
            MediaLinksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMediaItems::route('/'),
            'create' => Pages\CreateMediaItem::route('/create'),
            'edit'   => Pages\EditMediaItem::route('/{record}/edit'),
        ];
    }
}
