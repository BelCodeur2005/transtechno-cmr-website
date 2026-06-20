<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Communication';
    protected static ?string $navigationLabel = 'Messages';
    protected static ?string $modelLabel = 'Message';
    protected static ?string $pluralModelLabel = 'Messages';
    protected static ?int $navigationSort = 4;

    protected const STATUSES = [
        'nouveau' => 'Nouveau',
        'lu' => 'Lu',
        'traité' => 'Traité',
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
            Forms\Components\Section::make('Expéditeur')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')->label('Nom')->disabled(),
                    Forms\Components\TextInput::make('email')->label('Email')->disabled(),
                    Forms\Components\TextInput::make('phone')->label('Téléphone')->disabled(),
                    Forms\Components\TextInput::make('structure')->label('Structure')->disabled(),
                    Forms\Components\TextInput::make('structure_type')->label("Type d'établissement")->disabled(),
                    Forms\Components\TextInput::make('subject')->label('Sujet')->disabled(),
                ]),

            Forms\Components\Section::make('Message')
                ->schema([
                    Forms\Components\Textarea::make('message')
                        ->label('')
                        ->disabled()
                        ->rows(6)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Suivi')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options(self::STATUSES)
                        ->default('nouveau')
                        ->required()
                        ->native(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Expéditeur')
                    ->description(fn (ContactMessage $r) => $r->structure)
                    ->weight(fn (ContactMessage $r) => $r->status === 'nouveau' ? 'bold' : null)
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Sujet')
                    ->placeholder('—')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Contact')
                    ->description(fn (ContactMessage $r) => $r->phone)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => self::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'nouveau' => 'danger',
                        'lu' => 'info',
                        'traité' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reçu le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')->options(self::STATUSES),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Lire')->icon('heroicon-m-envelope-open'),
                Tables\Actions\Action::make('marquer_traite')
                    ->label('Marquer traité')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (ContactMessage $r) => $r->status !== 'traité')
                    ->action(fn (ContactMessage $r) => $r->update(['status' => 'traité'])),
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
            'index' => Pages\ListContactMessages::route('/'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}
