<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Models\Country;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Regionen';

    protected static ?string $navigationLabel = 'Länder';

    protected static ?string $modelLabel = 'Land';

    protected static ?string $pluralModelLabel = 'Länder';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            TextInput::make('iso_code')
                ->label('ISO-Code')
                ->maxLength(2),

            Select::make('status')
                ->label('Status')
                ->options([
                    'published' => 'Veröffentlicht',
                    'draft'     => 'Entwurf',
                ])
                ->default('published')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),

                TextColumn::make('iso_code')
                    ->label('ISO-Code'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        default     => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'published' => 'Veröffentlicht',
                        'draft'     => 'Entwurf',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit'   => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
