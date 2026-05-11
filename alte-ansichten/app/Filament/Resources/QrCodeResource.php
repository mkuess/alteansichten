<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QrCodeResource\Pages;
use App\Models\Place;
use App\Models\QrCode;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QrCodeResource extends Resource
{
    protected static ?string $model = QrCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'QR-Codes';

    protected static ?string $modelLabel = 'QR-Code';

    protected static ?string $pluralModelLabel = 'QR-Codes';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('place_id')
                ->label('Standort')
                ->options(Place::pluck('title', 'id'))
                ->searchable()
                ->required(),

            TextInput::make('code')
                ->label('Code')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('place.title')
                    ->label('Standort')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('redirect_url')
                    ->label('QR-Redirect-URL')
                    ->getStateUsing(fn (QrCode $record): string => url('/qr/' . $record->code))
                    ->copyable()
                    ->copyMessage('URL kopiert')
                    ->limit(50),

                TextColumn::make('target_url')
                    ->label('Ziel-URL')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('scan_count')
                    ->label('Scans')
                    ->sortable(),

                TextColumn::make('last_scanned_at')
                    ->label('Zuletzt gescannt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->actions([
                Action::make('openQrUrl')
                    ->label('QR-URL öffnen')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn (QrCode $record): string => url('/qr/' . $record->code))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQrCodes::route('/'),
            'create' => Pages\CreateQrCode::route('/create'),
            'edit'   => Pages\EditQrCode::route('/{record}/edit'),
        ];
    }
}
