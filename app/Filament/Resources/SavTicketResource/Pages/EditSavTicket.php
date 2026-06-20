<?php

namespace App\Filament\Resources\SavTicketResource\Pages;

use App\Filament\Resources\SavTicketResource;
use App\Models\SavTicket;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSavTicket extends EditRecord
{
    protected static string $resource = SavTicketResource::class;

    protected function getHeaderActions(): array
    {
        $phone = $this->resolvePhone($this->record);

        return array_filter([
            $phone
                ? Actions\Action::make('appeler_client')
                    ->label('Appeler ' . $phone)
                    ->icon('heroicon-o-phone')
                    ->color('success')
                    ->url('tel:' . $phone)
                : null,

            Actions\DeleteAction::make(),
        ]);
    }

    /**
     * Cherche le numéro dans l'ordre : client CRM → description (formulaire public).
     */
    private function resolvePhone(SavTicket $ticket): ?string
    {
        // 1) Client lié
        if ($ticket->client?->phone) {
            return preg_replace('/\s+/', '', $ticket->client->phone);
        }

        // 2) Description issue du formulaire public
        if (preg_match('/Tél\s*:\s*([\+\d][\d\s\/\-]{6,})/', $ticket->description ?? '', $m)) {
            return preg_replace('/\s+/', '', trim($m[1]));
        }

        return null;
    }
}
