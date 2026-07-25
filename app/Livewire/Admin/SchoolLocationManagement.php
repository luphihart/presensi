<?php

namespace App\Livewire\Admin;

use App\Models\SchoolLocation;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.admin')]
class SchoolLocationManagement extends Component
{
    public bool $showForm = false;
    public ?int $locationId = null;

    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('required|numeric|between:-90,90')]
    public string $latitude = '';

    #[Validate('required|numeric|between:-180,180')]
    public string $longitude = '';

    #[Validate('required|integer|min:10|max:5000')]
    public int $radiusMeters = 100;

    public bool $isActive = true;

    public ?string $successMessage = null;

    public function openCreate(): void
    {
        $this->reset(['locationId', 'name', 'latitude', 'longitude', 'radiusMeters', 'isActive']);
        $this->radiusMeters = 100;
        $this->isActive = true;
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $loc = SchoolLocation::find($id);
        if ($loc) {
            $this->locationId = $loc->id;
            $this->name = $loc->name;
            $this->latitude = (string)$loc->latitude;
            $this->longitude = (string)$loc->longitude;
            $this->radiusMeters = $loc->radius_meters;
            $this->isActive = (bool)$loc->is_active;
            $this->showForm = true;
        }
    }

    public function save(): void
    {
        $this->validate();

        SchoolLocation::updateOrCreate(
            ['id' => $this->locationId],
            [
                'name' => $this->name,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'radius_meters' => $this->radiusMeters,
                'is_active' => $this->isActive,
            ]
        );

        Setting::set('default_radius_meters', (string)$this->radiusMeters, 'Radius geofence default dalam meter');

        $this->showForm = false;
        $this->successMessage = 'Titik lokasi sekolah / geofence berhasil disimpan.';
    }

    public function delete(int $id): void
    {
        SchoolLocation::destroy($id);
        $this->successMessage = 'Titik lokasi berhasil dihapus.';
    }

    public function toggleActive(int $id): void
    {
        $loc = SchoolLocation::find($id);
        if ($loc) {
            $loc->update(['is_active' => !$loc->is_active]);
        }
    }

    public function render()
    {
        $locations = SchoolLocation::latest()->get();

        return view('livewire.admin.school-location-management', [
            'locations' => $locations,
        ]);
    }
}
