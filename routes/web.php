<?php

use App\Enums\UserRole;
use App\Livewire\Auth\Login;
use App\Livewire\Student\Dashboard as StudentDashboard;
use App\Livewire\Student\AttendanceCheckIn;
use App\Livewire\Student\AttendanceCheckOut;
use App\Livewire\Student\LeaveRequestForm;
use App\Livewire\Student\AttendanceHistory;
use App\Livewire\Student\ProfileEdit;

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\StudentManagement\StudentTable;
use App\Livewire\Admin\StudentManagement\StudentForm;
use App\Livewire\Admin\StudentManagement\StudentImport;
use App\Livewire\Admin\AttendanceManagement\AttendanceTable;
use App\Livewire\Admin\LeaveRequestManagement;
use App\Livewire\Admin\SchoolLocationManagement;
use App\Livewire\Admin\ClassRoomManagement;
use App\Livewire\Admin\ScheduleManagement;
use App\Livewire\Admin\HolidayCalendar;
use App\Livewire\Admin\ReportGenerator;
use App\Livewire\Admin\SchoolSettings;
use App\Livewire\Admin\WaliKelasManagement;
use App\Livewire\Admin\ChangePassword as AdminChangePassword;
use App\Livewire\Admin\AnnouncementManagement;

use App\Livewire\Student\ChangePassword as StudentChangePassword;
use App\Livewire\Student\AnnouncementList as StudentAnnouncementList;
use App\Livewire\Student\Leaderboard as StudentLeaderboard;

use App\Livewire\WaliKelas\Dashboard as WaliKelasDashboard;
use App\Livewire\WaliKelas\StudentList as WaliKelasStudentList;
use App\Livewire\WaliKelas\AttendanceTable as WaliKelasAttendanceTable;
use App\Livewire\WaliKelas\LeaveRequests as WaliKelasLeaveRequests;
use App\Livewire\WaliKelas\Report as WaliKelasReport;
use App\Livewire\WaliKelas\ChangePassword as WaliKelasChangePassword;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === UserRole::Admin) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === UserRole::WaliKelas) {
            return redirect()->route('wali_kelas.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    }
    return redirect()->route('login');
});

// Guest Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');

// Student Routes
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', StudentDashboard::class)->name('dashboard');
    Route::get('/attendance/check-in', AttendanceCheckIn::class)->name('attendance.check-in');
    Route::get('/attendance/check-out', AttendanceCheckOut::class)->name('attendance.check-out');
    Route::get('/leave', LeaveRequestForm::class)->name('leave.index');
    Route::get('/history', AttendanceHistory::class)->name('history');
    Route::get('/profile', ProfileEdit::class)->name('profile');
    Route::get('/password', StudentChangePassword::class)->name('password');
    Route::get('/announcements', StudentAnnouncementList::class)->name('announcements');
    Route::get('/leaderboard', StudentLeaderboard::class)->name('leaderboard');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/students', StudentTable::class)->name('students.index');
    Route::get('/students/create', StudentForm::class)->name('students.create');
    Route::get('/students/edit/{id}', StudentForm::class)->name('students.edit');
    Route::get('/students/import', StudentImport::class)->name('students.import');
    Route::get('/wali-kelas', WaliKelasManagement::class)->name('wali-kelas.index');
    Route::get('/classes', ClassRoomManagement::class)->name('classes.index');
    Route::get('/attendance', AttendanceTable::class)->name('attendance.index');
    Route::get('/leave-requests', LeaveRequestManagement::class)->name('leave-requests.index');
    Route::get('/schedules', ScheduleManagement::class)->name('schedules.index');
    Route::get('/locations', SchoolLocationManagement::class)->name('locations.index');
    Route::get('/holidays', HolidayCalendar::class)->name('holidays.index');
    Route::get('/reports', ReportGenerator::class)->name('reports.index');
    Route::get('/settings', SchoolSettings::class)->name('settings.index');
    Route::get('/password', AdminChangePassword::class)->name('password');
    Route::get('/announcements', AnnouncementManagement::class)->name('announcements.index');
});

// Wali Kelas Routes
Route::middleware(['auth', 'wali_kelas'])->prefix('wali-kelas')->name('wali_kelas.')->group(function () {
    Route::get('/dashboard', WaliKelasDashboard::class)->name('dashboard');
    Route::get('/students', WaliKelasStudentList::class)->name('students');
    Route::get('/attendance', WaliKelasAttendanceTable::class)->name('attendance');
    Route::get('/leave-requests', WaliKelasLeaveRequests::class)->name('leave-requests');
    Route::get('/reports', WaliKelasReport::class)->name('reports');
    Route::get('/password', WaliKelasChangePassword::class)->name('password');
});
