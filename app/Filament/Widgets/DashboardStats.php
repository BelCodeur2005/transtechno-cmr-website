<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\QuotationResource;
use App\Filament\Resources\SavTicketResource;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SavTicket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class DashboardStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            $this->cotationsStat(),
            $this->produitsStat(),
            $this->savStat(),
            $this->caStat(),
        ];
    }

    private function cotationsStat(): Stat
    {
        $enAttente = Quotation::whereIn('status', ['Nouveau', 'En cours'])->count();

        // Sparkline : cotations créées sur les 7 derniers jours.
        $spark = $this->dailyCounts(Quotation::query(), 'created_at', 7);

        // Tendance : cette semaine vs semaine précédente.
        $thisWeek = Quotation::whereBetween('created_at', [now()->startOfWeek(), now()])->count();
        $lastWeek = Quotation::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        [$pct, $up] = $this->trend($thisWeek, $lastWeek);

        return Stat::make('Cotations en attente', number_format($enAttente, 0, ',', ' '))
            ->description($pct . ' cette semaine')
            ->descriptionIcon($up ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color('danger')
            ->chart($spark)
            ->icon('heroicon-o-document-currency-dollar')
            ->url(QuotationResource::getUrl())
            ->extraAttributes(['class' => 'cursor-pointer']);
    }

    private function produitsStat(): Stat
    {
        $actifs = Product::where('status', 'actif')->count();
        $reactifs = Product::where('status', 'actif')->where('product_type', 'reactif')->count();

        return Stat::make('Produits au catalogue', number_format($actifs, 0, ',', ' '))
            ->description($reactifs . ' réactif' . ($reactifs > 1 ? 's' : '') . ' ABM')
            ->descriptionIcon('heroicon-m-cube')
            ->color('primary')
            ->icon('heroicon-o-cube')
            ->url(ProductResource::getUrl())
            ->extraAttributes(['class' => 'cursor-pointer']);
    }

    private function savStat(): Stat
    {
        $semaine = SavTicket::where('opened_at', '>=', now()->startOfWeek())->count();
        $urgences = SavTicket::whereIn('status', ['Ouvert', 'En cours'])->where('priority', 'Urgent')->count();

        return Stat::make('Interventions SAV (semaine)', number_format($semaine, 0, ',', ' '))
            ->description($urgences . ' urgence' . ($urgences > 1 ? 's' : '') . ' en cours')
            ->descriptionIcon('heroicon-m-exclamation-triangle')
            ->color('warning')
            ->icon('heroicon-o-wrench-screwdriver')
            ->url(SavTicketResource::getUrl())
            ->extraAttributes(['class' => 'cursor-pointer']);
    }

    private function caStat(): Stat
    {
        $thisMonth = $this->wonRevenue(now()->startOfMonth(), now()->endOfMonth());
        $lastMonth = $this->wonRevenue(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth());
        [$pct, $up] = $this->trend($thisMonth, $lastMonth);

        return Stat::make('CA Mensuel (devis gagnés)', $this->compactFcfa($thisMonth))
            ->description($pct . ' vs mois dernier')
            ->descriptionIcon($up ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color('success')
            ->icon('heroicon-o-banknotes');
    }

    /** Somme des totaux TTC des devis gagnés sur une période. */
    private function wonRevenue(Carbon $start, Carbon $end): int
    {
        return (int) Quotation::with('items')
            ->where('status', 'Gagné')
            ->whereBetween('updated_at', [$start, $end])
            ->get()
            ->sum(fn (Quotation $q) => $q->totalTtc());
    }

    /** Comptes journaliers sur N derniers jours (pour sparkline). */
    private function dailyCounts($query, string $column, int $days): array
    {
        $counts = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $counts[] = (clone $query)
                ->whereBetween($column, [$day->copy()->startOfDay(), $day->copy()->endOfDay()])
                ->count();
        }

        return $counts;
    }

    /** @return array{0:string,1:bool} [libellé %, hausse ?] */
    private function trend(int|float $current, int|float $previous): array
    {
        if ($previous <= 0) {
            return [$current > 0 ? '+100 %' : '—', $current >= $previous];
        }
        $pct = round((($current - $previous) / $previous) * 100, 1);

        return [($pct >= 0 ? '+' : '') . $pct . ' %', $pct >= 0];
    }

    private function compactFcfa(int $n): string
    {
        if ($n >= 1_000_000) {
            return rtrim(rtrim(number_format($n / 1_000_000, 1, ',', ' '), '0'), ',') . ' M FCFA';
        }
        if ($n >= 1_000) {
            return number_format($n / 1_000, 0, ',', ' ') . ' k FCFA';
        }

        return number_format($n, 0, ',', ' ') . ' FCFA';
    }
}
