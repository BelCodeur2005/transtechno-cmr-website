<?php

namespace App\Exports;

use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ChiffreAffairesExport implements WithMultipleSheets
{
    public function __construct(private readonly int $year) {}

    public function sheets(): array
    {
        return [
            new CaParMoisSheet($this->year),
            new CaParVilleSheet($this->year),
            new CaParCommercialSheet($this->year),
        ];
    }
}

// ── Feuille 1 : CA par mois ──────────────────────────────────────────────────

class CaParMoisSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private readonly int $year) {}

    public function title(): string { return 'CA par mois'; }

    public function headings(): array
    {
        return ['Mois', 'Devis gagnés', 'Sous-total HT (FCFA)', 'Total TTC (FCFA)', 'Panier moyen TTC (FCFA)'];
    }

    public function collection(): Collection
    {
        $months = collect();
        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create($this->year, $m, 1)->startOfMonth();
            $end   = Carbon::create($this->year, $m, 1)->endOfMonth();

            $won = Quotation::with('items')
                ->where('status', 'Gagné')
                ->whereBetween('updated_at', [$start, $end])
                ->get();

            $count = $won->count();
            $ht    = $won->sum(fn (Quotation $q) => $q->subtotalHt());
            $ttc   = $won->sum(fn (Quotation $q) => $q->totalTtc());

            $months->push([
                'mois'    => $start->locale('fr')->translatedFormat('F Y'),
                'count'   => $count,
                'ht'      => $ht,
                'ttc'     => $ttc,
                'avg_ttc' => $count > 0 ? intdiv($ttc, $count) : 0,
            ]);
        }

        return $months;
    }

    public function map($row): array
    {
        return [
            ucfirst($row['mois']),
            $row['count'],
            number_format($row['ht'],  0, ',', ' '),
            number_format($row['ttc'], 0, ',', ' '),
            number_format($row['avg_ttc'], 0, ',', ' '),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '004A80']]]];
    }
}

// ── Feuille 2 : CA par ville ─────────────────────────────────────────────────

class CaParVilleSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private readonly int $year) {}

    public function title(): string { return 'CA par ville'; }

    public function headings(): array
    {
        return ['Ville', 'Devis gagnés', 'Total TTC (FCFA)', 'Part (%)'];
    }

    public function collection(): Collection
    {
        $won = Quotation::with('items')
            ->where('status', 'Gagné')
            ->whereYear('updated_at', $this->year)
            ->get();

        $totalTtc = $won->sum(fn (Quotation $q) => $q->totalTtc()) ?: 1;

        return $won->groupBy('city')->map(function (Collection $group, string $city) use ($totalTtc) {
            $ttc = $group->sum(fn (Quotation $q) => $q->totalTtc());
            return [
                'city'  => $city ?: 'Non renseigné',
                'count' => $group->count(),
                'ttc'   => $ttc,
                'pct'   => round($ttc / $totalTtc * 100, 1),
            ];
        })->sortByDesc('ttc')->values();
    }

    public function map($row): array
    {
        return [
            $row['city'],
            $row['count'],
            number_format($row['ttc'], 0, ',', ' '),
            $row['pct'] . ' %',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '004A80']]]];
    }
}

// ── Feuille 3 : CA par commercial ────────────────────────────────────────────

class CaParCommercialSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private readonly int $year) {}

    public function title(): string { return 'CA par commercial'; }

    public function headings(): array
    {
        return ['Commercial', 'Devis gagnés', 'Total TTC (FCFA)', 'Taux de conversion (%)', 'Panier moyen TTC (FCFA)'];
    }

    public function collection(): Collection
    {
        $allByUser = Quotation::whereYear('created_at', $this->year)
            ->whereNotNull('assigned_to')
            ->selectRaw('assigned_to, COUNT(*) as total')
            ->groupBy('assigned_to')
            ->pluck('total', 'assigned_to');

        $wonByUser = Quotation::with('items')
            ->where('status', 'Gagné')
            ->whereYear('updated_at', $this->year)
            ->whereNotNull('assigned_to')
            ->get()
            ->groupBy('assigned_to');

        return User::whereIn('id', $wonByUser->keys()->merge($allByUser->keys())->unique())
            ->get(['id', 'name'])
            ->map(function (User $user) use ($wonByUser, $allByUser) {
                $wonGroup = $wonByUser->get($user->id, collect());
                $ttc = $wonGroup->sum(fn (Quotation $q) => $q->totalTtc());
                $count = $wonGroup->count();
                $total = (int) ($allByUser->get($user->id) ?? 0);
                return [
                    'name'       => $user->name,
                    'won'        => $count,
                    'ttc'        => $ttc,
                    'conversion' => $total > 0 ? round($count / $total * 100, 1) : 0.0,
                    'avg_ttc'    => $count > 0 ? intdiv($ttc, $count) : 0,
                ];
            })
            ->sortByDesc('ttc')
            ->values();
    }

    public function map($row): array
    {
        return [
            $row['name'],
            $row['won'],
            number_format($row['ttc'],     0, ',', ' '),
            $row['conversion'] . ' %',
            number_format($row['avg_ttc'], 0, ',', ' '),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '004A80']]]];
    }
}
