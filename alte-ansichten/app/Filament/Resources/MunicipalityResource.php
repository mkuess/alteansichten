<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MunicipalityResource\Pages;
use App\Models\District;
use App\Models\Municipality;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MunicipalityResource extends Resource
{
    protected static ?string $model = Municipality::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Regionen';

    protected static ?string $navigationLabel = 'Gemeinden';

    protected static ?string $modelLabel = 'Gemeinde';

    protected static ?string $pluralModelLabel = 'Gemeinden';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

        public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('district_id')
                ->label('Bezirk')
                ->options(District::pluck('name', 'id'))
                ->searchable()
                ->nullable(),

            TextInput::make('name')
                ->label('Name')
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

            Textarea::make('description')
                ->label('Beschreibung')
                ->rows(5),

            FileUpload::make('logo_path')
                ->label('Gemeindewappen')
                ->disk('public')
                ->directory('municipalities/logos')
                ->image()
                ->imagePreviewHeight('80')
                ->maxSize(2048)
                ->nullable(),

            TextInput::make('postal_code')
                ->label('Postleitzahl')
                ->maxLength(20),

            TextInput::make('latitude')
                ->label('Breitengrad')
                ->numeric(),

            TextInput::make('longitude')
                ->label('Längengrad')
                ->numeric(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'draft'     => 'Entwurf',
                    'published' => 'Veröffentlicht',
                    'archived'  => 'Archiviert',
                ])
                ->default('draft')
                ->required(),

            Toggle::make('public_profile_enabled')
                ->label('Öffentliches Profil aktiviert')
                ->default(true),

            TextInput::make('internal_reference_code')
                ->label('Interne Referenz')
                ->maxLength(50),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('district.name')
                    ->label('Bezirk')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),

                TextColumn::make('postal_code')
                    ->label('PLZ')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'archived'  => 'warning',
                        default     => 'gray',
                    }),

                IconColumn::make('public_profile_enabled')
                    ->label('Öffentlich')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('district')
                    ->label('Bezirk')
                    ->relationship('district', 'name'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft'     => 'Entwurf',
                        'published' => 'Veröffentlicht',
                        'archived'  => 'Archiviert',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMunicipalities::route('/'),
            'create' => Pages\CreateMunicipality::route('/create'),
            'edit'   => Pages\EditMunicipality::route('/{record}/edit'),
        ];
    }
}
