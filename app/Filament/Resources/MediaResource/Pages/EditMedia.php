<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Pages\Mediatheque;
use App\Filament\Resources\MediaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMedia extends EditRecord
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /** Fil d'Ariane vers la nouvelle page Médiathèque (et non l'ancienne liste). */
    public function getBreadcrumbs(): array
    {
        return [
            Mediatheque::getUrl() => 'Médiathèque',
            'Modifier le média',
        ];
    }

    /** Retour à la Médiathèque après enregistrement. */
    protected function getRedirectUrl(): string
    {
        return Mediatheque::getUrl();
    }
}
