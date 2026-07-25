<?php

namespace App\Livewire\Admin;

use App\Models\Holiday;
use App\Models\SchoolYear;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.admin')]
class HolidayCalendar extends Component
{
    #[Validate('required|date')]
    public string $date = '';

    #[Validate('required|string|max:150')]
    public string $name = '';

    #[Validate('required|in:national,school')]
    public string $type = 'school';

    public ?string $successMessage = null;

    public function mount(): void
    {
        $this->date = now()->toDateString();
    }

    public function addHoliday(): void
    {
        $this->validate();

        $schoolYear = SchoolYear::getActive();
        if (!$schoolYear) return;

        Holiday::updateOrCreate(
            ['school_year_id' => $schoolYear->id, 'date' => $this->date],
            ['name' => $this->name, 'type' => $this->type]
        );

        $this->reset(['name']);
        $this->successMessage = 'Hari libur berhasil ditambahkan.';
    }

    public function deleteHoliday(int $id): void
    {
        Holiday::destroy($id);
    }

    public function render()
    {
        $schoolYear = SchoolYear::getActive();
        $holidays = $schoolYear ? Holiday::where('school_year_id', $schoolYear->id)
            ->orderBy('date')
            ->get() : collect();

        return view('livewire.admin.holiday-calendar', [
            'holidays' => $holidays,
        ]);
    }
}
