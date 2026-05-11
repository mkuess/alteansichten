<?php

namespace App\Filament\Resources\PlaceResource\RelationManagers;

use App\Models\MediaItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaLinksRelationManager extends RelationManager
{
    protected static string $relationship = 'placeMediaLinks';

    protected static ?string $title = 'Verknüpfte Medien';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('media_item_id')
                ->label('Medienelement')
                ->options(MediaItem::pluck('title', 'id'))
                ->searchable()
                ->required(),

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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['linkable_type'] = 'place';

        return $data;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('mediaItem.file_path')
                    ->label('Vorschau')
                    ->disk('public')
                    ->height(40)
                    ->width(56)
                    ->checkFileExistence(false),

                TextColumn::make('mediaItem.title')
                    ->label('Titel')
                    ->limit(35),

                TextColumn::make('mediaItem.type')
                    ->label('Typ')
                    ->badge(),

                TextColumn::make('mediaItem.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending'  => 'warning',
                        'rejected' => 'danger',
                        'hidden'   => 'gray',
                        default    => 'gray',
                    }),

                TextColumn::make('relationship_type')
                    ->label('Beziehung')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'main_subject'   => 'Hauptmotiv',
                        'panorama_of'    => 'Panorama von',
                        'shows_detail'   => 'Zeigt Detail',
                        'related'        => 'Verwandt',
                        'document_about' => 'Dokument über',
                        'source_for'     => 'Quelle für',
                        default          => '—',
                    }),

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
            ->defaultSort('sort_order')
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
