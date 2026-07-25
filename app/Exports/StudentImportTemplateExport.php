<?php

namespace App\Exports;

use App\Models\ClassRoom;
use App\Models\SchoolYear;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentImportTemplateExport implements FromView, ShouldAutoSize, WithStyles
{
    public function view(): View
    {
        $schoolYear = SchoolYear::getActive();
        $classRooms = $schoolYear ? ClassRoom::where('school_year_id', $schoolYear->id)->pluck('name')->toArray() : ClassRoom::pluck('name')->toArray();

        return view('exports.student-template', [
            'classList' => implode(', ', $classRooms),
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E293B']]],
            2 => ['font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '475569']]],
            4 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}
