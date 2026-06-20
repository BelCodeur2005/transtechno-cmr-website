<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use App\Models\Quotation;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListQuotations extends ListRecords
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->label('Nouvelle cotation (téléphone / visite)'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Toutes les demandes'),

            'nouveau' => Tab::make('Nouvelles')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Nouveau'))
                ->badge(Quotation::where('status', 'Nouveau')->count())
                ->badgeColor('danger'),

            'en_cours' => Tab::make('En cours')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['En cours', 'Devis Envoyé'])),

            'urgent' => Tab::make('Dossiers urgents')
                ->modifyQueryUsing(fn (Builder $query) => $query->where(fn (Builder $q) => $q->where('priority', 'Urgent')->orWhere('status', 'Urgent')))
                ->badgeColor('danger'),

            'gagne' => Tab::make('Gagnées')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Gagné')),
        ];
    }
}
