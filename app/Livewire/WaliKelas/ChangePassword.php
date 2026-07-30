<?php

namespace App\Livewire\WaliKelas;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.wali-kelas')]
class ChangePassword extends Component
{
    #[Validate('required|current_password')]
    public string $current_password = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    public ?string $successMessage = null;

    public function changePassword(): void
    {
        $this->validate();

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->successMessage = 'Password berhasil diperbarui.';
    }

    public function render()
    {
        return view('livewire.wali-kelas.change-password');
    }
}
