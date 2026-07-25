<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceMatrixExport implements FromView, ShouldAutoSize, WithStyles
{
    public function __construct(
        public array $reportData,
        public string $title
    ) {}

    public function view(): View
    {
        return view('reports.attendance-excel', array_merge($this->reportData, [
            'title' => $this->title,
            'generated_at' => now()->format('d/m/Y H:i'),
        ]));
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
        ];
    }
}
