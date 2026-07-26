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
    public ?int $editingId = null;

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

    public function editHoliday(int $id): void
    {
        $holiday = Holiday::find($id);
        if (!$holiday) return;

        $this->editingId = $holiday->id;
        $this->date = $holiday->date->toDateString();
        $this->name = $holiday->name;
        $this->type = $holiday->type;
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->date = now()->toDateString();
        $this->name = '';
        $this->type = 'school';
        $this->resetValidation();
    }

    public function addHoliday(): void
    {
        $this->validate();

        $schoolYear = SchoolYear::getActive();
        if (!$schoolYear) return;

        if ($this->editingId) {
            $holiday = Holiday::find($this->editingId);
            if ($holiday) {
                $holiday->update([
                    'date' => $this->date,
                    'name' => $this->name,
                    'type' => $this->type,
                ]);
                $this->successMessage = 'Hari libur berhasil diperbarui.';
            }
            $this->cancelEdit();
            return;
        }

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
        if ($this->editingId === $id) {
            $this->cancelEdit();
        }
        $this->successMessage = 'Hari libur berhasil dihapus.';
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
