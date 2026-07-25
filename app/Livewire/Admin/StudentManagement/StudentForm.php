<?php

namespace App\Livewire\Admin\StudentManagement;

use App\Enums\UserRole;
use App\Models\ClassRoom;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class StudentForm extends Component
{
    public ?int $studentId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $nis = '';
    public int $classRoomId = 0;
    public string $gender = 'L';
    public string $birthDate = '2010-01-01';
    public string $phone = '';

    public function mount(?int $id = null): void
    {
        if ($id) {
            $student = Student::with('user')->find($id);
            if ($student) {
                $this->studentId = $student->id;
                $this->name = $student->user->name;
                $this->email = $student->user->email;
                $this->nis = $student->nis;
                $this->classRoomId = $student->class_room_id;
                $this->gender = $student->gender;
                $this->birthDate = $student->birth_date->format('Y-m-d');
                $this->phone = $student->phone ?? '';
            }
        }
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . ($this->studentId ? Student::find($this->studentId)->user_id : 'NULL'),
            'nis' => 'required|string|max:30|unique:students,nis,' . ($this->studentId ?? 'NULL'),
            'classRoomId' => 'required|exists:class_rooms,id',
            'gender' => 'required|in:L,P',
            'birthDate' => 'required|date',
        ];

        if (!$this->studentId) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        if ($this->studentId) {
            $student = Student::find($this->studentId);
            $student->user->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);
            if (!empty($this->password)) {
                $student->user->update(['password' => Hash::make($this->password)]);
            }

            $student->update([
                'class_room_id' => $this->classRoomId,
                'nis' => $this->nis,
                'gender' => $this->gender,
                'birth_date' => $this->birthDate,
                'phone' => $this->phone,
            ]);
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => UserRole::Student,
                'is_active' => true,
            ]);

            Student::create([
                'user_id' => $user->id,
                'class_room_id' => $this->classRoomId,
                'nis' => $this->nis,
                'gender' => $this->gender,
                'birth_date' => $this->birthDate,
                'phone' => $this->phone,
                'enrolled_at' => now()->toDateString(),
                'is_active' => true,
            ]);
        }

        $this->redirectRoute('admin.students.index', navigate: true);
    }

    public function render()
    {
        $schoolYear = SchoolYear::getActive();
        $classRooms = $schoolYear ? ClassRoom::where('school_year_id', $schoolYear->id)->get() : collect();

        return view('livewire.admin.student-management.student-form', [
            'classRooms' => $classRooms,
        ]);
    }
}
