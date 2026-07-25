<?php

namespace App\Livewire\Admin;

use App\Models\Schedule;
use App\Models\SchoolYear;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class ScheduleManagement extends Component
{
    public array $schedulesData = [];
    public ?string $successMessage = null;

    public function mount(): void
    {
        $schoolYear = SchoolYear::getActive();
        if ($schoolYear) {
            $schedules = Schedule::where('school_year_id', $schoolYear->id)
                ->orderBy('day_of_week')
                ->get();

            foreach ($schedules as $s) {
                $this->schedulesData[$s->day_of_week] = [
                    'id' => $s->id,
                    'day_name' => $this->getDayName($s->day_of_week),
                    'check_in_time' => substr($s->check_in_time, 0, 5),
                    'check_in_tolerance_minutes' => $s->check_in_tolerance_minutes,
                    'check_out_time' => substr($s->check_out_time, 0, 5),
                    'is_school_day' => (bool)$s->is_school_day,
                ];
            }
        }
    }

    private function getDayName(int $day): string
    {
        return match($day) {
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        };
    }

    public function saveSchedules(): void
    {
        foreach ($this->schedulesData as $day => $data) {
            Schedule::where('id', $data['id'])->update([
                'check_in_time' => $data['check_in_time'],
                'check_in_tolerance_minutes' => (int)$data['check_in_tolerance_minutes'],
                'check_out_time' => $data['check_out_time'],
                'is_school_day' => (bool)$data['is_school_day'],
            ]);
        }

        $this->successMessage = 'Pengaturan jam masuk & toleransi berhasil diperbarui.';
    }

    public function render()
    {
        return view('livewire.admin.schedule-management');
    }
}
