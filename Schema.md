# Schema.md — Struktur Database Aplikasi Presensi Digital Sekolah

**Versi:** 2.0 (Production Schema)  
**DBMS:** MySQL 8.0+ / MariaDB  
**Charset & Collation:** `utf8mb4_unicode_ci`  
**Tanggal Update:** Juli 2026  

---

## 1. Entity Relationship Diagram (ERD)

```
users ──1:1── students ──N:1── class_rooms ──N:1── school_years
                  │
                  ├──1:N── attendances ──N:1── leave_requests
                  ├──1:N── leave_requests
                  └──1:N── notifications (polymorphic)

school_years ──1:N── schedules
school_years ──1:N── holidays
school_years ──1:N── class_rooms

settings (key-value global configuration)
```

---

## 2. Rincian Skema Tabel

### 2.1 Tabel `users`
Akun autentikasi untuk role `admin` dan `student`.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (PK) | No | Auto | Primary Key |
| `name` | varchar(150) | No | - | Nama lengkap user |
| `email` | varchar(150) | No | - | Unique email login |
| `password` | varchar(255) | No | - | Hashed password |
| `role` | enum('admin','student') | No | 'student' | User role (`App\Enums\UserRole`) |
| `theme_preference` | enum('light','dark','system') | No | 'system' | Preferensi tema tampilan |
| `last_login_at` | timestamp | Yes | NULL | Timestamp login terakhir |
| `is_active` | boolean | No | true | Status aktif user |
| `email_verified_at` | timestamp | Yes | NULL | Timestamp verifikasi email |
| `remember_token` | varchar(100) | Yes | NULL | Token sesi persistent |
| `created_at` | timestamp | No | - | Timestamp dibuat |
| `updated_at` | timestamp | No | - | Timestamp diperbarui |

**Indeks:** `email` (Unique), `role`.

---

### 2.2 Tabel `students`
Data profil murid, terikat 1:1 dengan `users` dan N:1 dengan `class_rooms`.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (PK) | No | Auto | Primary Key |
| `user_id` | bigint (FK) | No | - | Unique FK → `users.id` (On Delete Cascade) |
| `class_room_id` | bigint (FK) | No | - | FK → `class_rooms.id` |
| `nis` | varchar(30) | No | - | Unique Nomor Induk Siswa |
| `phone` | varchar(20) | Yes | NULL | Nomor HP / WhatsApp murid |
| `address` | text | Yes | NULL | Alamat tempat tinggal |
| `birth_date` | date | No | - | Tanggal lahir murid (untuk ucapan ultah) |
| `profile_photo_path` | varchar(255) | Yes | NULL | Path foto profil di storage |
| `gender` | enum('L','P') | No | 'L' | Jenis kelamin (Laki-laki / Perempuan) |
| `enrolled_at` | date | No | - | Tanggal terdaftar di sekolah |
| `is_active` | boolean | No | true | Status aktif murid |
| `deleted_at` | timestamp | Yes | NULL | Soft delete timestamp |
| `created_at` | timestamp | No | - | Timestamp dibuat |
| `updated_at` | timestamp | No | - | Timestamp diperbarui |

**Indeks:** `user_id` (Unique), `nis` (Unique), `class_room_id`, `birth_date`.

---

### 2.3 Tabel `class_rooms`
Kelas dan jurusan murid terikat pada tahun ajaran aktif.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (PK) | No | Auto | Primary Key |
| `school_year_id` | bigint (FK) | No | - | FK → `school_years.id` |
| `name` | varchar(50) | No | - | Nama kelas (contoh: `X-D3`, `XI-IPA-1`) |
| `major` | varchar(100) | Yes | NULL | Jurusan / Peminatan |
| `deleted_at` | timestamp | Yes | NULL | Soft delete timestamp |
| `created_at` | timestamp | No | - | Timestamp dibuat |
| `updated_at` | timestamp | No | - | Timestamp diperbarui |

**Indeks:** `school_year_id`, Unique(`school_year_id`, `name`).

---

### 2.4 Tabel `school_years`
Tahun ajaran akademik sekolah.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (PK) | No | Auto | Primary Key |
| `name` | varchar(20) | No | - | Nama tahun ajaran (contoh: `2026/2027`) |
| `start_date` | date | No | - | Tanggal mulai |
| `end_date` | date | No | - | Tanggal selesai |
| `is_active` | boolean | No | false | Menandakan tahun ajaran aktif |
| `created_at` | timestamp | No | - | Timestamp dibuat |
| `updated_at` | timestamp | No | - | Timestamp diperbarui |

**Indeks:** `is_active`.

---

### 2.5 Tabel `schedules`
Pengaturan jam presensi & hari sekolah per hari (Senin-Minggu).

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (PK) | No | Auto | Primary Key |
| `school_year_id` | bigint (FK) | No | - | FK → `school_years.id` |
| `day_of_week` | smallint | No | - | Hari (0=Minggu, 1=Senin, ..., 6=Sabtu) |
| `check_in_time` | time | No | - | Jam target presensi masuk |
| `check_in_tolerance_minutes` | smallint | No | 10 | Toleransi keterlambatan (menit) |
| `check_out_time` | time | No | - | Jam minimal presensi pulang |
| `is_school_day` | boolean | No | true | `false` = hari libur sekolah (Sabtu/Minggu) |
| `created_at` | timestamp | No | - | Timestamp dibuat |
| `updated_at` | timestamp | No | - | Timestamp diperbarui |

**Indeks:** Unique(`school_year_id`, `day_of_week`).

---

### 2.6 Tabel `holidays`
Kalender hari libur nasional & khusus sekolah.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (PK) | No | Auto | Primary Key |
| `school_year_id` | bigint (FK) | No | - | FK → `school_years.id` |
| `date` | date | No | - | Tanggal libur |
| `name` | varchar(150) | No | - | Keterangan libur (contoh: `Hari Kemerdekaan RI`) |
| `type` | enum('national','school') | No | 'school' | Kategori libur |
| `created_at` | timestamp | No | - | Timestamp dibuat |
| `updated_at` | timestamp | No | - | Timestamp diperbarui |

**Indeks:** `date`, Unique(`school_year_id`, `date`).

---

### 2.7 Tabel `school_locations`
Koordinat titik GPS geofence sekolah.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (PK) | No | Auto | Primary Key |
| `name` | varchar(100) | No | - | Nama lokasi sekolah |
| `latitude` | decimal(10,7) | No | - | Koordinat Latitude GPS |
| `longitude` | decimal(10,7) | No | - | Koordinat Longitude GPS |
| `radius_meters` | integer | No | 100 | Radius batas presensi (meter) |
| `is_active` | boolean | No | true | Status lokasi aktif |
| `created_at` | timestamp | No | - | Timestamp dibuat |
| `updated_at` | timestamp | No | - | Timestamp diperbarui |

---

### 2.8 Tabel `attendances`
Catatan presensi harian murid.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (PK) | No | Auto | Primary Key |
| `student_id` | bigint (FK) | No | - | FK → `students.id` |
| `school_year_id` | bigint (FK) | No | - | FK → `school_years.id` |
| `date` | date | No | - | Tanggal presensi |
| `check_in_at` | timestamp | Yes | NULL | Jam presensi masuk |
| `check_in_photo_path` | varchar(255) | Yes | NULL | File foto selfie masuk |
| `check_in_latitude` | decimal(10,7) | Yes | NULL | Latitude GPS presensi masuk |
| `check_in_longitude` | decimal(10,7) | Yes | NULL | Longitude GPS presensi masuk |
| `check_in_distance_meters` | decimal(8,2) | Yes | NULL | Jarak GPS terhitung dari sekolah |
| `check_out_at` | timestamp | Yes | NULL | Jam presensi pulang |
| `check_out_photo_path` | varchar(255) | Yes | NULL | File foto selfie pulang |
| `check_out_latitude` | decimal(10,7) | Yes | NULL | Latitude GPS presensi pulang |
| `check_out_longitude` | decimal(10,7) | Yes | NULL | Longitude GPS presensi pulang |
| `check_out_distance_meters` | decimal(8,2) | Yes | NULL | Jarak GPS presensi pulang |
| `status` | enum('hadir','terlambat','izin','sakit','alpa') | No | - | Status kehadiran (`App\Enums\AttendanceStatus`) |
| `is_manual_correction` | boolean | No | false | Flag jika dikoreksi manual |
| `corrected_by` | bigint (FK) | Yes | NULL | FK → `users.id` |
| `correction_reason` | text | Yes | NULL | Alasan koreksi presensi |
| `leave_request_id` | bigint (FK) | Yes | NULL | FK → `leave_requests.id` |
| `created_at` | timestamp | No | - | Timestamp dibuat |
| `updated_at` | timestamp | No | - | Timestamp diperbarui |

**Indeks:** Unique(`student_id`, `date`), `date`, `status`, `student_id`.

---

### 2.9 Tabel `leave_requests`
Pengajuan izin dan sakit murid.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (PK) | No | Auto | Primary Key |
| `student_id` | bigint (FK) | No | - | FK → `students.id` |
| `type` | enum('izin','sakit') | No | - | Jenis pengajuan |
| `date` | date | No | - | Tanggal izin/sakit yang diajukan |
| `reason` | text | No | - | Alasan pengajuan |
| `attachment_path` | varchar(255) | Yes | NULL | File lampiran surat dokter/orang tua |
| `status` | enum('pending','approved','rejected') | No | 'pending' | Status pengajuan (`App\Enums\LeaveStatus`) |
| `reviewed_by` | bigint (FK) | Yes | NULL | FK → `users.id` Admin reviewer |
| `reviewed_at` | timestamp | Yes | NULL | Waktu diproses |
| `review_note` | text | Yes | NULL | Catatan dari Admin |
| `created_at` | timestamp | No | - | Timestamp dibuat |
| `updated_at` | timestamp | No | - | Timestamp diperbarui |

**Indeks:** `student_id`, `status`, `date`.

---

### 2.10 Tabel `notifications`
Notifikasi in-app realtime untuk murid dan admin.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (PK) | No | Auto | Primary Key |
| `user_id` | bigint (FK) | No | - | FK → `users.id` (Penerima notifikasi) |
| `type` | enum(...) | No | - | Type: `leave_status`, `absence_reminder`, `birthday`, `new_leave_request`, `system` |
| `title` | varchar(150) | No | - | Judul notifikasi |
| `body` | text | No | - | Isi notifikasi |
| `related_type` | varchar(50) | Yes | NULL | Nama class model terkait |
| `related_id` | bigint | Yes | NULL | ID record model terkait |
| `is_read` | boolean | No | false | Status sudah dibaca |
| `read_at` | timestamp | Yes | NULL | Waktu dibaca |
| `created_at` | timestamp | No | - | Timestamp dibuat |

**Indeks:** `user_id`, `is_read`.

---

### 2.11 Tabel `settings`
Pengaturan konfigurasi global sekolah dan sistem (key-value).

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (PK) | No | Auto | Primary Key |
| `key` | varchar(100) | No | - | Unique setting key (contoh: `school_name`, `school_address`, `school_phone`) |
| `value` | text | Yes | NULL | Nilai pengaturan |
| `description` | varchar(255) | Yes | NULL | Deskripsi penjelasan setting |
| `created_at` | timestamp | No | - | Timestamp dibuat |
| `updated_at` | timestamp | No | - | Timestamp diperbarui |

**Indeks:** `key` (Unique).
