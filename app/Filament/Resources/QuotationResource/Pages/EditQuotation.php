<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use App\Mail\DevisMail;
use App\Models\QuotationStatusHistory;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    /** Statut avant sauvegarde, pour historiser le changement. */
    protected ?string $oldStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('devis_pdf')
                ->label('Aperçu du devis (PDF)')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->url(fn () => route('cotation.devis', $this->record))
                ->openUrlInNewTab(),

            Actions\Action::make('envoyer_devis')
                ->label('Envoyer le devis')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->modalHeading('Envoyer le devis au client')
                ->modalDescription(fn () => 'Le PDF du devis #' . $this->record->reference . ' sera joint à l\'email.')
                ->modalSubmitActionLabel('Envoyer')
                ->form([
                    Forms\Components\TextInput::make('email')
                        ->label('Email du client')
                        ->email()
                        ->required()
                        ->default(fn () => $this->record->email),
                    Forms\Components\Textarea::make('custom_message')
                        ->label('Message personnalisé (optionnel)')
                        ->rows(3)
                        ->placeholder('Un mot d\'accompagnement ajouté en haut de l\'email…'),
                ])
                ->action(function (array $data): void {
                    try {
                        // Mémorise l'email si nouveau, pour les prochains envois.
                        if ($this->record->email !== $data['email']) {
                            $this->record->update(['email' => $data['email']]);
                        }

                        Mail::to($data['email'])->send(new DevisMail($this->record, $data['custom_message'] ?? null));

                        $old = $this->record->status;
                        if ($old !== 'Devis Envoyé' && ! in_array($old, ['Gagné', 'Perdu'], true)) {
                            $this->record->update(['status' => 'Devis Envoyé']);
                            QuotationStatusHistory::create([
                                'quotation_id' => $this->record->id,
                                'old_status' => $old,
                                'new_status' => 'Devis Envoyé',
                                'changed_by' => auth()->id(),
                                'note' => 'Devis envoyé par email à ' . $data['email'] . '.',
                            ]);
                        }

                        $this->refreshFormData(['status', 'email']);

                        Notification::make()
                            ->title('Devis envoyé')
                            ->body('Le devis a été envoyé à ' . $data['email'] . '.')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Log::warning('Échec envoi devis #' . $this->record->reference . ' : ' . $e->getMessage());
                        Notification::make()
                            ->title('Échec de l\'envoi')
                            ->body('Le devis n\'a pas pu être envoyé. Vérifiez la configuration email.')
                            ->danger()
                            ->send();
                    }
                }),

            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->oldStatus = $this->record->getOriginal('status');
    }

    protected function afterSave(): void
    {
        $newStatus = $this->record->status;

        if ($this->oldStatus !== null && $this->oldStatus !== $newStatus) {
            QuotationStatusHistory::create([
                'quotation_id' => $this->record->id,
                'old_status' => $this->oldStatus,
                'new_status' => $newStatus,
                'changed_by' => auth()->id(),
                'note' => 'Statut modifié depuis le back-office.',
            ]);
        }
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Cotation mise à jour';
    }
}
