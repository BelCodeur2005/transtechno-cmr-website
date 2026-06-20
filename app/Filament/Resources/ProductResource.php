<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Catalogue & Produits';
    protected static ?string $navigationLabel = 'Produits';
    protected static ?string $modelLabel = 'Produit';
    protected static ?string $pluralModelLabel = 'Produits';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make()->tabs([

                // ── Onglet Général ───────────────────────────────
                Forms\Components\Tabs\Tab::make('Général')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du produit')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('reference')
                            ->label('Référence')
                            ->maxLength(255)
                            ->placeholder('ex. BS-050108-A02'),

                        Forms\Components\Select::make('product_type')
                            ->label('Type de produit')
                            ->options([
                                'equipement' => 'Équipement',
                                'reactif' => 'Réactif (ABM)',
                                'consommable' => 'Consommable',
                            ])
                            ->default('equipement')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('brand_id')
                            ->label('Marque')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('category_id')
                            ->label('Catégorie')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('poles')
                            ->label('Pôles d\'expertise')
                            ->relationship('poles', 'name')
                            ->multiple()
                            ->preload()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('short_description')
                            ->label('Description courte')
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('long_description')
                            ->label('Description longue')
                            ->disableToolbarButtons(['attachFiles'])
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('price_fcfa')
                                ->label('Prix (FCFA)')
                                ->numeric()
                                ->suffix('FCFA'),

                            Forms\Components\Toggle::make('price_visible')
                                ->label('Prix visible au public')
                                ->default(false),

                            Forms\Components\Toggle::make('is_featured')
                                ->label('Produit vedette')
                                ->default(false),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('status')
                                ->label('Statut')
                                ->options([
                                    'actif' => 'Actif',
                                    'brouillon' => 'Brouillon',
                                    'archivé' => 'Archivé',
                                ])
                                ->default('brouillon')
                                ->required(),

                            Forms\Components\TextInput::make('order')
                                ->label('Ordre d\'affichage')
                                ->numeric()
                                ->default(0),
                        ]),
                    ])->columns(2),

                // ── Onglet Spécifications ────────────────────────
                Forms\Components\Tabs\Tab::make('Spécifications')
                    ->icon('heroicon-o-list-bullet')
                    ->schema([
                        Forms\Components\Repeater::make('specs')
                            ->label('Caractéristiques techniques')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Caractéristique')
                                    ->required()
                                    ->placeholder('ex. Plage de mesures'),
                                Forms\Components\TextInput::make('value')
                                    ->label('Valeur')
                                    ->required()
                                    ->placeholder('ex. 0–4.3 OD'),
                            ])
                            ->orderColumn('order')
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter une caractéristique')
                            ->collapsible(),
                    ]),

                // ── Onglet Médias ────────────────────────────────
                Forms\Components\Tabs\Tab::make('Médias')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\Repeater::make('media')
                            ->label('Images & fiches techniques')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Type')
                                    ->options([
                                        'image' => 'Image',
                                        'pdf' => 'PDF (fiche technique)',
                                        'video' => 'Vidéo',
                                    ])
                                    ->default('image')
                                    ->required()
                                    ->live(),

                                // Champs virtuels distincts (image_path / file_path) : ils ne partagent pas
                                // l'état avec « path », ce qui évite que le FileUpload n'écrase l'aperçu de l'image.
                                // La consolidation vers la colonne « path » se fait dans les callbacks ci-dessous.
                                \App\Forms\Components\MediaPicker::make('image_path')
                                    ->label('Image')
                                    ->visible(fn (Forms\Get $get) => ($get('type') ?? 'image') === 'image'),

                                Forms\Components\FileUpload::make('file_path')
                                    ->label('Fichier (PDF / vidéo)')
                                    ->disk('s3')
                                    ->directory('products')
                                    ->acceptedFileTypes(['application/pdf', 'video/*'])
                                    ->maxSize(10240)
                                    ->validationMessages([
                                        'max'   => 'Le fichier est trop lourd. Taille maximale autorisée : 10 Mo.',
                                        'mimes' => 'Seuls les formats PDF et vidéo sont acceptés.',
                                    ])
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false)
                                    ->helperText('PDF ou vidéo · 10 Mo max.')
                                    ->visible(fn (Forms\Get $get) => ($get('type') ?? 'image') !== 'image'),

                                // Le texte alternatif et le titre sont définis dans la médiathèque
                                // (et hérités automatiquement à l'enregistrement) — pas de double saisie ici.
                                Forms\Components\Toggle::make('is_cover')
                                    ->label('Image de couverture')
                                    ->columnSpanFull()
                                    ->visible(fn (Forms\Get $get) => ($get('type') ?? 'image') === 'image'),
                            ])
                            // Remplit le bon champ virtuel à partir de la colonne « path » à l'ouverture.
                            ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                                if (($data['type'] ?? 'image') === 'image') {
                                    $data['image_path'] = $data['path'] ?? null;
                                } else {
                                    $data['file_path'] = $data['path'] ?? null;
                                }
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => self::consolidateMediaPath($data))
                            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => self::consolidateMediaPath($data))
                            ->orderColumn('order')
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un média')
                            ->collapsible(),
                    ]),

                // ── Onglet Stock ─────────────────────────────────
                Forms\Components\Tabs\Tab::make('Stock')
                    ->icon('heroicon-o-archive-box')
                    ->schema([
                        Forms\Components\Select::make('stock_status')
                            ->label('Statut du stock')
                            ->options([
                                'en_stock' => 'En stock (Douala)',
                                'en_transit' => 'En transit',
                                'rupture' => 'Rupture',
                            ])
                            ->default('en_stock')
                            ->required(),

                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('Quantité indicative')
                            ->numeric(),
                    ])->columns(2),

                // ── Onglet Réactif ABM (conditionnel) ────────────
                Forms\Components\Tabs\Tab::make('Détails Réactif (ABM)')
                    ->icon('heroicon-o-beaker')
                    ->visible(fn (Get $get) => $get('product_type') === 'reactif')
                    ->schema([
                        Forms\Components\Group::make()
                            ->relationship('reagentDetail')
                            ->schema([
                                Forms\Components\Select::make('storage_temp')
                                    ->label('Température de stockage')
                                    ->options([
                                        'Ambiante' => 'Ambiante',
                                        '+2-8°C' => '+2-8°C',
                                        '-20°C' => '-20°C',
                                        '-80°C' => '-80°C',
                                    ])
                                    ->default('Ambiante')
                                    ->required(),

                                Forms\Components\TextInput::make('conditionnement')
                                    ->label('Conditionnement')
                                    ->placeholder('ex. Kit 100 réactions'),

                                Forms\Components\TextInput::make('volume_quantity')
                                    ->label('Volume / Quantité')
                                    ->placeholder('ex. 1 mL, 100 µg'),

                                Forms\Components\Toggle::make('is_ivd')
                                    ->label('Certifié IVD (sinon usage Recherche)'),

                                Forms\Components\TextInput::make('lot_number_example')
                                    ->label('N° lot (exemple)'),

                                Forms\Components\TextInput::make('expiry_note')
                                    ->label('Note de conservation')
                                    ->placeholder('ex. 12 mois à -20°C'),
                            ])
                            ->columns(2),
                    ]),

                Forms\Components\Tabs\Tab::make('Produits similaires')
                    ->icon('heroicon-o-squares-2x2')
                    ->schema([
                        Forms\Components\Select::make('relatedProducts')
                            ->label('Produits liés')
                            ->helperText('Ces produits apparaîtront dans la section "Vous aimerez aussi" en bas de cette fiche. Prioritaires sur la sélection automatique par catégorie.')
                            ->relationship('relatedProducts', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn (Product $r) => $r->name . ($r->reference ? '  ·  ' . $r->reference : ''))
                            ->placeholder('Chercher un produit à lier…')
                            ->maxItems(6)
                            ->columnSpanFull(),
                    ]),

            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('coverImage.path')
                    ->label('')
                    ->width(50)
                    ->height(50)
                    ->getStateUsing(fn (Product $p) => $p->coverImage?->path ? url('/media-proxy/' . $p->coverImage->path) : null),

                Tables\Columns\TextColumn::make('name')
                    ->label('Produit')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Product $record) => $record->reference),

                Tables\Columns\TextColumn::make('product_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'equipement' => 'Équipement',
                        'reactif' => 'Réactif',
                        'consommable' => 'Consommable',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'reactif' => 'warning',
                        'consommable' => 'gray',
                        default => 'primary',
                    }),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Marque')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('price_fcfa')
                    ->label('Prix')
                    ->numeric(thousandsSeparator: '.')
                    ->suffix(' FCFA')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_status')
                    ->label('Stock')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'en_stock' => 'En stock',
                        'en_transit' => 'En transit',
                        'rupture' => 'Rupture',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'en_stock' => 'success',
                        'en_transit' => 'info',
                        'rupture' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'actif' => 'success',
                        'brouillon' => 'gray',
                        'archivé' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Vedette')
                    ->boolean()
                    ->toggleable(),
            ])
            ->defaultSort('order')
            ->filters([
                Tables\Filters\SelectFilter::make('product_type')
                    ->label('Type')
                    ->options([
                        'equipement' => 'Équipement',
                        'reactif' => 'Réactif',
                        'consommable' => 'Consommable',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'actif' => 'Actif',
                        'brouillon' => 'Brouillon',
                        'archivé' => 'Archivé',
                    ]),
                Tables\Filters\SelectFilter::make('stock_status')
                    ->label('Stock')
                    ->options([
                        'en_stock' => 'En stock',
                        'en_transit' => 'En transit',
                        'rupture' => 'Rupture',
                    ]),
                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Marque')
                    ->relationship('brand', 'name'),
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

    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Consolide les champs virtuels image_path / file_path d'un média vers la colonne « path »
     * avant enregistrement, puis retire ces champs virtuels (ils ne sont pas des colonnes).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function consolidateMediaPath(array $data): array
    {
        $isImage = ($data['type'] ?? 'image') === 'image';
        $data['path'] = $isImage ? ($data['image_path'] ?? null) : ($data['file_path'] ?? null);

        // Hérite du texte alternatif et du titre définis dans la médiathèque (pas de double saisie).
        if ($isImage && ! empty($data['path'])) {
            $media = \App\Models\Media::where('path', $data['path'])->first(['alt', 'title']);
            if ($media) {
                $data['alt'] = $media->alt;
                $data['title'] = $media->title;
            }
        }

        unset($data['image_path'], $data['file_path']);

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
