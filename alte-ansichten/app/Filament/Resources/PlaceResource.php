<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaceResource\Pages;
use App\Filament\Resources\PlaceResource\RelationManagers\ContentReportsRelationManager;
use App\Filament\Resources\PlaceResource\RelationManagers\QrCodeRelationManager;
use App\Filament\Resources\PlaceResource\RelationManagers\SubmissionsRelationManager;
use App\Models\Category;
use App\Models\Municipality;
use App\Models\Place;
use App\Services\QrCodeService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlaceResource extends Resource
{
    protected static ?string $model = Place::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Inhalte';

    protected static ?string $navigationLabel = 'Standorte';

    protected static ?string $modelLabel = 'Standort';

    protected static ?string $pluralModelLabel = 'Standorte';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Basisdaten')
                ->schema([
                    Select::make('municipality_id')
                        ->label('Gemeinde')
                        ->options(Municipality::pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),

                    Select::make('category_id')
                        ->label('Kategorie')
                        ->options(Category::orderBy('sort_order')->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),

                    TextInput::make('title')
                        ->label('Titel')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('summary')
                        ->label('Kurzbeschreibung')
                        ->maxLength(255),

                    Textarea::make('story')
                        ->label('Geschichte / Beschreibung')
                        ->rows(6),

                    TextInput::make('period_from')
                        ->label('Zeitraum von (Jahr)')
                        ->numeric(),

                    TextInput::make('period_to')
                        ->label('Zeitraum bis (Jahr)')
                        ->numeric(),

                    TextInput::make('approximate_date_text')
                        ->label('Ungefähre Datumsangabe')
                        ->maxLength(255),
                ]),

            Section::make('Adresse')
                ->schema([
                    TextInput::make('street')
                        ->label('Straße')
                        ->maxLength(255),

                    TextInput::make('house_number')
                        ->label('Hausnummer')
                        ->maxLength(255),

                    TextInput::make('postal_code')
                        ->label('Postleitzahl')
                        ->maxLength(20),

                    TextInput::make('address_text')
                        ->label('Adresse (Freitext)')
                        ->maxLength(255),
                ]),

            Section::make('Koordinaten')
                ->schema([
                    TextInput::make('latitude')
                        ->label('Breitengrad')
                        ->numeric(),

                    TextInput::make('longitude')
                        ->label('Längengrad')
                        ->numeric(),

                    TextInput::make('location_precision')
                        ->label('Koordinaten-Genauigkeit')
                        ->maxLength(255),

                    TextInput::make('location_note')
                        ->label('Standorthinweis')
                        ->maxLength(255),
                ]),

            Section::make('Veröffentlichung')
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft'     => 'Entwurf',
                            'pending'   => 'Ausstehend',
                            'published' => 'Veröffentlicht',
                            'archived'  => 'Archiviert',
                            'rejected'  => 'Abgelehnt',
                        ])
                        ->default('draft')
                        ->required(),

                    Select::make('visibility')
                        ->label('Sichtbarkeit')
                        ->options([
                            'public'   => 'Öffentlich',
                            'unlisted' => 'Nicht gelistet',
                            'private'  => 'Privat',
                        ])
                        ->default('public')
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
                TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('municipality.name')
                    ->label('Gemeinde')
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Kategorie')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'pending'   => 'warning',
                        'rejected'  => 'danger',
                        'archived'  => 'gray',
                        default     => 'gray',
                    }),

                TextColumn::make('visibility')
                    ->label('Sichtbarkeit')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public'   => 'success',
                        'unlisted' => 'warning',
                        'private'  => 'gray',
                        default    => 'gray',
                    }),

                TextColumn::make('period_from')
                    ->label('Von'),

                TextColumn::make('period_to')
                    ->label('Bis'),

                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('municipality')
                    ->label('Gemeinde')
                    ->relationship('municipality', 'name'),

                SelectFilter::make('category')
                    ->label('Kategorie')
                    ->relationship('category', 'name'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft'     => 'Entwurf',
                        'pending'   => 'Ausstehend',
                        'published' => 'Veröffentlicht',
                        'archived'  => 'Archiviert',
                        'rejected'  => 'Abgelehnt',
                    ]),

                SelectFilter::make('visibility')
                    ->label('Sichtbarkeit')
                    ->options([
                        'public'   => 'Öffentlich',
                        'unlisted' => 'Nicht gelistet',
                        'private'  => 'Privat',
                    ]),
            ])
            ->actions([
                Action::make('createQrCode')
                    ->label('QR-Code erstellen')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->action(function (Place $record) {
                        $result = app(QrCodeService::class)->createForPlace($record);

                        if ($result === false) {
                            Notification::make()
                                ->title('QR-Code bereits vorhanden')
                                ->warning()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('QR-Code erfolgreich erstellt')
                                ->success()
                                ->send();
                        }
                    }),
            ]);
    }

    public static function getRelationManagers(): array
    {
        return [
            QrCodeRelationManager::class,
            SubmissionsRelationManager::class,
            ContentReportsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPlaces::route('/'),
            'create' => Pages\CreatePlace::route('/create'),
            'edit'   => Pages\EditPlace::route('/{record}/edit'),
        ];
    }
}
