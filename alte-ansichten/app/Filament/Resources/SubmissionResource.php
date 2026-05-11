<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubmissionResource\Pages;
use App\Models\Municipality;
use App\Models\Place;
use App\Models\Submission;
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

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationGroup = 'Moderation';

    protected static ?string $navigationLabel = 'Einreichungen';

    protected static ?string $modelLabel = 'Einreichung';

    protected static ?string $pluralModelLabel = 'Einreichungen';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Section::make('Eingereichte Inhalte')
                ->schema([
                    TextInput::make('title')
                        ->label('Titel')
                        ->required()
                        ->maxLength(255),

                    Select::make('material_type')
                        ->label('Materialtyp')
                        ->options([
                            'image'         => 'Bild',
                            'document'      => 'Dokument',
                            'story'         => 'Geschichte',
                            'correction'    => 'Korrektur',
                            'location_hint' => 'Ortshinweis',
                            'other'         => 'Sonstiges',
                        ])
                        ->nullable(),

                    Textarea::make('message')
                        ->label('Nachricht')
                        ->rows(5)
                        ->nullable(),

                    Textarea::make('source_note')
                        ->label('Quellenangabe')
                        ->rows(3)
                        ->nullable(),
                ]),

            Section::make('Kontaktdaten')
                ->schema([
                    TextInput::make('submitted_by_name')
                        ->label('Name')
                        ->maxLength(255)
                        ->nullable(),

                    TextInput::make('submitted_by_email')
                        ->label('E-Mail')
                        ->email()
                        ->maxLength(255)
                        ->nullable(),

                    TextInput::make('submitted_by_phone')
                        ->label('Telefon')
                        ->tel()
                        ->maxLength(50)
                        ->nullable(),
                ]),

            Section::make('Bezug / Zuordnung')
                ->schema([
                    Select::make('place_id')
                        ->label('Standort')
                        ->options(Place::pluck('title', 'id'))
                        ->searchable()
                        ->nullable(),

                    Select::make('municipality_id')
                        ->label('Gemeinde')
                        ->options(Municipality::pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),
                ]),

            Section::make('Rechte')
                ->schema([
                    Toggle::make('rights_confirmation')
                        ->label('Rechte bestätigt')
                        ->default(false),

                    Textarea::make('rights_note')
                        ->label('Rechtliche Anmerkung')
                        ->rows(3)
                        ->nullable(),
                ]),

            Section::make('Prüfung')
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending'   => 'Ausstehend',
                            'in_review' => 'In Prüfung',
                            'accepted'  => 'Akzeptiert',
                            'rejected'  => 'Abgelehnt',
                            'archived'  => 'Archiviert',
                        ])
                        ->default('pending')
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
                TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable()
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
                    })
                    ->sortable(),

                TextColumn::make('submitted_by_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('submitted_by_email')
                    ->label('E-Mail')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('place.title')
                    ->label('Standort')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                TextColumn::make('municipality.name')
                    ->label('Gemeinde')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                IconColumn::make('rights_confirmation')
                    ->label('Rechte')
                    ->boolean(),

                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'   => 'Ausstehend',
                        'in_review' => 'In Prüfung',
                        'accepted'  => 'Akzeptiert',
                        'rejected'  => 'Abgelehnt',
                        'archived'  => 'Archiviert',
                        default     => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'in_review' => 'info',
                        'accepted'  => 'success',
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
                    ->label('Eingereicht am')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Ausstehend',
                        'in_review' => 'In Prüfung',
                        'accepted'  => 'Akzeptiert',
                        'rejected'  => 'Abgelehnt',
                        'archived'  => 'Archiviert',
                    ]),

                SelectFilter::make('material_type')
                    ->label('Materialtyp')
                    ->options([
                        'image'         => 'Bild',
                        'document'      => 'Dokument',
                        'story'         => 'Geschichte',
                        'correction'    => 'Korrektur',
                        'location_hint' => 'Ortshinweis',
                        'other'         => 'Sonstiges',
                    ]),

                TernaryFilter::make('rights_confirmation')
                    ->label('Rechte bestätigt'),

                SelectFilter::make('place_id')
                    ->label('Standort')
                    ->options(Place::pluck('title', 'id'))
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
            'index'  => Pages\ListSubmissions::route('/'),
            'create' => Pages\CreateSubmission::route('/create'),
            'edit'   => Pages\EditSubmission::route('/{record}/edit'),
        ];
    }
}
