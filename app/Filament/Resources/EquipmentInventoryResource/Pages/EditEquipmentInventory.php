<?php

namespace App\Filament\Resources\EquipmentInventoryResource\Pages;

use App\Filament\Resources\EquipmentInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEquipmentInventory extends EditRecord
{
    protected static string $resource = EquipmentInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
