<?php

namespace App\Livewire\WaliKelas;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.wali-kelas')]
class Report extends Component
{
    public string $month = '';

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function exportExcel()
    {
        $user = auth()->user();
        $classRoom = ClassRoom::where('wali_kelas_id', $user->id)->first();
        if (!$classRoom) return;

        $reportData = $this->getReportMatrix();
        $title = 'Rekap Presensi Murid - Kelas ' . $classRoom->name . ' (' . Carbon::parse($this->month . '-01')->locale('id')->isoFormat('MMMM YYYY') . ')';

        $fileName = 'rekap-presensi-kelas-' . str_replace(' ', '-', strtolower($classRoom->name)) . '-' . $this->month . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AttendanceMatrixExport($reportData, $title), $fileName);
    }

    public function getReportMatrix(): array
    {
        $user = auth()->user();
        $classRoom = ClassRoom::where('wali_kelas_id', $user->id)->first();

        if (!$classRoom) {
            return [
                'days' => [],
                'matrix' => [],
                'yearMonth' => $this->month,
                'holidays' => [],
            ];
        }

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

        // Fetch students only in this classroom
        $students = Student::where('class_room_id', $classRoom->id)->with(['user', 'classRoom'])->get();

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
        $user = auth()->user();
        $classRoom = ClassRoom::where('wali_kelas_id', $user->id)->first();
        $reportData = $this->getReportMatrix();

        return view('livewire.wali-kelas.report', [
            'classRoom' => $classRoom,
            'reportData' => $reportData,
        ]);
    }
}
