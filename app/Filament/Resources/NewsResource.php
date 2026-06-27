<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Communication';
    protected static ?string $navigationLabel = 'Actualités';
    protected static ?string $modelLabel = 'Article';
    protected static ?string $pluralModelLabel = 'Actualités';
    protected static ?int $navigationSort = 1;

    protected const STATUSES = [
        'brouillon' => 'Brouillon',
        'publié' => 'Publié',
        'planifié' => 'Planifié',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->columnSpan(['lg' => 2])->schema([
                Forms\Components\Section::make('Contenu')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state, string $operation) =>
                                $operation === 'create' ? $set('slug', Str::slug($state ?? '')) : null)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->prefix('transtechno.cm/news/')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('excerpt')
                            ->label('Extrait / résumé')
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Affiché dans la liste des actualités.')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->label('Contenu')
                            ->disableToolbarButtons(['attachFiles'])
                            ->extraInputAttributes(['style' => 'min-height: 500px'])
                            ->columnSpanFull(),
                    ]),
            ]),

            Forms\Components\Group::make()->columnSpan(['lg' => 1])->schema([
                Forms\Components\Section::make('Publication')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options(self::STATUSES)
                            ->default('brouillon')
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Date de publication')
                            ->default(now())
                            ->helperText('Pour un article planifié, choisissez une date future.'),

                        Forms\Components\Select::make('author_id')
                            ->label('Auteur')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id()),

                        Forms\Components\Select::make('news_category_id')
                            ->label('Catégorie')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                                Forms\Components\TextInput::make('slug')->label('Slug')->required(),
                                Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
                            ]),

                        Forms\Components\Select::make('tags')
                            ->label('Mots-clés')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                                Forms\Components\TextInput::make('slug')->label('Slug')->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('Image à la une')
                    ->schema([
                        \App\Forms\Components\MediaPicker::make('cover_image_path')
                            ->label('')
                            ->helperText('Choisissez une image de la médiathèque ou téléversez-en une nouvelle.'),
                    ]),

                Forms\Components\Section::make('Référencement (SEO)')
                    ->description('Comment l\'article apparaît sur Google et les réseaux sociaux. Laissez vide pour utiliser le titre et l\'extrait.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Titre SEO')
                            ->maxLength(60)
                            ->live(onBlur: true)
                            ->placeholder(fn (Forms\Get $get) => $get('title') ?: 'Titre de l\'article')
                            ->helperText('~60 caractères max. Vide = le titre de l\'article est utilisé.'),

                        Forms\Components\Textarea::make('meta_description')
                            ->label('Description SEO')
                            ->rows(3)
                            ->maxLength(160)
                            ->live(onBlur: true)
                            ->placeholder(fn (Forms\Get $get) => $get('excerpt') ?: 'Résumé affiché sous le titre dans les résultats de recherche.')
                            ->helperText('~160 caractères max. Vide = l\'extrait est utilisé.'),

                        Forms\Components\Placeholder::make('seo_preview')
                            ->label('Aperçu Google')
                            ->content(function (Forms\Get $get): \Illuminate\Support\HtmlString {
                                $title = e($get('meta_title') ?: ($get('title') ?: 'Titre de l\'article'));
                                $desc = e($get('meta_description') ?: ($get('excerpt') ?: 'La description de l\'article apparaîtra ici…'));
                                $slug = e($get('slug') ?: 'votre-article');

                                return new \Illuminate\Support\HtmlString(
                                    '<div style="border:1px solid #e2e2e2;border-radius:8px;padding:12px 14px;background:#fff;font-family:Arial,sans-serif;max-width:100%;">'
                                    . '<div style="color:#202124;font-size:12px;line-height:1.3;">transtechno.cm › news › ' . $slug . '</div>'
                                    . '<div style="color:#1a0dab;font-size:18px;line-height:1.3;margin:2px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' . $title . '</div>'
                                    . '<div style="color:#4d5156;font-size:13px;line-height:1.4;">' . $desc . '</div>'
                                    . '</div>'
                                );
                            }),
                    ]),
            ]),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image_path')
                    ->label('')
                    ->getStateUsing(fn (News $record) => $record->coverUrl())
                    ->width(64)
                    ->height(44),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->weight('bold')
                    ->description(fn (News $r) => Str::limit($r->excerpt, 60))
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('author.name')
                    ->label('Auteur')
                    ->toggleable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => self::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'publié' => 'success',
                        'planifié' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publication')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(self::STATUSES),
                Tables\Filters\SelectFilter::make('news_category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'name'),
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
