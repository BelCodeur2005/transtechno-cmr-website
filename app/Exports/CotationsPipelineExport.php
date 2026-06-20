<?php

namespace App\Exports;

use App\Models\Quotation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CotationsPipelineExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle
{
    public function __construct(
        private readonly ?string $status = null,
        private readonly ?string $city = null,
        private readonly ?int $year = null,
    ) {}

    public function title(): string
    {
        return 'Pipeline Cotations';
    }

    public function collection(): Collection
    {
        return Quotation::with(['items', 'assignedUser'])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->city, fn ($q) => $q->where('city', $this->city))
            ->when($this->year, fn ($q) => $q->whereYear('created_at', $this->year))
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Statut',
            'Priorité',
            'Structure / Client',
            'Ville',
            'Secteur',
            'Contact',
            'Email',
            'Téléphone',
            'Nb articles',
            'Sous-total HT (FCFA)',
            'Remise (%)',
            'Transport (FCFA)',
            'TVA Exonérée',
            'Total TTC (FCFA)',
            'Assigné à',
            'Date demande',
            'Dernière modif.',
        ];
    }

    public function map($row): array
    {
        /** @var Quotation $row */
        return [
            $row->reference,
            $row->status,
            $row->priority ?? 'Normal',
            $row->structure_name,
            $row->city,
            $row->sector,
            $row->responsible_name,
            $row->email,
            $row->phone,
            $row->items->count(),
            number_format($row->subtotalHt(), 0, ',', ' '),
            $row->discount_percent . ' %',
            number_format((int) $row->transport_cost, 0, ',', ' '),
            $row->vat_exempt ? 'Oui' : 'Non',
            number_format($row->totalTtc(), 0, ',', ' '),
            $row->assignedUser?->name ?? '—',
            $row->created_at?->format('d/m/Y H:i'),
            $row->updated_at?->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '004A80']],
            ],
        ];
    }
}
