<?php

namespace App\Observers;

use App\Models\EquipmentInventory;
use App\Models\Quotation;
use Illuminate\Support\Facades\Log;

class QuotationObserver
{
    /**
     * Quand une cotation passe à "Gagné" avec l'option Installation active
     * et qu'un client est lié, on crée automatiquement les entrées dans le parc installé.
     */
    public function updating(Quotation $quotation): void
    {
        // Ne s'exécute que lors d'une transition vers "Gagné"
        if (! $quotation->isDirty('status')) return;
        if ($quotation->status !== 'Gagné') return;
        if ($quotation->getOriginal('status') === 'Gagné') return;

        // Installation doit être incluse dans la cotation
        if (! $quotation->services_installation) return;

        // Un client doit être lié (client_id est obligatoire dans equipment_inventory)
        if (! $quotation->client_id) return;

        $quotation->loadMissing('items');

        $createdCount = 0;
        foreach ($quotation->items as $item) {
            if (! $item->product_id) continue;

            // Idempotent : ne recrée pas si déjà enregistré pour cette cotation
            EquipmentInventory::firstOrCreate(
                [
                    'client_id'  => $quotation->client_id,
                    'product_id' => $item->product_id,
                    'notes'      => "Cotation #{$quotation->reference}",
                ],
                [
                    'installation_date' => now()->toDateString(),
                ]
            );
            $createdCount++;
        }

        if ($createdCount > 0) {
            Log::info("Cotation #{$quotation->reference} → Gagné : {$createdCount} équipement(s) ajouté(s) au parc du client #{$quotation->client_id}.");
        }
    }
}
