<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\LeaveRequest;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceStreakService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceStreakTest extends TestCase
{
    use RefreshDatabase;

    private SchoolYear $schoolYear;
    private Student $student;
    private AttendanceStreakService $streakService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolYear = SchoolYear::create([
            'name' => '2025/2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        // Monday to Friday active
        for ($i = 1; $i <= 5; $i++) {
            Schedule::create([
                'school_year_id' => $this->schoolYear->id,
                'day_of_week' => $i,
                'is_school_day' => true,
                'check_in_time' => '07:00:00',
                'check_out_time' => '15:00:00',
            ]);
        }

        $user = User::factory()->create(['name' => 'Budi Pertiwi']);
        $classRoom = ClassRoom::create(['school_year_id' => $this->schoolYear->id, 'name' => 'X-A']);
        $this->student = Student::create([
            'user_id' => $user->id,
            'class_room_id' => $classRoom->id,
            'nis' => '10001',
            'gender' => 'L',
            'birth_date' => '2009-05-15',
            'enrolled_at' => '2026-01-01',
            'is_active' => true,
        ]);

        $this->streakService = new AttendanceStreakService();
    }

    public function test_streak_increments_on_consecutive_attendance(): void
    {
        Carbon::setTestNow('2026-02-06'); // Friday

        // Create 5 consecutive present days (Mon - Fri: Feb 2 to Feb 6)
        for ($day = 2; $day <= 6; $day++) {
            Attendance::create([
                'student_id' => $this->student->id,
                'school_year_id' => $this->schoolYear->id,
                'date' => "2026-02-0{$day}",
                'status' => AttendanceStatus::Hadir,
            ]);
        }

        $result = $this->streakService->recalculateStreak($this->student);

        $this->assertEquals(5, $result['current']);
        $this->assertEquals(5, $this->student->fresh()->current_streak);
        $this->assertNotNull($this->student->getBadge());
        $this->assertEquals('On Fire', $this->student->getBadge()['name']);

        Carbon::setTestNow();
    }

    public function test_approved_leave_preserves_streak(): void
    {
        Carbon::setTestNow('2026-02-06'); // Friday

        // Mon, Tue present
        Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => '2026-02-02',
            'status' => AttendanceStatus::Hadir,
        ]);
        Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => '2026-02-03',
            'status' => AttendanceStatus::Hadir,
        ]);

        // Wed approved leave
        LeaveRequest::create([
            'student_id' => $this->student->id,
            'type' => LeaveType::Sakit,
            'date' => '2026-02-04',
            'reason' => 'Demam tinggi',
            'status' => LeaveStatus::Approved,
        ]);

        // Thu, Fri present
        Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => '2026-02-05',
            'status' => AttendanceStatus::Hadir,
        ]);
        Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => '2026-02-06',
            'status' => AttendanceStatus::Hadir,
        ]);

        $result = $this->streakService->recalculateStreak($this->student);

        // All 5 days counted (approved leave preserves streak)
        $this->assertEquals(5, $result['current']);

        Carbon::setTestNow();
    }

    public function test_retroactive_leave_approval_recalculates_streak(): void
    {
        Carbon::setTestNow('2026-02-06'); // Friday

        // Mon, Tue present
        Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => '2026-02-02',
            'status' => AttendanceStatus::Hadir,
        ]);
        Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => '2026-02-03',
            'status' => AttendanceStatus::Hadir,
        ]);

        // Wed Alpa (no leave yet)
        Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => '2026-02-04',
            'status' => AttendanceStatus::Alpa,
        ]);

        // Thu, Fri present
        Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => '2026-02-05',
            'status' => AttendanceStatus::Hadir,
        ]);
        Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => '2026-02-06',
            'status' => AttendanceStatus::Hadir,
        ]);

        // Before approval, streak was broken on Wed so current streak is 2 (Thu + Fri)
        $this->streakService->recalculateStreak($this->student);
        $this->assertEquals(2, $this->student->fresh()->current_streak);

        // NOW: Admin retroactively approves leave for Wed
        $leave = LeaveRequest::create([
            'student_id' => $this->student->id,
            'type' => LeaveType::Izin,
            'date' => '2026-02-04',
            'reason' => 'Acara keluarga',
            'status' => LeaveStatus::Pending,
        ]);

        // Simulate admin clicking approve
        $leave->update(['status' => LeaveStatus::Approved]);
        $this->streakService->recalculateStreak($this->student);

        // After retroactive approval, streak becomes 5!
        $this->assertEquals(5, $this->student->fresh()->current_streak);

        Carbon::setTestNow();
    }
}
