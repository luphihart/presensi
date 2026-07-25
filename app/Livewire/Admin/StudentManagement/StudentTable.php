<?php

namespace App\Livewire\Admin\StudentManagement;

use App\Enums\UserRole;
use App\Models\ClassRoom;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class StudentTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $classFilter = '';

    // Sorting State
    public string $sortColumn = 'name';
    public string $sortDirection = 'asc';

    // Bulk Delete State
    public array $selectedStudents = [];
    public bool $selectAll = false;

    // Modal Form State
    public bool $showFormModal = false;
    public ?int $studentId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $nis = '';
    public string $classRoomId = '';
    public string $gender = 'L';
    public string $birthDate = '';
    public ?string $successMessage = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->reset(['selectedStudents', 'selectAll']);
    }

    public function updatingClassFilter(): void
    {
        $this->resetPage();
        $this->reset(['selectedStudents', 'selectAll']);
    }

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
        $this->reset(['selectedStudents', 'selectAll']);
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedStudents = array_map('strval', $this->getStudentIdsForCurrentPage());
        } else {
            $this->selectedStudents = [];
        }
    }

    protected function getStudentIdsForCurrentPage(): array
    {
        $query = Student::query();

        if (!empty($this->search)) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })->orWhere('nis', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->classFilter)) {
            $query->where('class_room_id', $this->classFilter);
        }

        if ($this->sortColumn === 'name') {
            $query->select('students.*')
                  ->join('users', 'students.user_id', '=', 'users.id')
                  ->orderBy('users.name', $this->sortDirection);
        } elseif ($this->sortColumn === 'nis') {
            $query->orderBy('students.nis', $this->sortDirection);
        } else {
            $query->latest();
        }

        return $query->paginate(15)->pluck('id')->toArray();
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\StudentImportTemplateExport(), 'template-upload-murid.xlsx');
    }

    public function openCreate(): void
    {
        $this->reset(['studentId', 'name', 'email', 'password', 'nis', 'classRoomId', 'gender', 'birthDate']);
        $this->gender = 'L';
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $student = Student::with('user')->find($id);
        if ($student) {
            $this->studentId = $student->id;
            $this->name = $student->user->name;
            $this->email = $student->user->email;
            $this->password = ''; // Keep blank unless changing
            $this->nis = $student->nis;
            $this->classRoomId = (string)$student->class_room_id;
            $this->gender = $student->gender;
            $this->birthDate = $student->birth_date ? $student->birth_date->format('Y-m-d') : '';
            $this->showFormModal = true;
        }
    }

    public function saveStudent(): void
    {
        $student = $this->studentId ? Student::find($this->studentId) : null;
        $userId = $student ? $student->user_id : null;

        $rules = [
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'nis' => ['required', 'string', Rule::unique('students', 'nis')->ignore($this->studentId)],
            'classRoomId' => 'required|exists:class_rooms,id',
            'gender' => 'required|in:L,P',
            'birthDate' => 'required|date',
        ];

        if (!$this->studentId) {
            $rules['password'] = 'required|string|min:6';
        } else {
            $rules['password'] = 'nullable|string|min:6';
        }

        $this->validate($rules);

        if ($this->studentId && $student) {
            // Update User
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
            ];
            if (!empty($this->password)) {
                $userData['password'] = bcrypt($this->password);
            }
            $student->user->update($userData);

            // Update Student
            $student->update([
                'class_room_id' => $this->classRoomId,
                'nis' => $this->nis,
                'gender' => $this->gender,
                'birth_date' => $this->birthDate,
            ]);

            $this->successMessage = 'Data murid berhasil diperbarui.';
        } else {
            // Create User
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'role' => UserRole::Student,
                'is_active' => true,
            ]);

            // Create Student
            Student::create([
                'user_id' => $user->id,
                'class_room_id' => $this->classRoomId,
                'nis' => $this->nis,
                'gender' => $this->gender,
                'birth_date' => $this->birthDate,
                'enrolled_at' => now()->toDateString(),
                'is_active' => true,
            ]);

            $this->successMessage = 'Murid baru berhasil ditambahkan.';
        }

        $this->showFormModal = false;
    }

    public function deleteStudent(int $studentId): void
    {
        $student = Student::withTrashed()->find($studentId);
        if ($student) {
            if ($student->user) {
                $student->user->forceDelete();
            }
            $student->forceDelete();
            $this->successMessage = 'Data murid berhasil dihapus.';
        }
    }

    public function deleteSelectedStudents(): void
    {
        if (count($this->selectedStudents) > 0) {
            $count = count($this->selectedStudents);
            $students = Student::withTrashed()->whereIn('id', $this->selectedStudents)->get();

            DB::transaction(function () use ($students) {
                foreach ($students as $student) {
                    if ($student->user) {
                        $student->user->forceDelete();
                    }
                    $student->forceDelete();
                }
            });

            $this->reset(['selectedStudents', 'selectAll']);
            $this->successMessage = "Berhasil menghapus {$count} data murid terpilih.";
        }
    }

    public function render()
    {
        $schoolYear = SchoolYear::getActive();
        $classRooms = $schoolYear ? ClassRoom::where('school_year_id', $schoolYear->id)->get() : ClassRoom::all();

        $query = Student::with(['user', 'classRoom']);

        if (!empty($this->search)) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })->orWhere('nis', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->classFilter)) {
            $query->where('class_room_id', $this->classFilter);
        }

        if ($this->sortColumn === 'name') {
            $query->select('students.*')
                  ->join('users', 'students.user_id', '=', 'users.id')
                  ->orderBy('users.name', $this->sortDirection);
        } elseif ($this->sortColumn === 'nis') {
            $query->orderBy('students.nis', $this->sortDirection);
        } else {
            $query->latest();
        }

        $students = $query->paginate(15);

        return view('livewire.admin.student-management.student-table', [
            'students' => $students,
            'classRooms' => $classRooms,
        ]);
    }
}
