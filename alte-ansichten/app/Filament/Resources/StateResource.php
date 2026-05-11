<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StateResource\Pages;
use App\Models\Country;
use App\Models\State;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StateResource extends Resource
{
    protected static ?string $model = State::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Regionen';

    protected static ?string $navigationLabel = 'Bundesländer';

    protected static ?string $modelLabel = 'Bundesland';

    protected static ?string $pluralModelLabel = 'Bundesländer';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('country_id')
                ->label('Land')
                ->options(Country::pluck('name', 'id'))
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
                ->maxLength(10),

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
                TextColumn::make('country.name')
                    ->label('Land')
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
                SelectFilter::make('country')
                    ->label('Land')
                    ->relationship('country', 'name'),

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
            'index'  => Pages\ListStates::route('/'),
            'create' => Pages\CreateState::route('/create'),
            'edit'   => Pages\EditState::route('/{record}/edit'),
        ];
    }
}
