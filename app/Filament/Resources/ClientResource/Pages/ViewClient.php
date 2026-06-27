<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Models\Client;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            // ── Cartes de statistiques ─────────────────────────────────────
            Section::make('Statistiques')
                ->columns(4)
                ->schema([
                    TextEntry::make('quotations_total')
                        ->label('Cotations totales')
                        ->state(fn (Client $record): int => $record->quotations()->count())
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight(FontWeight::Bold)
                        ->color('primary'),

                    TextEntry::make('quotations_active')
                        ->label('En cours')
                        ->state(fn (Client $record): int => $record->quotations()
                            ->whereNotIn('status', ['Gagné', 'Perdu'])
                            ->count())
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight(FontWeight::Bold)
                        ->color('warning'),

                    TextEntry::make('sav_total')
                        ->label('Tickets SAV')
                        ->state(fn (Client $record): int => $record->savTickets()->count())
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight(FontWeight::Bold)
                        ->color('danger'),

                    TextEntry::make('equipment_total')
                        ->label('Équipements installés')
                        ->state(fn (Client $record): int => $record->equipmentInventory()->count())
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight(FontWeight::Bold)
                        ->color('success'),
                ]),

            // ── Dernière activité ──────────────────────────────────────────
            Section::make()
                ->columns(2)
                ->schema([
                    TextEntry::make('last_quotation_at')
                        ->label('Dernière cotation')
                        ->state(fn (Client $record): ?string => $record->quotations()
                            ->latest()
                            ->value('created_at'))
                        ->dateTime('d/m/Y')
                        ->placeholder('Aucune cotation'),

                    TextEntry::make('last_sav_at')
                        ->label('Dernier ticket SAV')
                        ->state(fn (Client $record): ?string => $record->savTickets()
                            ->latest()
                            ->value('opened_at'))
                        ->dateTime('d/m/Y')
                        ->placeholder('Aucun ticket'),
                ]),

            // ── Informations de l'établissement ───────────────────────────
            Section::make('Informations')
                ->columns(2)
                ->schema([
                    TextEntry::make('structure_name')
                        ->label('Structure')
                        ->columnSpanFull()
                        ->weight(FontWeight::Bold),

                    TextEntry::make('city')
                        ->label('Ville')
                        ->placeholder('—'),

                    TextEntry::make('sector')
                        ->label('Secteur')
                        ->badge()
                        ->color(fn (string $state) => match ($state) {
                            'Public' => 'info',
                            'Recherche' => 'warning',
                            'ONG' => 'success',
                            default => 'gray',
                        }),

                    TextEntry::make('email')
                        ->label('Email')
                        ->placeholder('—')
                        ->url(fn (?string $state): ?string => $state ? "mailto:{$state}" : null),

                    TextEntry::make('phone')
                        ->label('Téléphone')
                        ->placeholder('—'),

                    IconEntry::make('is_validated')
                        ->label('Client validé (exonération TVA MINSANTÉ)')
                        ->boolean()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
