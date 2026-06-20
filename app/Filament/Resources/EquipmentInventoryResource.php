<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentInventoryResource\Pages;
use App\Models\EquipmentInventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class EquipmentInventoryResource extends Resource
{
    protected static ?string $model = EquipmentInventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'Technique';
    protected static ?string $navigationLabel = 'Parc installé';
    protected static ?string $modelLabel = 'Équipement installé';
    protected static ?string $pluralModelLabel = 'Parc installé';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Équipement installé')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('client_id')
                        ->label('Client')
                        ->relationship('client', 'structure_name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('structure_name')->label('Nom de la structure')->required(),
                            Forms\Components\TextInput::make('city')->label('Ville'),
                            Forms\Components\Select::make('sector')->label('Secteur')
                                ->options(['Privé' => 'Privé', 'Public' => 'Public', 'ONG' => 'ONG', 'Recherche' => 'Recherche'])
                                ->default('Privé'),
                        ]),

                    Forms\Components\Select::make('product_id')
                        ->label('Produit')
                        ->relationship('product', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('serial_number')
                        ->label('N° de série')
                        ->maxLength(255)
                        ->placeholder('Identifiant unique de l\'appareil'),

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
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.structure_name')
                    ->label('Client')
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Équipement')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('serial_number')
                    ->label('N° de série')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('installation_date')
                    ->label('Mise en service')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('warranty_expiry')
                    ->label('Garantie')
                    ->badge()
                    ->placeholder('—')
                    ->formatStateUsing(fn ($state) => $state
                        ? (Carbon::parse($state)->isPast()
                            ? 'Expirée — ' . Carbon::parse($state)->format('d/m/Y')
                            : 'Active — ' . Carbon::parse($state)->format('d/m/Y'))
                        : null)
                    ->color(fn ($state) => $state === null
                        ? 'gray'
                        : (Carbon::parse($state)->isPast() ? 'danger' : 'success'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('sav_tickets_count')
                    ->label('Tickets SAV')
                    ->counts('savTickets')
                    ->badge()
                    ->color('warning'),
            ])
            ->defaultSort('installation_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'structure_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('warranty_expired')
                    ->label('Garantie expirée')
                    ->query(fn ($query) => $query->whereNotNull('warranty_expiry')->where('warranty_expiry', '<', now())),
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
            'index' => Pages\ListEquipmentInventories::route('/'),
            'create' => Pages\CreateEquipmentInventory::route('/create'),
            'edit' => Pages\EditEquipmentInventory::route('/{record}/edit'),
        ];
    }
}
