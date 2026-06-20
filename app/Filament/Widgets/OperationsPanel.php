<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SavTicket;
use Filament\Widgets\Widget;

class OperationsPanel extends Widget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = ['default' => 'full', 'lg' => 1];

    protected static string $view = 'filament.widgets.operations-panel';

    protected function getViewData(): array
    {
        return [
            'stockAlerts' => $this->stockAlerts(),
            'activity' => $this->activity(),
        ];
    }

    /** Produits en rupture ou stock critique (≤ 5). */
    private function stockAlerts(): array
    {
        return Product::query()
            ->where(fn ($q) => $q->where('stock_status', 'rupture')
                ->orWhere(fn ($w) => $w->whereNotNull('stock_quantity')->where('stock_quantity', '<=', 5)))
            ->orderByRaw("FIELD(stock_status, 'rupture') desc")
            ->limit(4)
            ->get(['id', 'name', 'reference', 'stock_status', 'stock_quantity'])
            ->map(fn (Product $p) => [
                'name' => $p->name,
                'reference' => $p->reference,
                'critical' => $p->stock_status === 'rupture',
                'label' => $p->stock_status === 'rupture'
                    ? 'Rupture de stock'
                    : 'Stock critique : ' . (int) $p->stock_quantity . ' unité' . ((int) $p->stock_quantity > 1 ? 's' : ''),
            ])
            ->all();
    }

    /** Fil d'activité récente, agrégé depuis plusieurs sources. */
    private function activity(): array
    {
        $events = collect();

        Quotation::query()->latest()->limit(4)->get()->each(function (Quotation $q) use ($events) {
            $events->push([
                'icon' => 'heroicon-m-document-currency-dollar',
                'color' => 'text-primary-600',
                'text' => 'Nouvelle demande de cotation <span class="font-semibold">#' . e($q->reference) . '</span> — ' . e($q->structure_name),
                'at' => $q->created_at,
            ]);
        });

        SavTicket::query()->latest('opened_at')->limit(3)->get()->each(function (SavTicket $t) use ($events) {
            $events->push([
                'icon' => 'heroicon-m-wrench-screwdriver',
                'color' => 'text-warning-600',
                'text' => 'Ticket SAV <span class="font-semibold">#' . e($t->reference) . '</span> ouvert',
                'at' => $t->opened_at ?? $t->created_at,
            ]);
        });

        ContactMessage::query()->latest()->limit(3)->get()->each(function (ContactMessage $m) use ($events) {
            $events->push([
                'icon' => 'heroicon-m-envelope',
                'color' => 'text-info-600',
                'text' => 'Nouveau message de <span class="font-semibold">' . e($m->name) . '</span>',
                'at' => $m->created_at,
            ]);
        });

        return $events
            ->filter(fn ($e) => $e['at'] !== null)
            ->sortByDesc('at')
            ->take(6)
            ->map(fn ($e) => [
                ...$e,
                'ago' => $e['at']->diffForHumans(),
            ])
            ->values()
            ->all();
    }
}
