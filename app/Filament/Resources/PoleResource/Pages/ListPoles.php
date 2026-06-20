<?php

namespace App\Filament\Resources\PoleResource\Pages;

use App\Filament\Resources\PoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPoles extends ListRecords
{
    protected static string $resource = PoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
