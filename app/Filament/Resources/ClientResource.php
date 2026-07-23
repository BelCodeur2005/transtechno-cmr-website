<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Commercial';
    protected static ?string $navigationLabel = 'Clients';
    protected static ?string $modelLabel = 'Client';
    protected static ?string $pluralModelLabel = 'Clients';
    protected static ?int $navigationSort = 3;

    protected const SECTORS = [
        'Privé' => 'Privé',
        'Public' => 'Public',
        'ONG' => 'ONG',
        'Recherche' => 'Recherche',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Établissement')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('structure_name')
                        ->label('Nom de la structure')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->placeholder('Ex : Clinique Pasteur, Hôpital Régional de Garoua'),

                    Forms\Components\TextInput::make('city')
                        ->label('Ville')
                        ->maxLength(255)
                        ->placeholder('Douala'),

                    Forms\Components\Select::make('sector')
                        ->label('Secteur')
                        ->options(self::SECTORS)
                        ->default('Privé')
                        ->native(false)
                        ->required(),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone')
                        ->label('Téléphone')
                        ->tel()
                        ->maxLength(50),

                    Forms\Components\Toggle::make('is_validated')
                        ->label('Client validé')
                        ->helperText('Un client validé peut bénéficier de l\'exonération TVA MINSANTÉ.')
                        ->default(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('structure_name')
                    ->label('Structure')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('Ville')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('sector')
                    ->label('Secteur')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Public' => 'info',
                        'Recherche' => 'warning',
                        'ONG' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('quotations_count')
                    ->label('Cotations')
                    ->counts('quotations')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('equipment_inventory_count')
                    ->label('Parc installé')
                    ->counts('equipmentInventory')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_validated')
                    ->label('Validé')
                    ->boolean(),
            ])
            ->defaultSort('structure_name')
            ->filters([
                Tables\Filters\SelectFilter::make('sector')->label('Secteur')->options(self::SECTORS),
                Tables\Filters\TernaryFilter::make('is_validated')->label('Validation'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
        return [
            RelationManagers\ContactsRelationManager::class,
            RelationManagers\QuotationsRelationManager::class,
            RelationManagers\EquipmentInventoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
