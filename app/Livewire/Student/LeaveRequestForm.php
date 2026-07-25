<?php

namespace App\Livewire\Student;

use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.student')]
class LeaveRequestForm extends Component
{
    use WithFileUploads;

    public string $activeTab = 'create'; // 'create' or 'history'

    #[Validate('required|date')]
    public string $date = '';

    #[Validate('required|string|in:izin,sakit')]
    public string $type = 'izin';

    #[Validate('required|string|min:5|max:500')]
    public string $reason = '';

    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf|max:2048')]
    public $attachment;

    public ?string $successMessage = null;

    public function mount(): void
    {
        $this->date = now()->toDateString();
    }

    public function submitLeave(): void
    {
        $this->validate();

        $student = Auth::user()->student;
        if (!$student) return;

        $attachmentPath = null;
        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('leave-attachments', 'public');
        }

        $leave = LeaveRequest::create([
            'student_id' => $student->id,
            'type' => LeaveType::from($this->type),
            'date' => $this->date,
            'reason' => $this->reason,
            'attachment_path' => $attachmentPath,
            'status' => LeaveStatus::Pending,
        ]);

        // Send in-app notification to admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => \App\Enums\NotificationType::NewLeaveRequest,
                'title' => 'Pengajuan Izin Baru',
                'body' => $student->user->name . ' mengajukan ' . $leave->type->label() . ' untuk tanggal ' . $leave->date->isoFormat('D MMMM YYYY'),
                'related_type' => LeaveRequest::class,
                'related_id' => $leave->id,
            ]);
        }

        $this->reset(['reason', 'attachment']);
        $this->successMessage = 'Pengajuan ' . LeaveType::from($this->type)->label() . ' berhasil dikirim dan menunggu persetujuan admin.';
        $this->activeTab = 'history';
    }

    public function render()
    {
        $student = Auth::user()->student;
        $history = $student ? LeaveRequest::where('student_id', $student->id)
            ->latest()
            ->paginate(10) : collect();

        return view('livewire.student.leave-request-form', [
            'history' => $history,
        ]);
    }
}
