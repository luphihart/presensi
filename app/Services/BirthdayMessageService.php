<?php

namespace App\Services;

use App\Models\Student;

class BirthdayMessageService
{
    private array $templates = [
        "Happy Birthday, :name! 🥳 Semoga harimu makin epic, makin pinter, dan selalu gaspol kelasss!",
        "Met ultah, :name! 🎂 Tetap glowing, semangat terus belajarnya dan stay cool selalu!",
        "HBD :name! 🎉 Waktunya leveling up! Semoga semua impianmu tercapai tahun ini!",
        "Happy Level Up Day, :name! ✨ Makin keren, makin berprestasi, and stay awesome!",
        "Selamat Ulang Tahun, :name! 🎈 Semoga makin sukses, makin bersinar, dan jadi kebanggaan sekolah!",
        "HBD :name! 🌟 Tambah umur, tambah jago, dan selalu dipenuhi kebahagiaan setiap hari!",
        "Happy Birthday, :name! 🚀 Tetap semangat kejar cita-cita dan terus jadi versi terbaik dirimu!",
        "Met ultah, :name! 🎁 Semoga makin berprestasi, sehat selalu, dan harimu penuh senyuman!",
        "Happy Level Up, :name! 🏆 Makin solid, makin cerdas, dan makin sukses di masa depan!",
        "Selamat Ultah, :name! 🌈 Semoga usiamu yang baru membawa banyak keberkahan dan prestasi baru!",
    ];

    public function getMessage(Student $student): string
    {
        $template = $this->templates[array_rand($this->templates)];
        $firstName = explode(' ', trim($student->user->name))[0];
        return str_replace(':name', $firstName, $template);
    }
}
