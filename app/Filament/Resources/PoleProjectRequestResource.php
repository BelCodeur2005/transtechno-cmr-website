<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PoleProjectRequestResource\Pages;
use App\Models\PoleProjectRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PoleProjectRequestResource extends Resource
{
    protected static ?string $model = PoleProjectRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Commercial';
    protected static ?string $navigationLabel = 'Demandes de projet';
    protected static ?string $modelLabel = 'Demande de projet';
    protected static ?string $pluralModelLabel = 'Demandes de projet';
    protected static ?int $navigationSort = 2;

    protected const STATUSES = [
        'nouveau' => 'Nouveau',
        'en_cours' => 'En cours',
        'traité' => 'Traité',
    ];

    protected const POLE_TYPES = [
        'hemodialyse' => 'Hémodialyse',
        'abm' => 'Réactifs ABM',
        'autre' => 'Autre',
    ];

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'nouveau')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Demandeur')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')->label('Nom complet')->required(),
                    Forms\Components\TextInput::make('structure')->label('Établissement'),
                    Forms\Components\TextInput::make('phone')->label('Téléphone')->tel(),
                    Forms\Components\TextInput::make('email')->label('Email')->email(),
                    Forms\Components\Select::make('pole_type')
                        ->label('Pôle')
                        ->options(self::POLE_TYPES)
                        ->default('hemodialyse'),
                ]),

            Forms\Components\Section::make('Demande')
                ->schema([
                    Forms\Components\Textarea::make('message')
                        ->label('Message / détails du projet')
                        ->rows(6)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Suivi')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options(self::STATUSES)
                        ->default('nouveau')
                        ->required()
                        ->native(false),
                    Forms\Components\Select::make('assigned_to')
                        ->label('Assigné à')
                        ->relationship('assignedTo', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Non assigné'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Demandeur')
                    ->description(fn (PoleProjectRequest $r) => $r->structure)
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->icon('heroicon-m-phone')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('pole_type')
                    ->label('Pôle')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => self::POLE_TYPES[$state] ?? $state)
                    ->color('info'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => self::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'nouveau' => 'warning',
                        'en_cours' => 'info',
                        'traité' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reçue le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')->options(self::STATUSES),
                Tables\Filters\SelectFilter::make('pole_type')->label('Pôle')->options(self::POLE_TYPES),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPoleProjectRequests::route('/'),
            'edit' => Pages\EditPoleProjectRequest::route('/{record}/edit'),
        ];
    }
}
