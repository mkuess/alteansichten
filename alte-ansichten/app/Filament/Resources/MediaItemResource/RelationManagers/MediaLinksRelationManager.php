<?php

namespace App\Filament\Resources\MediaItemResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaLinksRelationManager extends RelationManager
{
    protected static string $relationship = 'mediaLinks';

    protected static ?string $title = 'Medienverknüpfungen';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('linkable_type')
                ->label('Verknüpfungstyp')
                ->options([
                    'place'        => 'Standort (Place)',
                    'municipality' => 'Gemeinde (Municipality)',
                ])
                ->required(),

            TextInput::make('linkable_id')
                ->label('Verknüpfungs-ID')
                ->numeric()
                ->required()
                ->helperText('Standort-ID wenn Typ = place; Gemeinde-ID wenn Typ = municipality.'),

            Select::make('relationship_type')
                ->label('Beziehungstyp')
                ->options([
                    'main_subject'   => 'Hauptmotiv',
                    'panorama_of'    => 'Panorama von',
                    'shows_detail'   => 'Zeigt Detail',
                    'related'        => 'Verwandt',
                    'document_about' => 'Dokument über',
                    'source_for'     => 'Quelle für',
                ])
                ->nullable(),

            Toggle::make('is_primary')
                ->label('Primäre Verknüpfung')
                ->default(false),

            TextInput::make('sort_order')
                ->label('Reihenfolge')
                ->numeric()
                ->default(0),

            TextInput::make('period_from')
                ->label('Zeitraum von (Jahr)')
                ->numeric(),

            TextInput::make('period_to')
                ->label('Zeitraum bis (Jahr)')
                ->numeric(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('linkable_type')
                    ->label('Typ')
                    ->badge(),

                TextColumn::make('linkable_id')
                    ->label('ID'),

                TextColumn::make('relationship_type')
                    ->label('Beziehung'),

                IconColumn::make('is_primary')
                    ->label('Primär')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('#'),

                TextColumn::make('period_from')
                    ->label('Von'),

                TextColumn::make('period_to')
                    ->label('Bis'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
