<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use App\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $modelLabel = 'Média';
    protected static ?string $pluralModelLabel = 'Médiathèque';

    // La navigation passe par la page Médiathèque (UI WordPress) ;
    // cette ressource sert l'upload/recadrage et l'édition d'un média.
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Fichier')
                ->description('Formats PNG, JPG, WEBP — recadrage et redimensionnement disponibles via l\'éditeur.')
                ->schema([
                    Forms\Components\FileUpload::make('path')
                        ->label('')
                        ->image()
                        ->disk('s3')
                        ->directory('media')
                        ->imageEditor()
                        ->imageEditorAspectRatios([null, '16:9', '4:3', '1:1'])
                        ->imageEditorMode(2)
                        ->maxSize(4096)
                        ->required()
                        ->storeFileNamesIn('filename')
                        ->helperText('Cliquez sur le crayon après l\'upload pour recadrer ou redimensionner.'),
                ]),

            Forms\Components\Section::make('Métadonnées')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('alt')
                        ->label('Texte alternatif')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Description de l\'image pour l\'accessibilité et le SEO.')
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('title')
                        ->label('Titre')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('caption')
                        ->label('Légende')
                        ->maxLength(255),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Tables\Columns\ImageColumn::make('path')
                        ->label('')
                        ->getStateUsing(fn (Media $record) => $record->url())
                        ->height(160)
                        ->extraImgAttributes(['class' => 'w-full object-cover rounded-t-lg'])
                        ->extraAttributes(['class' => 'w-full']),

                    Stack::make([
                        Tables\Columns\TextColumn::make('title')
                            ->weight('bold')
                            ->getStateUsing(fn (Media $r) => $r->title ?: $r->filename)
                            ->searchable(['title', 'filename', 'alt']),

                        Tables\Columns\TextColumn::make('alt')
                            ->color('gray')
                            ->size('sm')
                            ->limit(48)
                            ->placeholder('— pas de texte alternatif —'),

                        Tables\Columns\TextColumn::make('size')
                            ->color('gray')
                            ->size('xs')
                            ->getStateUsing(fn (Media $r) => $r->humanSize()),
                    ])->space(1)->extraAttributes(['class' => 'p-3']),
                ])->extraAttributes(['class' => 'overflow-hidden rounded-lg border border-gray-200 bg-white']),
            ])
            ->contentGrid(['md' => 2, 'lg' => 3, 'xl' => 4])
            ->paginated([12, 24, 48])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton()->icon('heroicon-m-pencil-square'),
                Tables\Actions\DeleteAction::make()->iconButton()->icon('heroicon-m-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Médiathèque vide')
            ->emptyStateDescription('Téléversez vos premières images pour les réutiliser dans les articles et fiches produit.')
            ->emptyStateIcon('heroicon-o-photo');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
