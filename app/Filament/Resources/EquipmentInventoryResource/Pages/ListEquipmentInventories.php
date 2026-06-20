<?php

namespace App\Filament\Resources\EquipmentInventoryResource\Pages;

use App\Filament\Resources\EquipmentInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEquipmentInventories extends ListRecords
{
    protected static string $resource = EquipmentInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
