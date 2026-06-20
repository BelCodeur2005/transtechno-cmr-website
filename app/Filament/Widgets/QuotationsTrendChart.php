<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class QuotationsTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Évolution des cotations (6 mois)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = ['default' => 'full', 'lg' => 2];

    protected static ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $labels = [];
        $received = [];
        $won = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->locale('fr')->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $labels[] = ucfirst($month->translatedFormat('M Y'));
            $received[] = Quotation::whereBetween('created_at', [$start, $end])->count();
            $won[] = Quotation::where('status', 'Gagné')->whereBetween('updated_at', [$start, $end])->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Demandes reçues',
                    'data' => $received,
                    'backgroundColor' => 'rgba(0, 74, 128, 0.85)',
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Devis gagnés',
                    'data' => $won,
                    'backgroundColor' => 'rgba(22, 163, 74, 0.85)',
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'bottom'],
            ],
        ];
    }
}
