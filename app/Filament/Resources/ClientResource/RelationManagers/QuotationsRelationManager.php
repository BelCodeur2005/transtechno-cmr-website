<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class QuotationsRelationManager extends RelationManager
{
    protected static string $relationship = 'quotations';

    protected static ?string $title = 'Cotations';

    protected static ?string $modelLabel = 'cotation';

    protected const STATUSES = [
        'Nouveau'      => 'Nouveau',
        'En cours'     => 'En cours',
        'Devis Envoyé' => 'Devis Envoyé',
        'Urgent'       => 'Urgent',
        'Gagné'        => 'Gagné',
        'Perdu'        => 'Perdu',
        'Critique'     => 'Critique',
    ];

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reference')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Référence')
                    ->weight('bold')
                    ->searchable()
                    ->url(fn ($record) => \App\Filament\Resources\QuotationResource::getUrl('edit', ['record' => $record])),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Gagné'        => 'success',
                        'Perdu'        => 'danger',
                        'Urgent', 'Critique' => 'warning',
                        'Devis Envoyé' => 'info',
                        default        => 'gray',
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Priorité')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Urgent'   => 'danger',
                        'Critique' => 'warning',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('Assigné à')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(self::STATUSES),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Ouvrir')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => \App\Filament\Resources\QuotationResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->heading('Historique des cotations');
    }
}
