<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use Filament\Widgets\ChartWidget;

class QuotationsStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Cotations par statut';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = ['default' => 'full', 'lg' => 1];

    protected static ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $map = [
            'Nouveau' => '#9ca3af',
            'En cours' => '#006099',
            'Devis Envoyé' => '#3a6090',
            'Gagné' => '#16a34a',
            'Perdu' => '#E31E24',
            'Urgent' => '#ea580c',
            'Critique' => '#f59e0b',
        ];

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($map as $status => $color) {
            $count = Quotation::where('status', $status)->count();
            if ($count > 0) {
                $labels[] = $status;
                $data[] = $count;
                $colors[] = $color;
            }
        }

        return [
            'datasets' => [[
                'data' => $data ?: [1],
                'backgroundColor' => $colors ?: ['#e5e7eb'],
                'borderWidth' => 0,
            ]],
            'labels' => $labels ?: ['Aucune cotation'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'bottom'],
            ],
        ];
    }
}
