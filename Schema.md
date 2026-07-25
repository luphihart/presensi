# Schema.md — Struktur Database Aplikasi Presensi Murid

**Versi:** 1.0
**DBMS:** MySQL 8.0+ (disesuaikan untuk shared hosting cPanel)

---

## 1. Entity Relationship Overview

```
users ──1:1── students ──N:1── class_rooms ──N:1── school_years
                  │
                  ├──1:N── attendances
                  ├──1:N── leave_requests
                  └──1:N── notifications (polymorphic)

school_years ──1:N── schedules
school_years ──1:N── holidays
school_years ──1:N── class_rooms

users ──1:N── audit_logs (sebagai actor)
attendances ──1:N── audit_logs (sebagai target, polymorphic)
leave_requests ──1:N── audit_logs (sebagai target, polymorphic)
```

---

## 2. Tabel: `users`

Menyimpan akun login untuk kedua role (murid & admin). Data spesifik murid dipisah ke tabel `students` (1:1).

| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK, auto-increment |
| name | varchar(150) | not null |
| email | varchar(150) | unique, not null |
| password | varchar(255) | not null (hashed) |
| role | enum('admin','student') | not null, default 'student' |
| theme_preference | enum('light','dark','system') | default 'system' |
| last_login_at | timestamp | nullable |
| is_active | boolean | not null, default true |
| email_verified_at | timestamp | nullable |
| remember_token | varchar(100) | nullable |
| created_at | timestamp | not null |
| updated_at | timestamp | not null |

**Index:** `email` (unique), `role`.

---

## 3. Tabel: `school_years`

Tahun ajaran — pusat pemisahan data historis.

| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK |
| name | varchar(20) | not null, contoh: `2026/2027` |
| start_date | date | not null |
| end_date | date | not null |
| is_active | boolean | not null, default false |
| created_at | timestamp | not null |
| updated_at | timestamp | not null |

**Constraint:** hanya satu baris boleh `is_active = true` (ditegakkan di level aplikasi/trigger).
**Index:** `is_active`.

---

## 4. Tabel: `class_rooms`

Kelas/rombel, terikat ke tahun ajaran (agar kenaikan kelas tidak menimpa histori).

| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK |
| school_year_id | bigint | FK → school_years.id, not null |
| name | varchar(50) | not null, contoh: `7A`, `XI IPA 2` |
| created_at | timestamp | not null |
| updated_at | timestamp | not null |

**Index:** `school_year_id`.
**Unique:** (`school_year_id`, `name`).

---

## 5. Tabel: `students`

Data spesifik murid, 1:1 dengan `users`.

| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK |
| user_id | bigint | FK → users.id, unique, not null |
| class_room_id | bigint | FK → class_rooms.id, not null |
| nis | varchar(30) | unique, not null (nomor induk siswa) |
| phone | varchar(20) | nullable |
| address | text | nullable |
| birth_date | date | not null |
| profile_photo_path | varchar(255) | nullable |
| gender | enum('L','P') | not null |
| enrolled_at | date | not null |
| is_active | boolean | not null, default true |
| created_at | timestamp | not null |
| updated_at | timestamp | not null |

**Index:** `class_room_id`, `nis` (unique), `birth_date` (untuk query ucapan ulang tahun — index pada `EXTRACT(month FROM birth_date), EXTRACT(day FROM birth_date)` atau kolom generated `birth_month_day`).

---

## 6. Tabel: `schedules`

Jam masuk per hari, dapat berbeda tiap hari, terikat tahun ajaran.

| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK |
| school_year_id | bigint | FK → school_years.id, not null |
| day_of_week | smallint | not null, 0=Minggu … 6=Sabtu |
| check_in_time | time | not null |
| check_in_tolerance_minutes | smallint | not null, default 10 (grace period sebelum dihitung telat) |
| check_out_time | time | not null |
| is_school_day | boolean | not null, default true (false = hari tsb memang tidak ada sekolah, mis. Minggu) |
| created_at | timestamp | not null |
| updated_at | timestamp | not null |

**Unique:** (`school_year_id`, `day_of_week`).

---

## 7. Tabel: `holidays`

Hari libur, tanggal merah, libur khusus sekolah (untuk murid ditampilkan sebagai "hari libur").

| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK |
| school_year_id | bigint | FK → school_years.id, not null |
| date | date | not null |
| name | varchar(150) | not null, contoh: `Hari Kemerdekaan`, `Libur Semester Ganjil` |
| type | enum('national','school') | not null (tanggal merah nasional vs libur internal sekolah) |
| created_at | timestamp | not null |
| updated_at | timestamp | not null |

**Unique:** (`school_year_id`, `date`).
**Index:** `date`.

---

## 8. Tabel: `school_locations`

Titik koordinat sekolah untuk validasi geofencing (mendukung lebih dari satu titik, mis. beberapa gerbang).

| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK |
| name | varchar(100) | not null, contoh: `Gerbang Utama` |
| latitude | decimal(10,7) | not null |
| longitude | decimal(10,7) | not null |
| radius_meters | integer | not null, default 100 |
| is_active | boolean | not null, default true |
| created_at | timestamp | not null |
| updated_at | timestamp | not null |

---

## 9. Tabel: `attendances`

Catatan presensi. Satu baris per **sesi** (masuk/pulang) per murid per hari — atau alternatif: satu baris per hari dengan dua pasang kolom. Direkomendasikan **satu baris per hari** agar lebih mudah query status harian.

| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK |
| student_id | bigint | FK → students.id, not null |
| school_year_id | bigint | FK → school_years.id, not null |
| date | date | not null |
| check_in_at | timestamp | nullable |
| check_in_photo_path | varchar(255) | nullable |
| check_in_latitude | decimal(10,7) | nullable |
| check_in_longitude | decimal(10,7) | nullable |
| check_in_distance_meters | decimal(8,2) | nullable (jarak terhitung saat submit, untuk audit) |
| check_out_at | timestamp | nullable |
| check_out_photo_path | varchar(255) | nullable |
| check_out_latitude | decimal(10,7) | nullable |
| check_out_longitude | decimal(10,7) | nullable |
| check_out_distance_meters | decimal(8,2) | nullable |
| status | enum('hadir','terlambat','izin','sakit','alpa') | not null |
| is_manual_correction | boolean | not null, default false |
| corrected_by | bigint | FK → users.id, nullable |
| correction_reason | text | nullable |
| leave_request_id | bigint | FK → leave_requests.id, nullable (terisi jika status izin/sakit) |
| created_at | timestamp | not null |
| updated_at | timestamp | not null |

**Unique:** (`student_id`, `date`) — mencegah duplikasi record harian, sekaligus mencegah presensi ganda.
**Index:** `date`, `status`, `student_id`.
**Check constraint:** jika `is_manual_correction = true` maka `corrected_by` dan `correction_reason` wajib terisi (ditegakkan di level aplikasi).

---

## 10. Tabel: `leave_requests`

Pengajuan izin/sakit murid.

| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK |
| student_id | bigint | FK → students.id, not null |
| type | enum('izin','sakit') | not null |
| date | date | not null (tanggal yang diajukan) |
| reason | text | not null |
| attachment_path | varchar(255) | nullable |
| status | enum('pending','approved','rejected') | not null, default 'pending' |
| reviewed_by | bigint | FK → users.id, nullable |
| reviewed_at | timestamp | nullable |
| review_note | text | nullable |
| created_at | timestamp | not null |
| updated_at | timestamp | not null |

**Index:** `student_id`, `status`, `date`.

---

## 11. Tabel: `notifications`

Notifikasi in-app (bisa memanfaatkan Laravel Notifications bawaan dengan tabel default, atau custom seperti berikut untuk kontrol lebih eksplisit).

| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK |
| user_id | bigint | FK → users.id, not null (penerima) |
| type | enum('leave_status','absence_reminder','birthday','new_leave_request','system') | not null |
| title | varchar(150) | not null |
| body | text | not null |
| related_type | varchar(50) | nullable (nama model terkait, mis. `LeaveRequest`) |
| related_id | bigint | nullable |
| is_read | boolean | not null, default false |
| read_at | timestamp | nullable |
| created_at | timestamp | not null |

**Index:** `user_id`, `is_read`.

---

## 12. Tabel: `audit_logs`

Jejak audit untuk aksi sensitif (polymorphic terhadap entitas yang diubah).

| Kolom | Tipe | Constraint |
|---|---|---|
| id | bigint | PK |
| actor_id | bigint | FK → users.id, not null (siapa yang melakukan aksi) |
| action | varchar(100) | not null, contoh: `attendance.corrected`, `leave_request.approved`, `holiday.created` |
| subject_type | varchar(100) | not null (nama model, mis. `Attendance`) |
| subject_id | bigint | not null |
| old_values | jsonb | nullable |
| new_values | jsonb | nullable |
| ip_address | varchar(45) | nullable |
| created_at | timestamp | not null |

**Index:** `subject_type, subject_id`, `actor_id`, `created_at`.

---

## 13. Ringkasan Relasi & Aturan Integritas

| Relasi | Tipe | Aturan |
|---|---|---|
| users → students | 1:1 | Hapus user (soft delete) tidak menghapus histori presensi |
| school_years → class_rooms | 1:N | Kelas terikat tahun ajaran, tidak reusable lintas tahun |
| class_rooms → students | 1:N | Murid pindah kelas antar tahun = record `class_room_id` baru per tahun ajaran (via mekanisme kenaikan kelas, bukan update in-place) |
| school_years → schedules | 1:N | Jadwal jam masuk spesifik per tahun ajaran (7 baris per tahun ajaran, satu per hari) |
| school_years → holidays | 1:N | Libur spesifik per tahun ajaran |
| students → attendances | 1:N | Satu baris per hari per murid (unique constraint) |
| students → leave_requests | 1:N | Tidak dibatasi jumlah (tanpa kuota) |
| leave_requests → attendances | 1:1 (opsional) | Saat izin disetujui, sistem otomatis membuat/update record attendance hari itu dengan status sesuai (izin/sakit) |

---

## 14. Catatan Implementasi (MySQL / Shared Hosting)

- Semua tabel menggunakan **soft delete** (`deleted_at`) untuk `users`, `students`, `class_rooms` — agar histori presensi/izin tidak hilang saat data dihapus.
- Foto (profil, presensi) disimpan sebagai path relatif di storage, bukan BLOB di database.
- Perhitungan geofencing (`check_in_distance_meters`) dihitung di level aplikasi (Haversine, PHP native) sebelum insert — **bukan** via ekstensi spasial MySQL, karena dukungan spatial index/`ST_Distance_Sphere` tidak selalu aktif/konsisten di paket shared hosting. Pendekatan aplikasi-level ini juga membuat kode portable jika kelak pindah DBMS.
- Kolom `enum` diimplementasikan sebagai **native `ENUM` type MySQL** (`ENUM('hadir','terlambat','izin','sakit','alpa')`) — didukung penuh oleh MySQL dan oleh migration Laravel (`$table->enum(...)`), lebih sederhana dibanding pendekatan `CHECK constraint` yang dipakai di PostgreSQL.
- Kolom `jsonb` (dipakai di `audit_logs.old_values` / `new_values`) diganti menjadi tipe **`JSON`** native MySQL (tersedia sejak MySQL 5.7+, didukung penuh di MySQL 8.0). Fungsionalitas query JSON sedikit lebih terbatas dibanding `jsonb` PostgreSQL, tapi cukup untuk kebutuhan audit log (baca-tulis utuh, jarang di-query per-field).
- Gunakan **`utf8mb4`** sebagai charset & collation default di seluruh tabel (`utf8mb4_unicode_ci`) — penting agar mendukung emoji (dipakai di pesan ucapan ulang tahun bergaya Gen Z/Alpha) dan karakter khusus tanpa error.
- Perhatikan **batas resource shared hosting**: hindari query dengan `JOIN` berlapis-lapis tanpa index yang tepat, karena shared hosting biasanya membatasi waktu eksekusi query dan penggunaan CPU per proses. Semua kolom yang disebutkan di bagian index tiap tabel wajib dibuat sejak migration awal, bukan ditambahkan belakangan.
