<?php

namespace App\Filament\Resources;

use App\Exports\ChiffreAffairesExport;
use App\Exports\CotationsPipelineExport;
use App\Filament\Resources\QuotationResource\Pages;
use App\Models\Client;
use App\Models\Quotation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $slug = 'cotations';
    protected static ?string $navigationGroup = 'Commercial';
    protected static ?string $navigationLabel = 'Cotations';
    protected static ?string $modelLabel = 'Cotation';
    protected static ?string $pluralModelLabel = 'Cotations';
    protected static ?int $navigationSort = 1;

    protected const STATUSES = [
        'Nouveau' => 'Nouveau',
        'En cours' => 'En cours',
        'Devis Envoyé' => 'Devis Envoyé',
        'Urgent' => 'Urgent',
        'Gagné' => 'Gagné',
        'Perdu' => 'Perdu',
        'Critique' => 'Critique',
    ];

    protected const PRIORITIES = [
        'Normal' => 'Normal',
        'Critique' => 'Critique',
        'Urgent' => 'Urgent',
    ];

    protected const SECTORS = [
        'Privé' => 'Privé',
        'Public' => 'Public',
        'ONG' => 'ONG',
        'Recherche' => 'Recherche',
    ];

    /** Badge de navigation : nombre de demandes encore "Nouveau". */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'Nouveau')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            // ══════════════════════════════════════════════════════════════════
            // 1. SUIVI — Décisions commerciales immédiates
            //    Ref · Statut · Priorité · Commercial → tout en une rangée
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Suivi de la demande')
                ->icon('heroicon-o-flag')
                ->columns(4)
                ->schema([
                    Forms\Components\TextInput::make('reference')
                        ->label('Référence')
                        ->prefix('#')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options(self::STATUSES)
                        ->required()
                        ->native(false)
                        ->live(),

                    Forms\Components\Select::make('priority')
                        ->label('Priorité')
                        ->options(self::PRIORITIES)
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('assigned_to')
                        ->label('Commercial assigné')
                        ->relationship('assignedUser', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Non assigné'),
                ]),

            // ══════════════════════════════════════════════════════════════════
            // 2. DEMANDE DU CLIENT — Non-collapsible : l'admin DOIT lire ça
            //    Specs (ce que le client a écrit) + services cochés (ce qu'il veut)
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Demande du client')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->description('Message et services tels que soumis par le client.')
                ->schema([
                    Forms\Components\Textarea::make('specifications')
                        ->label('Spécifications / message du client')
                        ->helperText('Texte libre saisi par le client lors de sa demande en ligne.')
                        ->rows(4)
                        ->columnSpanFull(),

                    Forms\Components\Fieldset::make('Services demandés par le client')
                        ->schema([
                            Forms\Components\Toggle::make('services_installation')
                                ->label('Installation sur site')
                                ->live()
                                ->inline(false),
                            Forms\Components\Toggle::make('services_formation')
                                ->label('Formation du personnel')
                                ->live()
                                ->inline(false),
                            Forms\Components\Toggle::make('services_maintenance')
                                ->label('Contrat de maintenance')
                                ->live()
                                ->inline(false),
                            Forms\Components\Toggle::make('is_urgent')
                                ->label('Besoin urgent (≤ 7 j)')
                                ->inline(false),
                        ])->columns(4),
                ]),

            // ══════════════════════════════════════════════════════════════════
            // 3. ÉTABLISSEMENT — Coordonnées du contact
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Établissement')
                ->icon('heroicon-o-building-office-2')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('responsible_name')
                        ->label('Nom du responsable')
                        ->required(),
                    Forms\Components\TextInput::make('structure_name')
                        ->label('Structure')
                        ->required(),
                    Forms\Components\TextInput::make('city')
                        ->label('Ville')
                        ->required(),
                    Forms\Components\Select::make('sector')
                        ->label('Secteur')
                        ->options(self::SECTORS)
                        ->required()
                        ->native(false),
                    Forms\Components\TextInput::make('phone')
                        ->label('Téléphone')
                        ->tel(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email(),
                ]),

            // ══════════════════════════════════════════════════════════════════
            // 4. ARTICLES — Saisie et tarification des produits
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Articles demandés')
                ->icon('heroicon-o-list-bullet')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->label('')
                        ->columns(12)
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->label('Produit catalogue')
                                ->relationship('product', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('— hors-catalogue —')
                                ->columnSpan(4),
                            Forms\Components\TextInput::make('custom_label')
                                ->label('Désignation libre')
                                ->placeholder('si hors-catalogue')
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('brand_name')
                                ->label('Marque')
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('quantity')
                                ->label('Qté')
                                ->numeric()
                                ->minValue(1)
                                ->default(1)
                                ->required()
                                ->live(onBlur: true)
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('unit_price_fcfa')
                                ->label('P.U. (FCFA)')
                                ->numeric()
                                ->default(0)
                                ->live(onBlur: true)
                                ->columnSpan(2),
                        ])
                        ->addActionLabel('Ajouter un article')
                        ->defaultItems(0)
                        ->columnSpanFull(),

                    // Alerte si tous les prix sont à 0 — évite d'envoyer un devis vide
                    Forms\Components\Placeholder::make('prix_zero_warning')
                        ->label('')
                        ->columnSpanFull()
                        ->content(fn (): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString(
                            '<div class="flex items-start gap-3 rounded-lg border border-warning-400 bg-warning-50 px-4 py-3 text-sm text-warning-800">'
                            . '<svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-warning-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>'
                            . '<span><strong>Tous les prix sont à 0 FCFA.</strong> Renseignez les prix unitaires avant d\'envoyer le devis au client.</span>'
                            . '</div>'
                        ))
                        ->visible(fn (Get $get): bool =>
                            collect($get('items') ?? [])->isNotEmpty()
                            && collect($get('items') ?? [])->every(fn ($item) => (int) ($item['unit_price_fcfa'] ?? 0) === 0)
                        ),
                ]),

            // ══════════════════════════════════════════════════════════════════
            // 5. RÉCAPITULATIF FINANCIER — Remise · Services · Transport · TVA
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Récapitulatif financier')
                ->icon('heroicon-o-calculator')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('transport_cost')
                        ->label('Frais logistique / transport')
                        ->numeric()
                        ->default(0)
                        ->suffix('FCFA')
                        ->live(onBlur: true),

                    Forms\Components\TextInput::make('discount_percent')
                        ->label('Remise globale (produits)')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->maxValue(100)
                        ->suffix('%')
                        ->live(onBlur: true),

                    Forms\Components\Toggle::make('vat_exempt')
                        ->label('Exonération TVA MINSANTÉ')
                        ->helperText('Désactive la TVA de 19,25 %')
                        ->live()
                        ->inline(false),

                    // Prix des services — visibles uniquement si le service a été coché
                    Forms\Components\TextInput::make('service_installation_cost')
                        ->label('Prix : Installation & mise en service')
                        ->numeric()
                        ->default(0)
                        ->suffix('FCFA')
                        ->live(onBlur: true)
                        ->visible(fn (Get $get) => (bool) $get('services_installation')),

                    Forms\Components\TextInput::make('service_formation_cost')
                        ->label('Prix : Formation technique')
                        ->numeric()
                        ->default(0)
                        ->suffix('FCFA')
                        ->live(onBlur: true)
                        ->visible(fn (Get $get) => (bool) $get('services_formation')),

                    Forms\Components\TextInput::make('service_maintenance_cost')
                        ->label('Prix : Contrat de maintenance')
                        ->numeric()
                        ->default(0)
                        ->suffix('FCFA')
                        ->live(onBlur: true)
                        ->visible(fn (Get $get) => (bool) $get('services_maintenance')),

                    Forms\Components\Placeholder::make('recap')
                        ->label('')
                        ->columnSpanFull()
                        ->content(fn (Get $get) => self::recapView($get)),
                ]),

            // ══════════════════════════════════════════════════════════════════
            // 6. LIEN CRM & PARC — Secondaire, collapsed par défaut
            //    Ne bloque pas le workflow de tarification
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Lien CRM & parc installé')
                ->icon('heroicon-o-link')
                ->description('Optionnel — relier cette cotation à un client enregistré dans le CRM.')
                ->collapsed()
                ->schema([
                    Forms\Components\Select::make('client_id')
                        ->label('Client CRM')
                        ->relationship('client', 'structure_name')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->placeholder('Rechercher par établissement…')
                        ->helperText('Requis pour l\'enregistrement automatique dans le parc lors d\'une cotation Gagné + Installation.')
                        ->columnSpanFull(),

                    // Notice auto-parc : visible quand les 3 conditions sont réunies
                    Forms\Components\Placeholder::make('auto_park_notice')
                        ->label('')
                        ->columnSpanFull()
                        ->content(fn (Get $get): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString(
                            '<div class="flex items-center gap-2 rounded-lg bg-success-50 border border-success-300 px-4 py-2 text-sm text-success-700">'
                            . '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                            . '<span>À l\'enregistrement, les équipements du catalogue seront ajoutés automatiquement au parc installé de ce client.</span>'
                            . '</div>'
                        ))
                        ->visible(fn (Get $get): bool =>
                            $get('status') === 'Gagné'
                            && (bool) $get('services_installation')
                            && (bool) $get('client_id')
                        ),
                ]),

            // ══════════════════════════════════════════════════════════════════
            // 7. NOTES INTERNES
            // ══════════════════════════════════════════════════════════════════
            Forms\Components\Section::make('Notes internes')
                ->icon('heroicon-o-pencil-square')
                ->collapsed()
                ->schema([
                    Forms\Components\Textarea::make('internal_notes')
                        ->label('Notes (non visibles du client)')
                        ->helperText('Remarques internes, conditions particulières, historique de négociation.')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    /** Construit le bloc HTML du récapitulatif financier à partir de l'état du formulaire. */
    protected static function recapView(Get $get): \Illuminate\Support\HtmlString
    {
        $items = $get('items') ?? [];
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (int) ($item['quantity'] ?? 0) * (int) ($item['unit_price_fcfa'] ?? 0);
        }

        $discountPercent = (int) ($get('discount_percent') ?? 0);
        $discountAmount = (int) round($subtotal * $discountPercent / 100);
        $afterDiscount = $subtotal - $discountAmount;

        $installationCost = (bool) $get('services_installation') ? (int) ($get('service_installation_cost') ?? 0) : 0;
        $formationCost    = (bool) $get('services_formation')    ? (int) ($get('service_formation_cost') ?? 0)    : 0;
        $maintenanceCost  = (bool) $get('services_maintenance')  ? (int) ($get('service_maintenance_cost') ?? 0)  : 0;
        $servicesTotal    = $installationCost + $formationCost + $maintenanceCost;

        $transport = (int) ($get('transport_cost') ?? 0);
        $base = $afterDiscount + $servicesTotal + $transport;
        $vatExempt = (bool) $get('vat_exempt');
        $tva = $vatExempt ? 0 : (int) round($base * 0.1925);
        $total = $base + $tva;

        $fmt = fn (int $n) => number_format($n, 0, ',', '.') . ' FCFA';

        $row = fn (string $label, string $value, bool $strong = false) => sprintf(
            '<div class="flex justify-between %s"><span>%s</span><span>%s</span></div>',
            $strong ? 'text-base font-bold text-danger-600 dark:text-danger-400 border-t border-gray-300 pt-2 mt-1' : 'text-sm text-gray-600 dark:text-gray-300',
            $label,
            $value
        );

        $html = '<div class="space-y-1 max-w-md ml-auto">';
        $html .= $row('Sous-total produits HT', $fmt($subtotal));
        if ($discountPercent > 0) {
            $html .= $row("Remise ({$discountPercent} %)", '− ' . $fmt($discountAmount));
        }
        if ($installationCost > 0) {
            $html .= $row('Installation & mise en service', $fmt($installationCost));
        }
        if ($formationCost > 0) {
            $html .= $row('Formation technique', $fmt($formationCost));
        }
        if ($maintenanceCost > 0) {
            $html .= $row('Contrat de maintenance', $fmt($maintenanceCost));
        }
        if ($transport > 0) {
            $html .= $row('Transport / logistique', $fmt($transport));
        }
        $html .= $row($vatExempt ? 'TVA (exonérée MINSANTÉ)' : 'TVA 19,25 %', $vatExempt ? '0 FCFA' : $fmt($tva));
        $html .= $row('TOTAL GÉNÉRAL', $fmt($total), true);
        $html .= '</div>';

        return new \Illuminate\Support\HtmlString($html);
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

                Tables\Columns\TextColumn::make('structure_name')
                    ->label('Client')
                    ->description(fn (Quotation $r) => $r->responsible_name)
                    ->searchable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('Localisation')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Demande')
                    ->counts('items')
                    ->formatStateUsing(fn (?int $state) => ($state ?? 0) . ' article' . (($state ?? 0) > 1 ? 's' : ''))
                    ->description(fn (Quotation $r) => $r->created_at?->format('d/m/Y')),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Nouveau' => 'gray',
                        'En cours', 'Devis Envoyé' => 'info',
                        'Gagné' => 'success',
                        'Urgent', 'Perdu' => 'danger',
                        'Critique' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Priorité')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Urgent' => 'danger',
                        'Critique' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('export_pipeline')
                    ->label('Export Pipeline')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Filtrer par statut')
                            ->options(self::STATUSES)
                            ->placeholder('Tous')
                            ->native(false),
                        Forms\Components\Select::make('city')
                            ->label('Filtrer par ville')
                            ->options(fn () => Quotation::query()->distinct()->orderBy('city')->pluck('city', 'city')->toArray())
                            ->placeholder('Toutes')
                            ->native(false),
                        Forms\Components\Select::make('year')
                            ->label('Année')
                            ->options(fn () => collect(range(now()->year, now()->year - 3))->mapWithKeys(fn ($y) => [$y => $y])->toArray())
                            ->default(now()->year)
                            ->native(false),
                    ])
                    ->action(function (array $data) {
                        return Excel::download(
                            new CotationsPipelineExport($data['status'] ?? null, $data['city'] ?? null, (int) ($data['year'] ?? now()->year)),
                            'cotations-pipeline-' . now()->format('Y-m-d') . '.xlsx'
                        );
                    }),

                Tables\Actions\Action::make('export_ca')
                    ->label('Export CA')
                    ->icon('heroicon-o-chart-bar')
                    ->color('gray')
                    ->form([
                        Forms\Components\Select::make('year')
                            ->label('Année')
                            ->options(fn () => collect(range(now()->year, now()->year - 3))->mapWithKeys(fn ($y) => [$y => $y])->toArray())
                            ->default(now()->year)
                            ->required()
                            ->native(false),
                    ])
                    ->action(function (array $data) {
                        return Excel::download(
                            new ChiffreAffairesExport((int) $data['year']),
                            'ca-annuel-' . $data['year'] . '.xlsx'
                        );
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(self::STATUSES),
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Priorité')
                    ->options(self::PRIORITIES),
                Tables\Filters\SelectFilter::make('city')
                    ->label('Ville')
                    ->options(fn () => Quotation::query()->distinct()->orderBy('city')->pluck('city', 'city')->toArray()),
                Tables\Filters\SelectFilter::make('sector')
                    ->label('Secteur')
                    ->options(self::SECTORS),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Traiter'),
                Tables\Actions\Action::make('devis_pdf')
                    ->label('Devis PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->url(fn (Quotation $record) => route('cotation.devis', $record))
                    ->openUrlInNewTab(),
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
            'index'  => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'edit'   => Pages\EditQuotation::route('/{record}/edit'),
        ];
    }
}
