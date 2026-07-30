<?php

namespace App\Livewire\Admin;

use App\Models\ClassRoom;
use App\Models\SchoolYear;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.admin')]
class ClassRoomManagement extends Component
{
    public bool $showFormModal = false;
    public ?int $classRoomId = null;

    #[Validate('required|string|max:50')]
    public string $name = '';

    #[Validate('nullable|string|max:50')]
    public string $major = '';

    #[Validate('required|exists:school_years,id')]
    public ?int $schoolYearId = null;

    #[Validate('nullable|exists:users,id')]
    public ?int $waliKelasId = null;

    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    public function mount(): void
    {
        $activeYear = SchoolYear::getActive();
        $this->schoolYearId = $activeYear?->id ?? SchoolYear::first()?->id;
    }

    public function openCreate(): void
    {
        $this->reset(['classRoomId', 'name', 'major', 'waliKelasId', 'errorMessage']);
        $activeYear = SchoolYear::getActive();
        $this->schoolYearId = $activeYear?->id ?? SchoolYear::first()?->id;
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->reset(['errorMessage']);
        $class = ClassRoom::find($id);
        if ($class) {
            $this->classRoomId = $class->id;
            $this->name = $class->name;
            $this->major = $class->major ?? '';
            $this->schoolYearId = $class->school_year_id;
            $this->waliKelasId = $class->wali_kelas_id;
            $this->showFormModal = true;
        }
    }

    public function save(): void
    {
        $this->validate();

        // Check if wali_kelas_id is already assigned to another class
        if ($this->waliKelasId) {
            $alreadyAssigned = ClassRoom::where('wali_kelas_id', $this->waliKelasId)
                ->where('id', '!=', $this->classRoomId)
                ->first();
            if ($alreadyAssigned) {
                $this->errorMessage = "Wali Kelas ini sudah di-assign ke kelas {$alreadyAssigned->name}. Satu wali kelas hanya dapat mengampu 1 kelas.";
                return;
            }
        }

        ClassRoom::updateOrCreate(
            ['id' => $this->classRoomId],
            [
                'school_year_id' => $this->schoolYearId,
                'name' => $this->name,
                'major' => $this->major,
                'wali_kelas_id' => $this->waliKelasId ?: null,
            ]
        );

        $this->showFormModal = false;
        $this->successMessage = 'Data kelas & jurusan berhasil disimpan.';
    }

    public function delete(int $id): void
    {
        $class = ClassRoom::withCount('students')->find($id);
        if ($class) {
            if ($class->students_count > 0) {
                $this->errorMessage = "Kelas {$class->name} tidak dapat dihapus karena masih memiliki {$class->students_count} murid.";
                return;
            }
            $class->delete();
            $this->successMessage = 'Kelas berhasil dihapus.';
        }
    }

    public function render()
    {
        $schoolYears = SchoolYear::all();
        $classRooms = ClassRoom::with(['schoolYear', 'waliKelas'])->withCount('students')->latest()->get();
        $waliKelasList = \App\Models\User::where('role', \App\Enums\UserRole::WaliKelas)->orderBy('name')->get();

        // Unique majors for summary
        $majors = ClassRoom::whereNotNull('major')->where('major', '!=', '')->pluck('major')->unique();

        return view('livewire.admin.class-room-management', [
            'classRooms' => $classRooms,
            'schoolYears' => $schoolYears,
            'majors' => $majors,
            'waliKelasList' => $waliKelasList,
        ]);
    }
}
