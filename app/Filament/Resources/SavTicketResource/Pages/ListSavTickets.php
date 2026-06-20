<?php

namespace App\Filament\Resources\SavTicketResource\Pages;

use App\Filament\Resources\SavTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSavTickets extends ListRecords
{
    protected static string $resource = SavTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
