<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class SchoolSettings extends Component
{
    public string $schoolName = '';
    public string $schoolAddress = '';
    public string $schoolPhone = '';
    public ?string $successMessage = null;

    public function mount(): void
    {
        $this->schoolName = Setting::get('school_name', 'SMA Negeri 1 Nusantara');
        $this->schoolAddress = Setting::get('school_address', 'Jl. Pendidikan No. 1');
        $this->schoolPhone = Setting::get('school_phone', '021-12345678');
    }

    public function saveSettings(): void
    {
        $this->validate([
            'schoolName' => 'required|string|max:150',
            'schoolAddress' => 'nullable|string|max:255',
            'schoolPhone' => 'nullable|string|max:50',
        ]);

        Setting::set('school_name', $this->schoolName, 'Nama resmi sekolah');
        Setting::set('school_address', $this->schoolAddress, 'Alamat lengkap sekolah');
        Setting::set('school_phone', $this->schoolPhone, 'Nomor telepon sekolah');

        $this->successMessage = 'Pengaturan sekolah berhasil disimpan.';
    }

    public function render()
    {
        return view('livewire.admin.school-settings');
    }
}
