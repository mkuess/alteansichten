<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentReportResource\Pages;
use App\Models\ContentReport;
use App\Models\MediaItem;
use App\Models\Municipality;
use App\Models\Place;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContentReportResource extends Resource
{
    protected static ?string $model = ContentReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Moderation';

    protected static ?string $navigationLabel = 'Meldungen';

    protected static ?string $modelLabel = 'Meldung';

    protected static ?string $pluralModelLabel = 'Meldungen';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

        public static function form(Form $form): Form
    {
        return $form->schema([

            Section::make('Gemeldeter Inhalt')
                ->schema([
                    Select::make('place_id')
                        ->label('Standort')
                        ->options(Place::pluck('title', 'id'))
                        ->searchable()
                        ->nullable(),

                    Select::make('media_item_id')
                        ->label('Medienelement')
                        ->options(MediaItem::pluck('title', 'id'))
                        ->searchable()
                        ->nullable(),

                    Select::make('municipality_id')
                        ->label('Gemeinde')
                        ->options(Municipality::pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),
                ]),

            Section::make('Kontaktdaten des Melders')
                ->schema([
                    TextInput::make('reporter_name')
                        ->label('Name')
                        ->maxLength(255)
                        ->nullable(),

                    TextInput::make('reporter_email')
                        ->label('E-Mail')
                        ->email()
                        ->maxLength(255)
                        ->nullable(),
                ]),

            Section::make('Meldedetails')
                ->schema([
                    Select::make('report_type')
                        ->label('Meldegrund')
                        ->options([
                            'rights_issue'         => 'Rechtehinweis',
                            'wrong_information'    => 'Falsche Information',
                            'privacy_concern'      => 'Datenschutzanliegen',
                            'duplicate'            => 'Duplikat',
                            'inappropriate_content'=> 'Unangemessener Inhalt',
                            'technical_problem'    => 'Technisches Problem',
                            'other'                => 'Sonstiges',
                        ])
                        ->required(),

                    Textarea::make('message')
                        ->label('Nachricht')
                        ->rows(5)
                        ->required(),

                    Toggle::make('rights_claim')
                        ->label('Rechtsanspruch geltend gemacht')
                        ->default(false),
                ]),

            Section::make('Prüfung')
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'open'      => 'Offen',
                            'in_review' => 'In Prüfung',
                            'resolved'  => 'Erledigt',
                            'rejected'  => 'Abgelehnt',
                            'archived'  => 'Archiviert',
                        ])
                        ->default('open')
                        ->required(),

                    Textarea::make('review_note')
                        ->label('Prüfnotiz')
                        ->rows(4)
                        ->nullable(),

                    Select::make('reviewed_by_user_id')
                        ->label('Geprüft von')
                        ->options(User::pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),

                    DateTimePicker::make('reviewed_at')
                        ->label('Geprüft am')
                        ->nullable(),
                ]),

            Section::make('Technische Daten')
                ->schema([
                    TextInput::make('ip_address')
                        ->label('IP-Adresse')
                        ->maxLength(45)
                        ->nullable(),

                    Textarea::make('user_agent')
                        ->label('User Agent')
                        ->rows(2)
                        ->nullable(),
                ])
                ->collapsed(),

        ]);
    }

    public static function table(Table $table): Table
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
                    })
                    ->sortable(),

                TextColumn::make('message')
                    ->label('Nachricht')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('reporter_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                TextColumn::make('reporter_email')
                    ->label('E-Mail')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('place.title')
                    ->label('Standort')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                TextColumn::make('mediaItem.title')
                    ->label('Medienelement')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                TextColumn::make('municipality.name')
                    ->label('Gemeinde')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                IconColumn::make('rights_claim')
                    ->label('Rechtsanspruch')
                    ->boolean(),

                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open'      => 'Offen',
                        'in_review' => 'In Prüfung',
                        'resolved'  => 'Erledigt',
                        'rejected'  => 'Abgelehnt',
                        'archived'  => 'Archiviert',
                        default     => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'      => 'warning',
                        'in_review' => 'info',
                        'resolved'  => 'success',
                        'rejected'  => 'danger',
                        'archived'  => 'gray',
                        default     => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('reviewed_at')
                    ->label('Geprüft am')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Gemeldet am')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'open'      => 'Offen',
                        'in_review' => 'In Prüfung',
                        'resolved'  => 'Erledigt',
                        'rejected'  => 'Abgelehnt',
                        'archived'  => 'Archiviert',
                    ]),

                SelectFilter::make('report_type')
                    ->label('Meldegrund')
                    ->options([
                        'rights_issue'          => 'Rechtehinweis',
                        'wrong_information'     => 'Falsche Information',
                        'privacy_concern'       => 'Datenschutzanliegen',
                        'duplicate'             => 'Duplikat',
                        'inappropriate_content' => 'Unangemessener Inhalt',
                        'technical_problem'     => 'Technisches Problem',
                        'other'                 => 'Sonstiges',
                    ]),

                TernaryFilter::make('rights_claim')
                    ->label('Rechtsanspruch'),

                SelectFilter::make('place_id')
                    ->label('Standort')
                    ->options(Place::pluck('title', 'id'))
                    ->searchable(),

                SelectFilter::make('media_item_id')
                    ->label('Medienelement')
                    ->options(MediaItem::pluck('title', 'id'))
                    ->searchable(),

                SelectFilter::make('municipality_id')
                    ->label('Gemeinde')
                    ->options(Municipality::pluck('name', 'id'))
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContentReports::route('/'),
            'create' => Pages\CreateContentReport::route('/create'),
            'edit'   => Pages\EditContentReport::route('/{record}/edit'),
        ];
    }
}
