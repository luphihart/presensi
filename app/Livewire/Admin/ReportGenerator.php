<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Services\ReportExportService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class ReportGenerator extends Component
{
    public string $month = '';
    public string $classFilter = '';

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function exportExcel()
    {
        $reportData = $this->getReportMatrix();
        $selectedClass = $this->classFilter ? ClassRoom::find($this->classFilter)?->name : 'Semua Kelas';
        $title = 'Rekap Presensi Murid - ' . $selectedClass . ' (' . Carbon::parse($this->month . '-01')->locale('id')->isoFormat('MMMM YYYY') . ')';

        $fileName = 'rekap-presensi-' . str_replace(' ', '-', strtolower($selectedClass)) . '-' . $this->month . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AttendanceMatrixExport($reportData, $title), $fileName);
    }

    public function getReportMatrix(): array
    {
        $yearMonth = !empty($this->month) ? $this->month : now()->format('Y-m');
        $startOfMonth = Carbon::parse($yearMonth . '-01')->locale('id')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Build array of dates for the month
        $days = [];
        $current = $startOfMonth->copy();
        while ($current->lte($endOfMonth)) {
            $days[$current->toDateString()] = [
                'date' => $current->toDateString(),
                'dayNumber' => $current->format('d'),
                'dayName' => $current->locale('id')->isoFormat('ddd'),
                'isWeekend' => $current->isWeekend(),
            ];
            $current->addDay();
        }

        // Fetch students
        $studentQuery = Student::with(['user', 'classRoom']);
        if (!empty($this->classFilter)) {
            $studentQuery->where('class_room_id', $this->classFilter);
        }
        $students = $studentQuery->get();

        // Fetch attendances for date range
        $studentIds = $students->pluck('id');
        $attendances = Attendance::whereIn('student_id', $studentIds)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get()
            ->groupBy('student_id');

        // Fetch holidays for the month
        $holidays = \App\Models\Holiday::whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->pluck('name', 'date')
            ->toArray();

        $matrix = [];
        foreach ($students as $student) {
            $studentAttendances = $attendances->get($student->id, collect())->keyBy(function ($item) {
                return Carbon::parse($item->date)->toDateString();
            });

            $hadirCount = 0;
            $terlambatCount = 0;
            $izinCount = 0;
            $sakitCount = 0;
            $alpaCount = 0;
            $dateRecords = [];

            foreach ($days as $dateStr => $dayInfo) {
                $att = $studentAttendances->get($dateStr);
                if ($att) {
                    $statusVal = strtolower($att->status->value);
                    if ($statusVal === 'hadir') $hadirCount++;
                    elseif ($statusVal === 'terlambat') $terlambatCount++;
                    elseif ($statusVal === 'izin') $izinCount++;
                    elseif ($statusVal === 'sakit') $sakitCount++;
                    elseif ($statusVal === 'alpa') $alpaCount++;

                    $dateRecords[$dateStr] = [
                        'status' => $att->status->value,
                        'label' => match($statusVal) {
                            'hadir' => 'H',
                            'terlambat' => 'T',
                            'izin' => 'I',
                            'sakit' => 'S',
                            'alpa' => 'A',
                            default => '-'
                        },
                        'time' => $att->check_in_at ? $att->check_in_at->format('H:i') : null,
                    ];
                } else {
                    $dateRecords[$dateStr] = [
                        'status' => null,
                        'label' => $dayInfo['isWeekend'] || isset($holidays[$dateStr]) ? '-' : '',
                        'time' => null,
                    ];
                }
            }

            $totalAttended = $hadirCount + $terlambatCount;
            $totalSchoolDays = count(array_filter($days, fn($d) => !$d['isWeekend'] && !isset($holidays[$d['date']])));
            $percentage = $totalSchoolDays > 0 ? round(($totalAttended / $totalSchoolDays) * 100) : 100;

            $matrix[] = [
                'student' => $student,
                'summary' => [
                    'hadir' => $hadirCount,
                    'terlambat' => $terlambatCount,
                    'izin' => $izinCount,
                    'sakit' => $sakitCount,
                    'alpa' => $alpaCount,
                    'percentage' => $percentage,
                ],
                'dateRecords' => $dateRecords,
            ];
        }

        return [
            'days' => $days,
            'matrix' => $matrix,
            'yearMonth' => $yearMonth,
            'holidays' => $holidays,
        ];
    }

    public function render()
    {
        $schoolYear = SchoolYear::getActive();
        $classRooms = $schoolYear ? ClassRoom::where('school_year_id', $schoolYear->id)->get() : ClassRoom::all();
        $reportData = $this->getReportMatrix();

        return view('livewire.admin.report-generator', [
            'classRooms' => $classRooms,
            'reportData' => $reportData,
        ]);
    }
}
