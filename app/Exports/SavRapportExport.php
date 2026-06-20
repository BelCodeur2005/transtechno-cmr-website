<?php

namespace App\Exports;

use App\Models\SavTicket;
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

class SavRapportExport implements WithMultipleSheets
{
    public function __construct(private readonly int $year) {}

    public function sheets(): array
    {
        return [
            new SavTicketsDetailSheet($this->year),
            new SavParStatutSheet($this->year),
            new SavParTechnicienSheet($this->year),
        ];
    }
}

// ── Feuille 1 : Détail de tous les tickets ───────────────────────────────────

class SavTicketsDetailSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private readonly int $year) {}

    public function title(): string { return 'Tickets SAV'; }

    public function headings(): array
    {
        return [
            'Référence',
            'Client / Structure',
            'Équipement',
            'Statut',
            'Priorité',
            'Technicien',
            'Description',
            'Date ouverture',
            'Date résolution',
            'Délai résolution (h)',
        ];
    }

    public function collection(): Collection
    {
        return SavTicket::with(['client', 'equipments.product', 'technician'])
            ->whereYear('opened_at', $this->year)
            ->orderByDesc('opened_at')
            ->get();
    }

    public function map($row): array
    {
        /** @var SavTicket $row */
        $delai = null;
        if ($row->opened_at && $row->resolved_at) {
            $delai = round($row->opened_at->diffInMinutes($row->resolved_at) / 60, 1);
        }

        return [
            $row->reference,
            $row->client?->structure_name ?? '—',
            $row->equipments->map(fn ($e) => $e->product?->name ?? 'Équipement')->join(', ') ?: '—',
            $row->status,
            $row->priority ?? 'Normal',
            $row->technician?->name ?? 'Non assigné',
            $row->description,
            $row->opened_at?->format('d/m/Y H:i'),
            $row->resolved_at?->format('d/m/Y H:i') ?? '—',
            $delai !== null ? $delai . ' h' : '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E31E24']]]];
    }
}

// ── Feuille 2 : Synthèse par statut ─────────────────────────────────────────

class SavParStatutSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private readonly int $year) {}

    public function title(): string { return 'Par statut'; }

    public function headings(): array
    {
        return ['Statut', 'Nombre de tickets', 'Résolus', 'Taux résolution (%)', 'Délai moyen résolution (h)'];
    }

    public function collection(): Collection
    {
        $tickets = SavTicket::whereYear('opened_at', $this->year)->get();
        $statuses = ['Ouvert', 'En cours', 'Résolu', 'Clôturé'];

        return collect($statuses)->map(function (string $status) use ($tickets) {
            $group = $tickets->where('status', $status);
            $resolved = $group->whereNotNull('resolved_at');
            $count = $group->count();
            $resolvedCount = $resolved->count();

            $avgH = 0;
            if ($resolvedCount > 0) {
                $totalMin = $resolved->sum(function (SavTicket $t) {
                    return $t->opened_at && $t->resolved_at
                        ? $t->opened_at->diffInMinutes($t->resolved_at)
                        : 0;
                });
                $avgH = round($totalMin / $resolvedCount / 60, 1);
            }

            return [
                'status'        => $status,
                'count'         => $count,
                'resolved'      => $resolvedCount,
                'rate'          => $count > 0 ? round($resolvedCount / $count * 100, 1) : 0.0,
                'avg_hours'     => $avgH,
            ];
        })->filter(fn ($r) => $r['count'] > 0)->values();
    }

    public function map($row): array
    {
        return [
            $row['status'],
            $row['count'],
            $row['resolved'],
            $row['rate'] . ' %',
            $row['avg_hours'] > 0 ? $row['avg_hours'] . ' h' : '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E31E24']]]];
    }
}

// ── Feuille 3 : Synthèse par technicien ─────────────────────────────────────

class SavParTechnicienSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private readonly int $year) {}

    public function title(): string { return 'Par technicien'; }

    public function headings(): array
    {
        return ['Technicien', 'Tickets assignés', 'Résolus', 'Taux résolution (%)', 'Délai moyen résolution (h)'];
    }

    public function collection(): Collection
    {
        return SavTicket::with('technician')
            ->whereYear('opened_at', $this->year)
            ->get()
            ->groupBy(fn (SavTicket $t) => $t->technician?->name ?? 'Non assigné')
            ->map(function (Collection $group, string $tech) {
                $resolved = $group->whereNotNull('resolved_at');
                $count = $group->count();
                $resolvedCount = $resolved->count();

                $avgH = 0;
                if ($resolvedCount > 0) {
                    $totalMin = $resolved->sum(function (SavTicket $t) {
                        return $t->opened_at && $t->resolved_at
                            ? $t->opened_at->diffInMinutes($t->resolved_at)
                            : 0;
                    });
                    $avgH = round($totalMin / $resolvedCount / 60, 1);
                }

                return [
                    'tech'      => $tech,
                    'count'     => $count,
                    'resolved'  => $resolvedCount,
                    'rate'      => $count > 0 ? round($resolvedCount / $count * 100, 1) : 0.0,
                    'avg_hours' => $avgH,
                ];
            })
            ->sortByDesc('count')
            ->values();
    }

    public function map($row): array
    {
        return [
            $row['tech'],
            $row['count'],
            $row['resolved'],
            $row['rate'] . ' %',
            $row['avg_hours'] > 0 ? $row['avg_hours'] . ' h' : '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E31E24']]]];
    }
}
