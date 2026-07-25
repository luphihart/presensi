<?php

namespace App\Livewire\Student;

use App\Jobs\ProcessProfilePhoto;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.student')]
class ProfileEdit extends Component
{
    use WithFileUploads;

    // Name is read-only — managed by Admin only
    public string $name = '';


    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|string|max:500')]
    public string $address = '';

    public string $themePreference = 'system';

    #[Validate('nullable|image|max:2048')]
    public $photo;

    public ?string $successMessage = null;

    public function mount(): void
    {
        $user = Auth::user();
        $student = $user->student;

        $this->name = $user->name;
        $this->themePreference = $user->theme_preference ?? 'system';
        if ($student) {
            $this->phone = $student->phone ?? '';
            $this->address = $student->address ?? '';
        }
    }

    public function updateProfile(): void
    {
        $this->validate();

        $user = Auth::user();
        $student = $user->student;

        $user->update([
            'theme_preference' => $this->themePreference,
        ]);

        if ($student) {
            $student->update([
                'phone' => $this->phone,
                'address' => $this->address,
            ]);

            if ($this->photo) {
                $path = $this->photo->store('profile-photos', 'public');
                $student->update(['profile_photo_path' => $path]);
                $this->reset('photo');
            }
        }

        $this->dispatch('theme-changed', theme: $this->themePreference);
        $this->successMessage = 'Profil berhasil diperbarui.';
    }

    public function render()
    {
        return view('livewire.student.profile-edit', [
            'user' => Auth::user(),
            'student' => Auth::user()->student,
        ]);
    }
}
