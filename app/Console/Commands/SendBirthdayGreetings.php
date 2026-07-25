<?php

namespace App\Console\Commands;

use App\Enums\NotificationType;
use App\Models\Notification;
use App\Models\Student;
use App\Services\BirthdayMessageService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBirthdayGreetings extends Command
{
    protected $signature = 'birthday:send-greetings';
    protected $description = 'Send Gen Z / Alpha style birthday greetings to students celebrating today';

    public function handle(BirthdayMessageService $birthdayService): int
    {
        $today = Carbon::today();
        $monthDay = $today->format('m-d');

        $birthdayStudents = Student::where('is_active', true)
            ->whereRaw("DATE_FORMAT(birth_date, '%m-%d') = ?", [$monthDay])
            ->get();

        $count = 0;
        foreach ($birthdayStudents as $student) {
            // Check idempotency (prevent duplicate notification on same day)
            $alreadySent = Notification::where('user_id', $student->user_id)
                ->where('type', NotificationType::Birthday)
                ->whereDate('created_at', $today->toDateString())
                ->exists();

            if ($alreadySent) continue;

            $message = $birthdayService->getMessage($student);

            Notification::create([
                'user_id' => $student->user_id,
                'type' => NotificationType::Birthday,
                'title' => 'Selamat Ulang Tahun! 🎉',
                'body' => $message,
            ]);

            $count++;
        }

        $this->info("Kirim ucapan ulang tahun selesai. $count murid diberi notifikasi.");
        return Command::SUCCESS;
    }
}
