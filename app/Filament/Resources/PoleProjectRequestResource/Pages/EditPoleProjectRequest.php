<?php

namespace App\Filament\Resources\PoleProjectRequestResource\Pages;

use App\Filament\Resources\PoleProjectRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPoleProjectRequest extends EditRecord
{
    protected static string $resource = PoleProjectRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
