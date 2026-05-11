<?php

namespace App\Filament\Resources\PlaceResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContentReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'contentReports';

    protected static ?string $title = 'Meldungen';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('report_type')
                    ->label('Meldegrund')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'rights_issue'          => 'Rechtehinweis',
                        'wrong_information'     => 'Falsche Information',
                        'privacy_concern'       => 'Datenschutzanliegen',
                        'duplicate'             => 'Duplikat',
                        'inappropriate_content' => 'Unangemessener Inhalt',
                        'technical_problem'     => 'Technisches Problem',
                        'other'                 => 'Sonstiges',
                        default                 => $state,
                    }),

                TextColumn::make('message')
                    ->label('Nachricht')
                    ->limit(50),

                TextColumn::make('reporter_name')
                    ->label('Melder')
                    ->limit(25),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'      => 'warning',
                        'in_review' => 'info',
                        'resolved'  => 'success',
                        'rejected'  => 'danger',
                        'archived'  => 'gray',
                        default     => 'gray',
                    }),

                IconColumn::make('rights_claim')
                    ->label('Rechtsanspruch')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Gemeldet')
                    ->dateTime('d.m.Y'),
            ])
            ->headerActions([])
            ->actions([]);
    }
}
