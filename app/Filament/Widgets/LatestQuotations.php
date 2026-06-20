<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\QuotationResource;
use App\Models\Quotation;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestQuotations extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = ['default' => 'full', 'lg' => 2];

    protected static ?string $heading = 'Dernières demandes de cotation';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Quotation::query()->latest()->limit(6)
            )
            ->paginated(false)
            ->headerActions([
                Tables\Actions\Action::make('voir_tout')
                    ->label('Voir tout')
                    ->icon('heroicon-m-arrow-right')
                    ->iconPosition('after')
                    ->color('gray')
                    ->url(QuotationResource::getUrl()),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Référence')
                    ->prefix('#')
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                Tables\Columns\TextColumn::make('structure_name')
                    ->label('Projet / Structure')
                    ->description(fn (Quotation $r) => $r->city)
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Nouveau' => 'gray',
                        'En cours', 'Devis Envoyé' => 'info',
                        'Gagné' => 'success',
                        'Urgent', 'Perdu' => 'danger',
                        'Critique' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->since()
                    ->tooltip(fn (Quotation $r) => $r->created_at?->format('d/m/Y H:i')),
            ])
            ->recordUrl(fn (Quotation $record) => QuotationResource::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('Aucune cotation')
            ->emptyStateDescription('Les demandes envoyées depuis le site apparaîtront ici.')
            ->emptyStateIcon('heroicon-o-document-currency-dollar');
    }
}
