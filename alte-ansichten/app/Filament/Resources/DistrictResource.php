<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistrictResource\Pages;
use App\Models\District;
use App\Models\State;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Regionen';

    protected static ?string $navigationLabel = 'Bezirke';

    protected static ?string $modelLabel = 'Bezirk';

    protected static ?string $pluralModelLabel = 'Bezirke';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

        public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('state_id')
                ->label('Bundesland')
                ->options(State::pluck('name', 'id'))
                ->searchable()
                ->required(),

            TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            TextInput::make('code')
                ->label('Code')
                ->maxLength(20),

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
                TextColumn::make('state.name')
                    ->label('Bundesland')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),

                TextColumn::make('code')
                    ->label('Code'),

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
                SelectFilter::make('state')
                    ->label('Bundesland')
                    ->relationship('state', 'name'),

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
            'index'  => Pages\ListDistricts::route('/'),
            'create' => Pages\CreateDistrict::route('/create'),
            'edit'   => Pages\EditDistrict::route('/{record}/edit'),
        ];
    }
}
