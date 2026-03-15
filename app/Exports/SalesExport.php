<?php
namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    private int $rowIndex = 0;

    public function __construct(private string $period = 'monthly') {}

    public function collection()
    {
        $q = Sale::with(['department', 'soldBy'])->where('status', 'completed');
        if ($this->period === 'daily')
            $q->whereDate('created_at', today());
        elseif ($this->period === 'monthly')
            $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        return $q->latest()->get();
    }

    public function headings(): array
    {
        return ['#', 'Receipt No.', 'Patient Name', 'Department', 'Dispensed By', 'Amount (NGN)', 'Profit (NGN)', 'Date & Time'];
    }

    public function map($sale): array
    {
        $this->rowIndex++;
        return [
            $this->rowIndex,
            $sale->receipt_number,
            $sale->patient_name,
            $sale->department->name,
            $sale->soldBy->name,
            number_format($sale->total_amount, 2),
            number_format($sale->total_profit, 2),
            $sale->created_at->format('d M Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $last = $sheet->getHighestRow();

        // Header style
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Alternating rows
        for ($r = 2; $r <= $last; $r++) {
            $sheet->getStyle("A{$r}:H{$r}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $r % 2 === 0 ? 'FFF0F4F8' : 'FFFFFFFF']],
            ]);
        }

        // Borders
        $sheet->getStyle("A1:H{$last}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ]);

        return [];
    }

    public function title(): string { return 'Sales Report'; }
}