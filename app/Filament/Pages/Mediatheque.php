<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Mediatheque extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Communication';
    protected static ?string $navigationLabel = 'Médiathèque';
    protected static ?string $title = 'Médiathèque';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.mediatheque';
}
