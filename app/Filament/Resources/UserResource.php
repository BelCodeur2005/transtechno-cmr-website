<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Système';
    protected static ?string $navigationLabel = 'Utilisateurs & Rôles';
    protected static ?string $modelLabel = 'Utilisateur';
    protected static ?string $pluralModelLabel = 'Utilisateurs';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Compte')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom complet')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('job_title')
                        ->label('Fonction / Poste')
                        ->placeholder('Ex. : Commercial Senior')
                        ->maxLength(100)
                        ->prefixIcon('heroicon-o-briefcase'),

                    Forms\Components\TextInput::make('phone')
                        ->label('Téléphone')
                        ->tel()
                        ->placeholder('+237 6__ __ __ __')
                        ->maxLength(30)
                        ->prefixIcon('heroicon-o-phone'),

                    Forms\Components\TextInput::make('password')
                        ->label('Mot de passe')
                        ->password()
                        ->revealable()
                        ->minLength(12)
                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->helperText('Minimum 12 caractères. Laisser vide en édition pour ne pas changer.')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Rôles & accès')
                ->description('Définit ce que l\'utilisateur peut faire dans le back-office.')
                ->schema([
                    Forms\Components\Select::make('roles')
                        ->label('Rôles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->required()
                        ->helperText('Au moins un rôle est nécessaire pour accéder au back-office.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->weight('bold')
                    ->description(fn (User $r) => $r->job_title ?? $r->email)
                    ->searchable(['name', 'email'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rôles')
                    ->badge()
                    ->color('primary')
                    ->placeholder('Aucun rôle'),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Vérifié')
                    ->dateTime('d/m/Y')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Rôle')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    // Empêche la suppression de son propre compte.
                    ->visible(fn (User $record) => $record->id !== auth()->id()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            $records->reject(fn (User $u) => $u->id === auth()->id())
                                ->each->delete();
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
