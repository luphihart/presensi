<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.admin')]
class WaliKelasManagement extends Component
{
    public bool $showFormModal = false;
    public ?int $waliKelasId = null;

    #[Validate('required|string|max:150')]
    public string $name = '';

    #[Validate('required|string|max:50')]
    public string $nip = '';

    public ?int $classRoomId = null;

    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    public function openCreate(): void
    {
        $this->reset(['waliKelasId', 'name', 'nip', 'classRoomId', 'errorMessage']);
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->reset(['errorMessage']);
        $user = User::where('role', UserRole::WaliKelas)->find($id);
        if ($user) {
            $this->waliKelasId = $user->id;
            $this->name = $user->name;
            $this->nip = $user->nip ?? '';
            $this->classRoomId = $user->homeroomClass?->id;
            $this->showFormModal = true;
        }
    }

    public function save(): void
    {
        $this->nip = trim($this->nip);
        $email = $this->nip . '@walikelas.com';

        // Validation rules
        $rules = [
            'name' => 'required|string|max:150',
            'nip' => 'required|string|max:50|unique:users,nip,' . ($this->waliKelasId ?? 'NULL'),
        ];
        $this->validate($rules);

        if ($this->classRoomId) {
            $existingAssigned = ClassRoom::where('wali_kelas_id', '!=', $this->waliKelasId)
                ->where('id', $this->classRoomId)
                ->first();
            if ($existingAssigned && $existingAssigned->wali_kelas_id) {
                $this->errorMessage = "Kelas {$existingAssigned->name} sudah diampu oleh wali kelas lain.";
                return;
            }
        }

        if ($this->waliKelasId) {
            $user = User::find($this->waliKelasId);
            $user->update([
                'name' => $this->name,
                'nip' => $this->nip,
                'email' => $email,
            ]);
            $msg = "Data Wali Kelas {$user->name} berhasil diperbarui.";
        } else {
            $user = User::create([
                'name' => $this->name,
                'nip' => $this->nip,
                'email' => $email,
                'password' => Hash::make('walikelas123'),
                'role' => UserRole::WaliKelas,
                'is_active' => true,
            ]);
            $msg = "Wali Kelas baru berhasil ditambahkan! Username: {$email}, Password: walikelas123";
        }

        // Unassign old class if any
        ClassRoom::where('wali_kelas_id', $user->id)->update(['wali_kelas_id' => null]);

        // Assign new class if selected
        if ($this->classRoomId) {
            $class = ClassRoom::find($this->classRoomId);
            if ($class) {
                $class->update(['wali_kelas_id' => $user->id]);
            }
        }

        $this->showFormModal = false;
        $this->successMessage = $msg;
    }

    public function toggleStatus(int $id): void
    {
        $user = User::where('role', UserRole::WaliKelas)->find($id);
        if ($user) {
            $user->is_active = !$user->is_active;
            $user->save();
            $this->successMessage = "Status akun Wali Kelas {$user->name} berhasil diubah.";
        }
    }

    public function resetPassword(int $id): void
    {
        $user = User::where('role', UserRole::WaliKelas)->find($id);
        if ($user) {
            $user->password = Hash::make('walikelas123');
            $user->save();
            $this->successMessage = "Password Wali Kelas {$user->name} berhasil di-reset ke walikelas123.";
        }
    }

    public function delete(int $id): void
    {
        $user = User::where('role', UserRole::WaliKelas)->find($id);
        if ($user) {
            ClassRoom::where('wali_kelas_id', $user->id)->update(['wali_kelas_id' => null]);
            $user->delete();
            $this->successMessage = "Wali Kelas berhasil dihapus.";
        }
    }

    public function render()
    {
        $waliKelasList = User::where('role', UserRole::WaliKelas)
            ->with(['homeroomClass'])
            ->latest()
            ->get();

        $availableClasses = ClassRoom::orderBy('name')->get();

        return view('livewire.admin.wali-kelas-management', [
            'waliKelasList' => $waliKelasList,
            'availableClasses' => $availableClasses,
        ]);
    }
}
