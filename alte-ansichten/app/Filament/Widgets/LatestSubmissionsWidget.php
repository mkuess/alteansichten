<?php

namespace App\Filament\Widgets;

use App\Models\Submission;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestSubmissionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Neueste Einreichungen';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Submission::query()
                    ->with(['place', 'municipality'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Betreff')
                    ->limit(40)
                    ->placeholder('—')
                    ->searchable(false),

                TextColumn::make('submitted_by_name')
                    ->label('Einreicher')
                    ->limit(28)
                    ->placeholder('Anonym'),

                TextColumn::make('place.name')
                    ->label('Standort')
                    ->placeholder('—')
                    ->limit(28),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'      => 'ausstehend',
                        'open'         => 'offen',
                        'under_review' => 'in Prüfung',
                        'approved'     => 'genehmigt',
                        'rejected'     => 'abgelehnt',
                        default        => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending', 'open' => 'warning',
                        'under_review'    => 'info',
                        'approved'        => 'success',
                        'rejected'        => 'danger',
                        default           => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Datum')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }
}
