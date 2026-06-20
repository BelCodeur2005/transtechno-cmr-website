<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use App\Models\QuotationStatusHistory;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function afterCreate(): void
    {
        // Génère la référence si elle n'a pas encore été générée
        // (le modèle le fait normalement dans booted(), mais on log l'historique ici)
        QuotationStatusHistory::create([
            'quotation_id' => $this->record->id,
            'old_status'   => null,
            'new_status'   => $this->record->status,
            'changed_by'   => auth()->id(),
            'note'         => 'Cotation créée manuellement depuis le back-office.',
        ]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Cotation créée — ' . $this->record->reference;
    }
}
