<?php

namespace App\Filament\Resources\PoleProjectRequestResource\Pages;

use App\Filament\Resources\PoleProjectRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPoleProjectRequests extends ListRecords
{
    protected static string $resource = PoleProjectRequestResource::class;

    protected function getHeaderActions(): array
    {
        // Les demandes proviennent du formulaire public : pas de création manuelle.
        return [];
    }
}
