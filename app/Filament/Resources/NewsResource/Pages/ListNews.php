<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use App\Models\News;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nouvel article'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous'),

            'publie' => Tab::make('Publiés')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'publié'))
                ->badge(News::where('status', 'publié')->count())
                ->badgeColor('success'),

            'brouillon' => Tab::make('Brouillons')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'brouillon'))
                ->badge(News::where('status', 'brouillon')->count()),

            'planifie' => Tab::make('Planifiés')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'planifié'))
                ->badge(News::where('status', 'planifié')->count())
                ->badgeColor('warning'),
        ];
    }
}
