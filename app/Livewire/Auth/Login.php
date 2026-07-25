<?php

namespace App\Livewire\Auth;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;
    public bool $showPassword = false;
    public ?string $errorMessage = null;

    public function togglePassword(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function login(): void
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'is_active' => true], $this->remember)) {
            session()->regenerate();
            $user = Auth::user();
            $user->update(['last_login_at' => now()]);

            if ($user->role === UserRole::Admin) {
                $this->redirectRoute('admin.dashboard');
            } else {
                $this->redirectRoute('student.dashboard');
            }
            return;
        }

        $this->errorMessage = 'Email atau password salah, atau akun tidak aktif.';
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
