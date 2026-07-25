<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class ReportExportService
{
    /**
     * Generate Landscape PDF view for monthly matrix attendance report.
     */
    public function generateMatrixPdf(array $reportData, string $title = 'Laporan Presensi Bulanan'): \Barryvdh\DomPDF\PDF
    {
        $data = array_merge($reportData, [
            'title' => $title,
            'generated_at' => now()->format('d/m/Y H:i'),
        ]);

        return Pdf::loadView('reports.attendance-pdf', $data)
            ->setPaper('a4', 'landscape');
    }
}
