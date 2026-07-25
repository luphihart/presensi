<?php

namespace App\Livewire\Admin\StudentManagement;

use App\Services\StudentImportService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.admin')]
class StudentImport extends Component
{
    use WithFileUploads;

    public $file;
    public ?string $successMessage = null;
    public array $importErrors = [];

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\StudentImportTemplateExport(), 'template-upload-murid.xlsx');
    }

    public function import(StudentImportService $importService): void
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $data = Excel::toArray([], $this->file->getRealPath());
            $rows = $data[0] ?? [];

            $result = $importService->import($rows);

            if ($result['success']) {
                $this->successMessage = 'Berhasil mengimpor ' . $result['imported_count'] . ' murid baru.';
                $this->importErrors = $result['errors'];
            } else {
                $this->importErrors = [$result['message']];
            }
        } catch (\Throwable $e) {
            $this->importErrors = ['Format file tidak valid atau gagal dibaca: ' . $e->getMessage()];
        }
    }

    public function render()
    {
        return view('livewire.admin.student-management.student-import');
    }
}
