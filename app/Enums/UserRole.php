<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Student = 'student';
    case WaliKelas = 'wali_kelas';

    public function label(): string
    {
        return match($this) {
            self::Admin => 'Administrator',
            self::Student => 'Murid',
            self::WaliKelas => 'Wali Kelas',
        };
    }
}

