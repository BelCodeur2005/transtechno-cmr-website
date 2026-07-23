<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Catalogue & Produits';
    protected static ?string $navigationLabel = 'Catégories';
    protected static ?string $modelLabel = 'Catégorie';
    protected static ?string $pluralModelLabel = 'Catégories';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom de la catégorie')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('type')
                    ->label('Hiérarchie')
                    ->options([
                        'equipement' => 'Équipement',
                        'reactif' => 'Réactif',
                    ])
                    ->default('equipement')
                    ->required()
                    ->helperText('Détermine dans quel méga-menu la catégorie apparaît'),

                Forms\Components\Select::make('icon')
                    ->label('Icône')
                    ->options(\App\Support\MaterialIcons::htmlOptions())
                    ->allowHtml()
                    ->searchable()
                    ->native(false)
                    ->placeholder('Choisir une icône')
                    ->helperText('Icône affichée dans les menus et le catalogue.'),

                Forms\Components\Select::make('parent_id')
                    ->label('Catégorie parente')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Aucune (catégorie racine)'),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Catégorie active')
                        ->default(true),

                    Forms\Components\TextInput::make('order')
                        ->label('Ordre d\'affichage')
                        ->numeric()
                        ->default(0),
                ]),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Catégorie')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Hiérarchie')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'reactif' ? 'Réactif' : 'Équipement')
                    ->color(fn (string $state) => $state === 'reactif' ? 'warning' : 'primary'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parente')
                    ->badge()
                    ->placeholder('—')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Produits liés')
                    ->counts('products')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('order')
                    ->label('Ordre')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Hiérarchie')
                    ->options([
                        'equipement' => 'Équipement',
                        'reactif' => 'Réactif',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')->label('Statut'),
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Catégorie parente')
                    ->relationship('parent', 'name'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
