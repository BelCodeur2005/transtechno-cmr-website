<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class EquipmentInventoryRelationManager extends RelationManager
{
    protected static string $relationship = 'equipmentInventory';

    protected static ?string $title = 'Parc installé';

    protected static ?string $modelLabel = 'équipement installé';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_id')
                ->label('Produit')
                ->relationship('product', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\TextInput::make('serial_number')
                ->label('N° de série')
                ->maxLength(255),

            Forms\Components\DatePicker::make('installation_date')
                ->label('Date de mise en service')
                ->displayFormat('d/m/Y'),

            Forms\Components\DatePicker::make('warranty_expiry')
                ->label('Fin de garantie')
                ->displayFormat('d/m/Y'),

            Forms\Components\DatePicker::make('last_maintenance_date')
                ->label('Dernière maintenance')
                ->displayFormat('d/m/Y'),

            Forms\Components\Textarea::make('notes')
                ->label('Notes')
                ->rows(2)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('serial_number')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Équipement')
                    ->weight('bold')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('serial_number')
                    ->label('N° de série')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('installation_date')
                    ->label('Mise en service')
                    ->date('d/m/Y')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('warranty_expiry')
                    ->label('Garantie')
                    ->badge()
                    ->placeholder('—')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y') : null)
                    ->color(fn ($state) => $state === null
                        ? 'gray'
                        : (Carbon::parse($state)->isPast() ? 'danger' : 'success')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Ajouter un équipement'),
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
}
