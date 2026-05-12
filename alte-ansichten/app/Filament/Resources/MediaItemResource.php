<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaItemResource\Pages;
use App\Filament\Resources\MediaItemResource\RelationManagers\MediaLinksRelationManager;
use App\Models\MediaItem;
use App\Models\Place;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
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
        return $form->schema([
            Section::make('Basisdaten')
                ->schema([
                    Select::make('primary_place_id')
                        ->label('Primärer Standort')
                        ->options(Place::pluck('title', 'id'))
                        ->searchable()
                        ->nullable(),

                    Select::make('type')
                        ->label('Typ')
                        ->options([
                            'image'            => 'Bild',
                            'document'         => 'Dokument',
                            'newspaper_article' => 'Zeitungsartikel',
                            'external_link'    => 'Externer Link',
                            'audio'            => 'Audio',
                            'video'            => 'Video',
                            'other'            => 'Sonstiges',
                        ])
                        ->default('image')
                        ->required(),

                    TextInput::make('title')
                        ->label('Titel')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Textarea::make('description')
                        ->label('Beschreibung')
                        ->rows(4),

                    TextInput::make('year')
                        ->label('Jahr')
                        ->numeric(),

                    TextInput::make('date_text')
                        ->label('Datumsangabe (Text)')
                        ->maxLength(255),
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

                    Textarea::make('rights_note')
                        ->label('Rechtehinweis')
                        ->rows(3),

                    Textarea::make('source_note')
                        ->label('Quellenangabe')
                        ->rows(3),
                ]),

            Section::make('Standort und Veröffentlichung')
                ->schema([
                    Select::make('location_status')
                        ->label('Standortstatus')
                        ->options([
                            'exact_place'       => 'Exakter Standort',
                            'approximate_area'  => 'Ungefähre Gegend',
                            'municipality_only' => 'Nur Gemeinde',
                            'unknown'           => 'Unbekannt',
                            'multiple_places'   => 'Mehrere Standorte',
                            'not_location_based' => 'Kein Standortbezug',
                        ])
                        ->default('unknown')
                        ->required(),

                    TextInput::make('location_note')
                        ->label('Standorthinweis')
                        ->maxLength(255),

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

                    TextInput::make('internal_reference_code')
                        ->label('Interne Referenz')
                        ->maxLength(50),
                ]),
        ]);
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
                    ->visibility('public')
                    ->checkFileExistence(false),

                TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Typ')
                    ->badge(),

                TextColumn::make('primaryPlace.title')
                    ->label('Standort')
                    ->sortable(),

                TextColumn::make('year')
                    ->label('Jahr')
                    ->sortable(),

                TextColumn::make('rights_status')
                    ->label('Rechtsstatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public_domain'      => 'success',
                        'permission_granted' => 'success',
                        'needs_review'       => 'warning',
                        'unknown'            => 'gray',
                        default              => 'info',
                    }),

                TextColumn::make('location_status')
                    ->label('Standortstatus')
                    ->badge(),

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

                SelectFilter::make('location_status')
                    ->label('Standortstatus')
                    ->options([
                        'exact_place'        => 'Exakter Standort',
                        'approximate_area'   => 'Ungefähre Gegend',
                        'municipality_only'  => 'Nur Gemeinde',
                        'unknown'            => 'Unbekannt',
                        'multiple_places'    => 'Mehrere Standorte',
                        'not_location_based' => 'Kein Standortbezug',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Ausstehend',
                        'approved' => 'Freigegeben',
                        'rejected' => 'Abgelehnt',
                        'hidden'   => 'Versteckt',
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
