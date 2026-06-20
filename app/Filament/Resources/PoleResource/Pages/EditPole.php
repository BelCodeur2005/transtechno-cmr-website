<?php

namespace App\Filament\Resources\PoleResource\Pages;

use App\Filament\Resources\PoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPole extends EditRecord
{
    protected static string $resource = PoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
