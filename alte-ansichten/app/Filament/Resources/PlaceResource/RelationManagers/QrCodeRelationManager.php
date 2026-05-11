<?php

namespace App\Filament\Resources\PlaceResource\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QrCodeRelationManager extends RelationManager
{
    protected static string $relationship = 'qrCode';

    protected static ?string $title = 'QR-Code';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('code')
                ->label('Code')
                ->required()
                ->maxLength(255),

            TextInput::make('target_url')
                ->label('Ziel-URL')
                ->required()
                ->maxLength(500),

            TextInput::make('png_path')
                ->label('PNG-Pfad')
                ->maxLength(500),

            TextInput::make('svg_path')
                ->label('SVG-Pfad')
                ->maxLength(500),

            TextInput::make('scan_count')
                ->label('Scan-Anzahl')
                ->numeric()
                ->default(0),

            DateTimePicker::make('last_scanned_at')
                ->label('Zuletzt gescannt')
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code'),

                TextColumn::make('target_url')
                    ->label('Ziel-URL')
                    ->limit(50),

                TextColumn::make('scan_count')
                    ->label('Scans'),

                TextColumn::make('last_scanned_at')
                    ->label('Zuletzt gescannt')
                    ->dateTime('d.m.Y H:i'),

                TextColumn::make('png_path')
                    ->label('PNG')
                    ->formatStateUsing(fn (?string $state): string => $state ? '✓' : '—'),

                TextColumn::make('svg_path')
                    ->label('SVG')
                    ->formatStateUsing(fn (?string $state): string => $state ? '✓' : '—'),
            ])
            ->headerActions([])
            ->actions([
                EditAction::make(),
            ]);
    }
}
