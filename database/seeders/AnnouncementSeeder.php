<?php

namespace Database\Seeders;

use App\Enums\AnnouncementStatus;
use App\Enums\NotificationType;
use App\Models\Announcement;
use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', UserRole::Admin)->first();

        $announcement = Announcement::create([
            'title' => 'Penting: Kewajiban Presensi Harian & Imbauan Ubah Password Akun',
            'content' => "Yth. Seluruh Siswa/i,\n\nDiberitahukan kepada seluruh murid mengenai 2 poin penting berikut:\n\n1. Kewajiban Presensi Harian: Dihimbau kepada seluruh murid untuk melakukan presensi tepat waktu setiap hari sekolah. Data rekapitulasi presensi ini akan digunakan secara resmi untuk data kehadiran pada Rapot Siswa.\n\n2. Himbauan Ubah Password: Demi menjaga keamanan akun masing-masing, diharapkan seluruh murid segera melakukan perubahan password akun Anda melalui menu Profil -> Ubah Password.\n\nTerima kasih atas perhatian dan kerja samanya.",
            'created_by' => $admin?->id,
            'status' => AnnouncementStatus::Published,
            'published_at' => now(),
        ]);

        // Send notification to active students
        $students = Student::where('is_active', true)->get();
        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->user_id,
                'type' => NotificationType::Announcement,
                'title' => '📢 Pengumuman Baru',
                'body' => $announcement->title,
                'related_type' => Announcement::class,
                'related_id' => $announcement->id,
            ]);
        }
    }
}
