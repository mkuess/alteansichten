<?php

namespace App\Filament\Resources\PlaceResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static ?string $title = 'Einreichungen';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titel')
                    ->limit(40),

                TextColumn::make('material_type')
                    ->label('Typ')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'image'         => 'Bild',
                        'document'      => 'Dokument',
                        'story'         => 'Geschichte',
                        'correction'    => 'Korrektur',
                        'location_hint' => 'Ortshinweis',
                        'other'         => 'Sonstiges',
                        default         => '—',
                    }),

                TextColumn::make('submitted_by_name')
                    ->label('Einsender')
                    ->limit(25),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'in_review' => 'info',
                        'accepted'  => 'success',
                        'rejected'  => 'danger',
                        'archived'  => 'gray',
                        default     => 'gray',
                    }),

                IconColumn::make('rights_confirmation')
                    ->label('Rechte')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Eingereicht')
                    ->dateTime('d.m.Y'),
            ])
            ->headerActions([])
            ->actions([]);
    }
}
