<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PoleResource\Pages;
use App\Models\Pole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PoleResource extends Resource
{
    protected static ?string $model = Pole::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Catalogue & Produits';
    protected static ?string $navigationLabel = 'Pôles d\'expertise';
    protected static ?string $modelLabel = 'Pôle d\'expertise';
    protected static ?string $pluralModelLabel = 'Pôles d\'expertise';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom du pôle')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('icon')
                    ->label('Icône')
                    ->options(\App\Support\MaterialIcons::htmlOptions())
                    ->allowHtml()
                    ->searchable()
                    ->native(false)
                    ->placeholder('Choisir une icône'),

                Forms\Components\ColorPicker::make('color')
                    ->label('Couleur du pôle'),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Pôle actif')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label('Couleur'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Pôle')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Produits liés')
                    ->counts('products')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Statut'),
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
            'index' => Pages\ListPoles::route('/'),
            'create' => Pages\CreatePole::route('/create'),
            'edit' => Pages\EditPole::route('/{record}/edit'),
        ];
    }
}
