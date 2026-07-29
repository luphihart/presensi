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

    // Modal Form & Reset State
    public bool $showFormModal = false;
    public bool $showResetResultModal = false;
    public array $resetResults = [];
    public ?int $studentId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $nis = '';
    public string $classRoomId = '';
    public string $gender = 'L';
    public string $birthDate = '';
    public string $phone = '';
    public string $address = '';
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
        $this->reset(['studentId', 'name', 'email', 'password', 'nis', 'classRoomId', 'gender', 'birthDate', 'phone', 'address']);
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
            $this->phone = $student->phone ?? '';
            $this->address = $student->address ?? '';
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
            $students = Student::withTrashed()->with('user')->whereIn('id', $this->selectedStudents)->get();

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

    public function resetPassword(int $studentId): void
    {
        $student = Student::with('user')->find($studentId);
        if ($student && $student->user) {
            $newPassword = \Illuminate\Support\Str::random(8);
            $student->user->update([
                'password' => bcrypt($newPassword),
            ]);

            $this->resetResults = [
                [
                    'name' => $student->user->name,
                    'nis' => $student->nis,
                    'email' => $student->user->email,
                    'password' => $newPassword,
                ]
            ];
            $this->showResetResultModal = true;
            $this->successMessage = "Password murid {$student->user->name} berhasil direset.";
        }
    }

    public function bulkResetPassword(): void
    {
        if (count($this->selectedStudents) > 0) {
            $students = Student::with('user')->whereIn('id', $this->selectedStudents)->get();
            $results = [];

            DB::transaction(function () use ($students, &$results) {
                foreach ($students as $student) {
                    if ($student->user) {
                        $newPassword = \Illuminate\Support\Str::random(8);
                        $student->user->update([
                            'password' => bcrypt($newPassword),
                        ]);

                        $results[] = [
                            'name' => $student->user->name,
                            'nis' => $student->nis,
                            'email' => $student->user->email,
                            'password' => $newPassword,
                        ];
                    }
                }
            });

            $this->resetResults = $results;
            $this->showResetResultModal = true;
            $count = count($results);
            $this->reset(['selectedStudents', 'selectAll']);
            $this->successMessage = "Berhasil mereset password {$count} murid terpilih.";
        }
    }

    public function recalculateStreak(int $id): void
    {
        $student = Student::find($id);
        if ($student) {
            app(\App\Services\AttendanceStreakService::class)->recalculateStreak($student);
            $this->successMessage = "Hitung ulang streak untuk {$student->user->name} selesai (Streak saat ini: {$student->current_streak} hari).";
        }
    }

    public function recalculateAllStreaks(): void
    {
        $students = Student::where('is_active', true)->get();
        $streakService = app(\App\Services\AttendanceStreakService::class);
        $schoolYear = SchoolYear::getActive();

        $holidays = [];
        $schedules = [];
        if ($schoolYear) {
            $holidays = \App\Models\Holiday::where('school_year_id', $schoolYear->id)
                ->pluck('date')
                ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
                ->toArray();

            $schedules = \App\Models\Schedule::where('school_year_id', $schoolYear->id)
                ->pluck('is_school_day', 'day_of_week')
                ->toArray();
        }

        foreach ($students as $student) {
            $streakService->recalculateStreak($student, $holidays, $schedules);
        }
        $this->successMessage = "Berhasil menghitung ulang streak untuk seluruh " . count($students) . " murid aktif.";
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
        } elseif ($this->sortColumn === 'streak') {
            $query->orderBy('students.current_streak', $this->sortDirection);
        } else {
            $query->latest();
        }

        $students = $query->paginate(15);

        // Leaderboard Top 5 Streaks (per kelas jika ada filter kelas, atau global)
        $leaderboardQuery = Student::with(['user', 'classRoom'])
            ->where('is_active', true)
            ->where('current_streak', '>', 0);

        if (!empty($this->classFilter)) {
            $leaderboardQuery->where('class_room_id', $this->classFilter);
        }

        $leaderboard = $leaderboardQuery->orderByDesc('current_streak')
            ->orderByDesc('longest_streak')
            ->take(5)
            ->get();

        return view('livewire.admin.student-management.student-table', [
            'students' => $students,
            'classRooms' => $classRooms,
            'leaderboard' => $leaderboard,
        ]);
    }
}
