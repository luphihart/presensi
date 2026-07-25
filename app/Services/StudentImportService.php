<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\ClassRoom;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StudentImportService
{
    /**
     * Import students from parsed array data (from Maatwebsite Excel).
     * Automatically maps header titles or defaults to expected column indices.
     * Handles soft-deleted records gracefully by restoring & updating them.
     */
    public function import(array $rows): array
    {
        // Increase execution time for bulk imports
        set_time_limit(300);

        $schoolYear = SchoolYear::getActive();
        if (!$schoolYear) {
            return ['success' => false, 'message' => 'Tidak ada tahun ajaran aktif.'];
        }

        $imported = 0;
        $errors = [];

        // Default column indices for the Excel template
        $colIndex = [
            'nis' => 0,
            'name' => 1,
            'email' => 2,
            'password' => 3,
            'class' => 4,
            'gender' => 5,
            'birth_date' => 6,
            'phone' => 7,
            'address' => 8,
        ];

        $headerRowFound = false;

        foreach ($rows as $index => $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            $firstCell = strtolower(trim((string)($row[0] ?? '')));

            // Skip instruction header rows
            if (str_contains($firstCell, 'template') || str_contains($firstCell, 'petunjuk')) {
                continue;
            }

            // Check if current row is column header row
            if (!$headerRowFound && (str_starts_with($firstCell, 'nis') || str_contains($firstCell, 'nama') || str_contains($firstCell, 'email'))) {
                $headerRowFound = true;
                // Dynamically map columns from header title
                foreach ($row as $cIdx => $cVal) {
                    $cValLower = strtolower(trim((string)$cVal));
                    if ($cValLower === 'nis' || str_starts_with($cValLower, 'nis') || str_contains($cValLower, 'nomor induk')) {
                        $colIndex['nis'] = $cIdx;
                    } elseif (str_contains($cValLower, 'nama')) {
                        $colIndex['name'] = $cIdx;
                    } elseif (str_contains($cValLower, 'email')) {
                        $colIndex['email'] = $cIdx;
                    } elseif (str_contains($cValLower, 'pass')) {
                        $colIndex['password'] = $cIdx;
                    } elseif (str_contains($cValLower, 'kelas')) {
                        $colIndex['class'] = $cIdx;
                    } elseif (str_contains($cValLower, 'kelamin') || str_contains($cValLower, 'gender')) {
                        $colIndex['gender'] = $cIdx;
                    } elseif (str_contains($cValLower, 'lahir')) {
                        $colIndex['birth_date'] = $cIdx;
                    } elseif (str_contains($cValLower, 'telepon') || str_contains($cValLower, 'phone') || str_contains($cValLower, 'telp')) {
                        $colIndex['phone'] = $cIdx;
                    } elseif (str_contains($cValLower, 'alamat') || str_contains($cValLower, 'address')) {
                        $colIndex['address'] = $cIdx;
                    }
                }
                continue;
            }

            $nis = trim((string)($row[$colIndex['nis']] ?? ''));
            $name = trim((string)($row[$colIndex['name']] ?? ''));
            $email = trim((string)($row[$colIndex['email']] ?? ''));
            $password = trim((string)($row[$colIndex['password']] ?? ''));
            $className = trim((string)($row[$colIndex['class']] ?? ''));
            $gender = strtoupper(trim((string)($row[$colIndex['gender']] ?? 'L')));
            $rawBirthDate = trim((string)($row[$colIndex['birth_date']] ?? ''));
            $phone = trim((string)($row[$colIndex['phone']] ?? ''));
            $address = trim((string)($row[$colIndex['address']] ?? ''));

            if (empty($name) || empty($email) || empty($nis) || empty($className)) {
                $errors[] = "Baris " . ($index + 1) . ": Data tidak lengkap (NIS, Nama, Email, dan Kelas wajib diisi).";
                continue;
            }

            // Safe birth date parsing
            $formattedBirthDate = '2010-01-01';
            if (!empty($rawBirthDate)) {
                try {
                    if (is_numeric($rawBirthDate)) {
                        $formattedBirthDate = ExcelDate::excelToDateTimeObject((float)$rawBirthDate)->format('Y-m-d');
                    } else {
                        $formattedBirthDate = Carbon::parse($rawBirthDate)->format('Y-m-d');
                    }
                } catch (\Throwable $t) {
                    $formattedBirthDate = '2010-01-01';
                }
            }

            try {
                DB::transaction(function () use ($schoolYear, $name, $email, $password, $nis, $className, $formattedBirthDate, $gender, $phone, $address, &$imported) {
                    $classRoom = ClassRoom::firstOrCreate([
                        'school_year_id' => $schoolYear->id,
                        'name' => $className,
                    ]);

                    // Find existing user including soft-deleted ones
                    $user = User::withTrashed()->where('email', $email)->first();

                    $userData = [
                        'name' => $name,
                        'role' => UserRole::Student,
                        'is_active' => true,
                    ];
                    if (!empty($password)) {
                        $userData['password'] = Hash::make($password);
                    } elseif (!$user) {
                        $userData['password'] = Hash::make('password123');
                    }

                    if ($user) {
                        if ($user->trashed()) {
                            $user->restore();
                        }
                        $user->update($userData);
                    } else {
                        $user = User::create(array_merge(['email' => $email], $userData));
                    }

                    // Find existing student record by user_id OR nis, including soft-deleted ones
                    $student = Student::withTrashed()
                        ->where('user_id', $user->id)
                        ->orWhere('nis', $nis)
                        ->first();

                    $studentData = [
                        'user_id' => $user->id,
                        'class_room_id' => $classRoom->id,
                        'nis' => $nis,
                        'gender' => in_array($gender, ['L', 'P']) ? $gender : 'L',
                        'birth_date' => $formattedBirthDate,
                        'phone' => !empty($phone) ? $phone : null,
                        'address' => !empty($address) ? $address : null,
                        'enrolled_at' => now()->toDateString(),
                        'is_active' => true,
                    ];

                    if ($student) {
                        if ($student->trashed()) {
                            $student->restore();
                        }
                        $student->update($studentData);
                    } else {
                        Student::create($studentData);
                    }

                    $imported++;
                });
            } catch (\Throwable $e) {
                $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'imported_count' => $imported,
            'errors' => $errors,
        ];
    }
}
