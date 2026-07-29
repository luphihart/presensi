<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\DisciplinePoint;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\Services\DisciplinePointService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected Student $student;
    protected ClassRoom $classRoom;
    protected SchoolYear $schoolYear;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolYear = SchoolYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $this->classRoom = ClassRoom::create([
            'school_year_id' => $this->schoolYear->id,
            'name' => 'X',
            'major' => 'PPLG 1',
        ]);

        $this->studentUser = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Student,
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'class_room_id' => $this->classRoom->id,
            'nis' => '12345',
            'gender' => 'L',
            'birth_date' => '2008-01-01',
            'enrolled_at' => '2025-07-01',
            'is_active' => true,
        ]);
    }

    public function test_points_awarded_on_attendance_sync(): void
    {
        $attendance = Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => Carbon::today()->toDateString(),
            'status' => AttendanceStatus::Hadir,
            'check_in_at' => now(),
            'check_out_at' => now()->addHours(8),
        ]);

        $service = app(DisciplinePointService::class);
        $service->syncAttendancePoints($this->student, $attendance);

        $this->student->refresh();

        $this->assertEquals(10, $this->student->total_points);
        $this->assertEquals(10, $this->student->monthly_points);

        $this->assertDatabaseHas('discipline_points', [
            'student_id' => $this->student->id,
            'points' => 10,
            'reason' => 'Hadir Tepat Waktu (Lengkap Masuk & Pulang)',
        ]);
    }

    public function test_partial_attendance_points_checkin_only(): void
    {
        $attendance = Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => Carbon::today()->toDateString(),
            'status' => AttendanceStatus::Hadir,
            'check_in_at' => now(),
        ]);

        $service = app(DisciplinePointService::class);
        $service->syncAttendancePoints($this->student, $attendance);

        $this->student->refresh();

        $this->assertEquals(7, $this->student->total_points);

        $this->assertDatabaseHas('discipline_points', [
            'student_id' => $this->student->id,
            'points' => 7,
            'reason' => 'Hadir Tepat Waktu (Presensi Masuk)',
        ]);
    }

    public function test_first_checkin_badge_awarded(): void
    {
        $attendance = Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => Carbon::today()->toDateString(),
            'status' => AttendanceStatus::Hadir,
            'check_in_at' => now(),
        ]);

        $service = app(DisciplinePointService::class);
        $service->syncAttendancePoints($this->student, $attendance);

        $this->assertDatabaseHas('student_badges', [
            'student_id' => $this->student->id,
            'badge_key' => 'first_checkin',
        ]);
    }

    public function test_student_can_render_leaderboard_page(): void
    {
        $this->actingAs($this->studentUser);

        $response = $this->get(route('student.leaderboard'));
        $response->assertStatus(200);

        Livewire::test(\App\Livewire\Student\Leaderboard::class)
            ->assertSee('Kedisiplinan')
            ->assertSee('Budi Santoso')
            ->call('setTab', 'all_time')
            ->assertSet('tab', 'all_time');
    }

    public function test_update_leaderboard_command(): void
    {
        // Create second student in same class with higher points
        $user2 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Student,
        ]);
        $student2 = Student::create([
            'user_id' => $user2->id,
            'class_room_id' => $this->classRoom->id,
            'nis' => '12346',
            'gender' => 'P',
            'birth_date' => '2008-02-02',
            'enrolled_at' => '2025-07-01',
            'monthly_points' => 50,
            'total_points' => 50,
            'is_active' => true,
        ]);

        $this->artisan('discipline:update-leaderboard')
            ->assertExitCode(0);

        $student2->refresh();
        $this->student->refresh();

        $this->assertEquals(1, $student2->monthly_rank);
        $this->assertEquals(2, $this->student->monthly_rank);
    }

    public function test_tied_rank_assignment(): void
    {
        $user2 = User::create([
            'name' => 'Ahmad Tied',
            'email' => 'ahmad@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Student,
        ]);
        $student2 = Student::create([
            'user_id' => $user2->id,
            'class_room_id' => $this->classRoom->id,
            'nis' => '12347',
            'gender' => 'L',
            'birth_date' => '2008-03-03',
            'enrolled_at' => '2025-07-01',
            'monthly_points' => 0,
            'total_points' => 0,
            'current_streak' => 0,
            'is_active' => true,
        ]);

        $service = app(DisciplinePointService::class);
        $service->recalculateRanks($this->classRoom->id);

        $this->student->refresh();
        $student2->refresh();

        // Both have 0 points, 0 total, 0 streak, null check in -> tied for rank 1
        $this->assertEquals(1, $this->student->monthly_rank);
        $this->assertEquals(1, $student2->monthly_rank);
    }

    public function test_avg_check_in_seconds_uses_current_month_only(): void
    {
        // Check in last month at 10:00 (36000s)
        Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => Carbon::today()->subMonth()->toDateString(),
            'status' => AttendanceStatus::Hadir,
            'check_in_at' => Carbon::today()->subMonth()->setTime(10, 0, 0),
        ]);

        // Check in this month at 07:00 (25200s)
        $attThisMonth = Attendance::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'date' => Carbon::today()->toDateString(),
            'status' => AttendanceStatus::Hadir,
            'check_in_at' => Carbon::today()->setTime(7, 0, 0),
        ]);

        $service = app(DisciplinePointService::class);
        $service->syncAttendancePoints($this->student, $attThisMonth);

        $this->student->refresh();

        // Should be 07:00:00 = 25200s (ignoring last month's 10:00:00)
        $this->assertEquals(25200, $this->student->avg_check_in_seconds);
    }
}
