<?php

namespace App\Filament\Resources;

use App\Exports\SavRapportExport;
use App\Filament\Resources\SavTicketResource\Pages;
use App\Models\EquipmentInventory;
use App\Models\SavTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class SavTicketResource extends Resource
{
    protected static ?string $model = SavTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Technique';
    protected static ?string $navigationLabel = 'SAV & Maintenance';
    protected static ?string $modelLabel = 'Ticket SAV';
    protected static ?string $pluralModelLabel = 'Tickets SAV';
    protected static ?int $navigationSort = 1;

    protected const STATUSES = [
        'Ouvert'   => 'Ouvert',
        'En cours' => 'En cours',
        'Résolu'   => 'Résolu',
        'Clôturé'  => 'Clôturé',
    ];

    protected const PRIORITIES = [
        'Normal'   => 'Normal',
        'Critique' => 'Critique',
        'Urgent'   => 'Urgent',
    ];

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', ['Ouvert', 'En cours'])->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            // ══════════════════════════════════════════════════════════════════
            // 1. DISPATCH — Décisions immédiates à l'ouverture du ticket
            //    Ref · Statut · Priorité · Technicien → tout en une rangée
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Dispatch')
                ->columns(4)
                ->schema([
                    Forms\Components\TextInput::make('reference')
                        ->label('Référence')
                        ->prefix('#')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Générée automatiquement'),

                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options(self::STATUSES)
                        ->default('Ouvert')
                        ->required()
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            if (in_array($state, ['Résolu', 'Clôturé'], true)) {
                                $set('resolved_at', now()->format('Y-m-d H:i:s'));
                            }
                        }),

                    Forms\Components\Select::make('priority')
                        ->label('Priorité')
                        ->options(self::PRIORITIES)
                        ->default('Normal')
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('assigned_technician_id')
                        ->label('Technicien assigné')
                        ->relationship('technician', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Non assigné'),
                ]),

            // ══════════════════════════════════════════════════════════════════
            // 2. DEMANDE CLIENT — Ce que le client a décrit + comment le joindre
            //    Visible en priorité : l'admin doit pouvoir rappeler immédiatement
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Demande client')
                ->description('Informations telles que soumises par le client.')
                ->schema([
                    // Carte structurée pour les tickets issus du formulaire public
                    Forms\Components\Placeholder::make('public_contact_card')
                        ->label('Contact (formulaire site web)')
                        ->columnSpanFull()
                        ->content(function (Forms\Get $get): \Illuminate\Support\HtmlString {
                            $desc = $get('description') ?? '';
                            if (! str_starts_with($desc, '[Demande en ligne]')) {
                                return new \Illuminate\Support\HtmlString('');
                            }

                            // Extraction des lignes de contexte (avant "DESCRIPTION :")
                            $rows = [];
                            foreach (explode("\n", $desc) as $line) {
                                $line = trim($line);
                                if (! $line || $line === '[Demande en ligne]') continue;
                                if (str_starts_with($line, 'DESCRIPTION :')) break;
                                $rows[] = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
                            }

                            // Extraction du problème (après "DESCRIPTION :")
                            $problemStart = strpos($desc, 'DESCRIPTION :');
                            $problem = $problemStart !== false
                                ? trim(substr($desc, $problemStart + strlen('DESCRIPTION :')))
                                : '';

                            // Numéro de téléphone cliquable
                            preg_match('/Tél\s*:\s*([\+\d][\d\s\/\-]{6,})/', $desc, $m);
                            $phone = isset($m[1]) ? preg_replace('/\s+/', '', trim($m[1])) : null;

                            $html  = '<div class="space-y-3">';

                            // Bloc coordonnées
                            $html .= '<div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 space-y-1">';
                            $html .= '<p class="text-xs font-semibold uppercase tracking-wide text-blue-500 mb-2">Coordonnées</p>';
                            foreach ($rows as $row) {
                                $html .= "<p class=\"text-sm text-gray-800\">{$row}</p>";
                            }
                            if ($phone) {
                                $html .= "<a href=\"tel:{$phone}\" class=\"mt-2 inline-flex items-center gap-1.5 rounded-md bg-green-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-green-700\">"
                                    . "<svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'/></svg>"
                                    . " Appeler {$phone}</a>";
                            }
                            $html .= '</div>';

                            // Bloc description de la panne
                            if ($problem) {
                                $html .= '<div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">';
                                $html .= '<p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">Description de la panne</p>';
                                $html .= '<p class="text-sm text-gray-800 whitespace-pre-line">' . htmlspecialchars($problem, ENT_QUOTES, 'UTF-8') . '</p>';
                                $html .= '</div>';
                            }

                            $html .= '</div>';

                            return new \Illuminate\Support\HtmlString($html);
                        })
                        ->visible(fn (Forms\Get $get): bool =>
                            str_starts_with($get('description') ?? '', '[Demande en ligne]')
                        ),

                    // Champ description complet (ticket manuel ou vue brute du ticket public)
                    Forms\Components\Textarea::make('description')
                        ->label(fn (Forms\Get $get): string =>
                            str_starts_with($get('description') ?? '', '[Demande en ligne]')
                                ? 'Description brute (formulaire public — ne pas modifier)'
                                : 'Description de la panne / intervention'
                        )
                        ->rows(5)
                        ->required()
                        ->columnSpanFull(),
                ]),

            // ══════════════════════════════════════════════════════════════════
            // 3. CLIENT CRM — Lien optionnel vers la fiche client
            //    Secondaire : après avoir lu la demande, on lie le bon client
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Client CRM')
                ->description('Optionnel — lier ce ticket à un client enregistré dans le CRM.')
                ->collapsed()
                ->schema([
                    Forms\Components\Select::make('client_id')
                        ->label('Structure cliente')
                        ->relationship('client', 'structure_name')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->columnSpanFull()
                        ->placeholder('Rechercher par nom d\'établissement…'),

                    Forms\Components\Placeholder::make('client_info')
                        ->label('Fiche client')
                        ->columnSpanFull()
                        ->content(function (Forms\Get $get): string {
                            $clientId = $get('client_id');
                            if (! $clientId) return '—';

                            $client = \App\Models\Client::with('contacts')->find($clientId);
                            if (! $client) return '—';

                            $lines = ["🏥 {$client->structure_name}"];
                            if ($client->city)  $lines[] = "📍 {$client->city} — Secteur {$client->sector}";
                            if ($client->phone) $lines[] = "📞 {$client->phone}";
                            if ($client->email) $lines[] = "✉️ {$client->email}";

                            $primary = $client->contacts->firstWhere('is_primary', true)
                                ?? $client->contacts->first();
                            if ($primary) {
                                $lines[] = "👤 {$primary->name}" . ($primary->role ? " ({$primary->role})" : '');
                                if ($primary->phone) $lines[] = "   📞 {$primary->phone}";
                            }

                            return implode("\n", $lines);
                        })
                        ->visible(fn (Forms\Get $get) => (bool) $get('client_id')),
                ]),

            // ══════════════════════════════════════════════════════════════════
            // 4. ÉQUIPEMENTS — Identifiés après avoir lu le problème
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Équipements concernés')
                ->description('Renseignez au moins une des trois options ci-dessous.')
                ->schema([
                    Forms\Components\Select::make('equipments')
                        ->label('① Parc installé')
                        ->helperText('Appareils déjà enregistrés dans le parc client.')
                        ->relationship('equipments')
                        ->getOptionLabelFromRecordUsing(function (EquipmentInventory $record): string {
                            $product = $record->product?->name ?? 'Équipement';
                            $serial  = $record->serial_number ? " — N° {$record->serial_number}" : '';
                            $client  = $record->client?->structure_name ? " [{$record->client->structure_name}]" : '';
                            return $product . $serial . $client;
                        })
                        ->searchable()
                        ->preload()
                        ->multiple()
                        ->columnSpanFull(),

                    Forms\Components\Select::make('_catalogue_lookup')
                        ->label('② Chercher dans le catalogue')
                        ->helperText('Pré-remplit le champ texte libre ci-dessous.')
                        ->options(fn () => \App\Models\Product::query()
                            ->with('brand')
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn ($p) => [
                                $p->id => $p->name . ($p->brand?->name ? ' — ' . $p->brand->name : ''),
                            ])
                        )
                        ->searchable()
                        ->dehydrated(false)
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            if (! $state) return;
                            $product = \App\Models\Product::with('brand')->find($state);
                            if (! $product) return;
                            $label = $product->name;
                            if ($product->brand?->name) $label .= ' — ' . $product->brand->name;
                            $set('equipments_notes', $label . "\nN° série : ");
                        })
                        ->placeholder('Tapez le nom du produit…')
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('equipments_notes')
                        ->label('③ Texte libre (hors parc / hors catalogue)')
                        ->helperText('Marque, modèle, N° de série.')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            // ══════════════════════════════════════════════════════════════════
            // 5. PRÉ-DIAGNOSTIC — Notes admin après le premier appel
            //    Séparé de la description client : moment et auteur différents
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Pré-diagnostic')
                ->description('Notes de l\'équipe technique après le premier contact téléphonique, avant déplacement.')
                ->schema([
                    Forms\Components\Textarea::make('pre_diagnostic')
                        ->label('Observations à distance')
                        ->helperText('Cause probable, vérifications demandées à l\'utilisateur, matériel à prévoir.')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),

            // ══════════════════════════════════════════════════════════════════
            // 6. DATES — Suivi temporel du ticket
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Dates')
                ->columns(2)
                ->schema([
                    Forms\Components\DateTimePicker::make('opened_at')
                        ->label('Ouvert le')
                        ->default(now()),

                    Forms\Components\DateTimePicker::make('resolved_at')
                        ->label('Résolu / Clôturé le'),
                ]),

            // ══════════════════════════════════════════════════════════════════
            // 7. RAPPORT — Rempli après l'intervention, collapsed par défaut
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Rapport d\'intervention')
                ->description('À compléter après la clôture du ticket sur site.')
                ->collapsed()
                ->schema([
                    Forms\Components\Textarea::make('rapport_intervention')
                        ->label('Rapport technique')
                        ->helperText('Cause identifiée, actions réalisées, pièces remplacées, durée d\'intervention, observations.')
                        ->rows(7)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Référence')
                    ->prefix('#')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.structure_name')
                    ->label('Client / Contact')
                    ->description(fn (SavTicket $r): ?string => $r->client?->city)
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('equipments_count')
                    ->label('Équipements')
                    ->counts('equipments')
                    ->badge()
                    ->color('gray')
                    ->suffix(' appareil(s)')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Ouvert'   => 'warning',
                        'En cours' => 'info',
                        'Résolu'   => 'success',
                        'Clôturé'  => 'gray',
                        default    => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Priorité')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Urgent'   => 'danger',
                        'Critique' => 'warning',
                        default    => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('technician.name')
                    ->label('Technicien')
                    ->placeholder('Non assigné')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('opened_at')
                    ->label('Ouvert le')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('resolved_at')
                    ->label('Résolu le')
                    ->dateTime('d/m/Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('opened_at', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('export_sav')
                    ->label('Rapport SAV Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->form([
                        Forms\Components\Select::make('year')
                            ->label('Année')
                            ->options(fn () => collect(range(now()->year, now()->year - 3))
                                ->mapWithKeys(fn ($y) => [$y => $y])
                                ->toArray())
                            ->default(now()->year)
                            ->required()
                            ->native(false),
                    ])
                    ->action(function (array $data) {
                        return Excel::download(
                            new SavRapportExport((int) $data['year']),
                            'rapport-sav-' . $data['year'] . '.xlsx'
                        );
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')->options(self::STATUSES),
                Tables\Filters\SelectFilter::make('priority')->label('Priorité')->options(self::PRIORITIES),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSavTickets::route('/'),
            'create' => Pages\CreateSavTicket::route('/create'),
            'edit'   => Pages\EditSavTicket::route('/{record}/edit'),
        ];
    }
}
