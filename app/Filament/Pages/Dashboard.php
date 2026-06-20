<?php

namespace App\Filament\Pages;

use App\Filament\Resources\NewsResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\LatestQuotations;
use App\Filament\Widgets\OperationsPanel;
use App\Filament\Widgets\QuotationsStatusChart;
use App\Filament\Widgets\QuotationsTrendChart;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Carbon;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Tableau de Bord';
    protected static ?string $title = 'Tableau de Bord';

    public function getHeading(): string
    {
        return 'Tableau de Bord';
    }

    public function getSubheading(): ?string
    {
        $date = ucfirst(Carbon::now()->locale('fr')->translatedFormat('l j F Y'));

        return "Aperçu général de l'activité Transtechno Cameroun · {$date}";
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('new_product')
                    ->label('Nouveau produit')
                    ->icon('heroicon-m-cube')
                    ->url(ProductResource::getUrl('create')),
                Action::make('new_news')
                    ->label('Nouvelle actualité')
                    ->icon('heroicon-m-newspaper')
                    ->url(NewsResource::getUrl('create')),
            ])
                ->label('Nouveau dossier')
                ->icon('heroicon-m-plus')
                ->button()
                ->color('primary'),
        ];
    }

    public function getColumns(): int|array
    {
        return 3;
    }

    public function getWidgets(): array
    {
        return [
            DashboardStats::class,
            QuotationsTrendChart::class,
            QuotationsStatusChart::class,
            LatestQuotations::class,
            OperationsPanel::class,
        ];
    }
}
