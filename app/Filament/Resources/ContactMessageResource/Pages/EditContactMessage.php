<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactMessage extends EditRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /** À l'ouverture d'un message « nouveau », le passer automatiquement en « lu ». */
    protected function afterFill(): void
    {
        if ($this->record->status === 'nouveau') {
            $this->record->update(['status' => 'lu']);
            $this->data['status'] = 'lu';
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Message mis à jour';
    }
}
