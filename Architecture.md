# Architecture.md — Aplikasi Presensi Digital Sekolah

**Versi:** 2.0 (Production Deployed Architecture)  
**Status:** Deployed & Production Ready  
**Domain:** `https://presensi.sinaumedia.my.id`  
**Server IP:** `202.10.48.207`  
**Tanggal Update:** Juli 2026  

---

## 1. Ringkasan Arsitektur

Aplikasi dibangun sebagai **monolith modular reaktif** berbasis Laravel 12.x dan Livewire 3.x, dideploy pada **Virtual Private Server (VPS)** Linux Ubuntu/Debian dengan lingkungan Nginx web server, PHP-FPM 8.2/8.3, dan MySQL database engine.

Sistem memanfaatkan pendekatan Livewire 3 full-stack reaktif tanpa memerlukan API terpisah, memadukan pustaka **Leaflet.js** untuk peta presensi GPS interaktif, **Maatwebsite Excel** untuk ekspor data matriks, dan **DomPDF** untuk ekspor PDF lanskap.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        VPS Linux (Ubuntu / Debian)                     │
│                                                                        │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │  Nginx Web Server + SSL (Let's Encrypt / Certbot)                │  │
│  │  - Reverse Proxy / FastCGI Pass to PHP 8.2-FPM                   │  │
│  │  - SSL Termination & HTTPS Redirection                           │  │
│  │  - FastCGI Params: HTTPS on, X-Forwarded-Proto https             │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                 │                                      │
│                                 ▼                                      │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │                    Laravel 12.x App (PHP-FPM)                    │  │
│  │   ┌──────────────┐         ┌──────────────┐         ┌──────────┐ │  │
│  │   │ Livewire 3   │         │ Livewire 3   │         │ Leaflet  │ │  │
│  │   │ (Murid UI)   │         │ (Admin UI)   │         │ GPS Map  │ │  │
│  │   └──────────────┘         └──────────────┘         └──────────┘ │  │
│  │         └─────────┬────────────────┘                             │  │
│  │            Shared Blade Component Library (Design System)        │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                 │                                      │
│         ┌───────────────────────┼────────────────────────┐             │
│         ▼                       ▼                        ▼             │
│  ┌─────────────┐       ┌─────────────────┐      ┌──────────────────┐   │
│  │    MySQL    │       │ Linux Crontab   │      │ Local Storage    │   │
│  │ (database:  │       │ (tiap menit:    │      │ (profile-photos, │   │
│  │  presensi2) │       │  schedule:run)  │      │  attendance-     │   │
│  │             │       │                 │      │  photos)         │   │
│  └─────────────┘       └─────────────────┘      └──────────────────┘   │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Tech Stack Terimplementasi

| Layer | Komponen / Package | Alasan & Peran |
|---|---|---|
| **Backend Core** | Laravel 12.x (PHP 8.2+) | Framework inti monolith, routing, validation, middleware, ORM Eloquent, migration, seeder |
| **Reactivity Engine** | Livewire 3.x | Komponen reaktif tanpa full SPA overhead; mengelola form, tabel, modal, dan state UI |
| **Frontend Styling** | Tailwind CSS v4 + Vanilla CSS | Utility-first styling dengan sistem token CSS Variables (`--color-bg`, `--color-surface`, `--color-primary`, dll) |
| **Interactive Map** | Leaflet.js v1.9.4 | Render peta GPS interaktif sebaran posisi presensi murid & radius geofence di Admin Dashboard |
| **Image Processing** | GD / Intervention Image v3 (`ImageCompressionService`) | Kompresi instant foto presensi dan foto profil murid (resizing, WebP/JPEG compression) |
| **PDF Export** | `barryvdh/laravel-dompdf` (v3.0) | Render laporan lanskap presensi bulanan ke PDF A4 siap cetak |
| **Excel Export / Import** | `maatwebsite/excel` (v3.1) | Generasi file Excel `.xlsx` matriks presensi bulanan & parser import data murid massal |
| **Database** | MySQL (`presensi2`) | Database relasional dengan indeks pada `student_id`, `date`, `school_year_id`, `status` |
| **Geofencing Engine** | Haversine Formula (PHP Native `GeofenceService`) | Perhitungan akurat jarak meter antara posisi GPS murid vs koordinat sekolah |
| **Task Scheduler** | Linux Crontab (`php artisan schedule:run`) | Menjalankan kalkulasi Alpa harian (23:00 WIB), ucapan ulang tahun (06:00 WIB), dan backup |
| **Web Server** | Nginx + PHP-FPM 8.2 | Web server performa tinggi dengan SSL Let's Encrypt dan pengalihan HTTPS otomatis |

---

## 3. Peta Komponen & File Aplikasi

```
d:/presensi2/
├── app/
│   ├── Console/Commands/
│   │   ├── CalculateDailyAbsences.php      # Perhitungan Alpa harian (23:00 WIB) + Notifikasi
│   │   ├── SendBirthdayGreetings.php       # Ucapan ultah otomatis (06:00 WIB) + Notifikasi
│   │   └── BackupDatabase.php              # Otomasi backup database .sql
│   ├── Enums/
│   │   ├── AttendanceStatus.php            # Hadir, Terlambat, Izin, Sakit, Alpa
│   │   ├── LeaveStatus.php                 # Pending, Approved, Rejected
│   │   ├── NotificationType.php            # LeaveStatus, AbsenceReminder, Birthday, NewLeaveRequest, System
│   │   └── UserRole.php                    # Admin, Student
│   ├── Livewire/
│   │   ├── Admin/
│   │   │   ├── Dashboard.php               # Ringkasan stat + Leaflet.js GPS map pre-encoded JSON
│   │   │   ├── HolidayCalendar.php         # Manajemen libur (Aksi ikon edit ✏️ & hapus 🗑️)
│   │   │   ├── LeaveRequestManagement.php  # Approval izin + Auto-update status Alpa susulan
│   │   │   ├── ReportGenerator.php         # Generator rekap matriks + ekspor Excel/PDF
│   │   │   ├── ScheduleManagement.php      # Jam masuk, toleransi, jam pulang, is_school_day
│   │   │   ├── SchoolLocationManagement.php# Koordinat GPS & radius geofence sekolah
│   │   │   ├── SchoolSettings.php          # Pengaturan profil nama, alamat, & kontak sekolah
│   │   │   ├── AttendanceManagement/
│   │   │   │   └── AttendanceTable.php     # Tabel presensi harian + modal foto
│   │   │   ├── ClassRoomManagement.php     # Manajemen kelas & jurusan
│   │   │   └── StudentManagement/
│   │   │       ├── StudentTable.php        # Tabel murid + sorting A-Z/NIS + bulk delete
│   │   │       ├── StudentForm.php         # Tambah/edit data murid
│   │   │       └── StudentImport.php       # Import massal Excel murid
│   │   ├── Student/
│   │   │   ├── Dashboard.php               # Status presensi hari ini + banner ultah Gen Z
│   │   │   ├── AttendanceCheckIn.php       # Check-in GPS + foto selfie
│   │   │   ├── AttendanceCheckOut.php      # Check-out presensi pulang
│   │   │   ├── AttendanceHistory.php       # Kalender titik presensi (sync libur Sabtu-Minggu)
│   │   │   ├── LeaveRequestForm.php        # Form pengajuan izin/sakit + lampiran
│   │   │   └── ProfileEdit.php             # Edit foto & tema profil (Nama dikunci 🔒)
│   │   └── Shared/
│   │       └── NotificationCenter.php      # Lonceng notifikasi realtime murid & admin
│   ├── Models/
│   │   ├── Attendance.php
│   │   ├── ClassRoom.php
│   │   ├── Holiday.php
│   │   ├── LeaveRequest.php
│   │   ├── Notification.php
│   │   ├── Schedule.php
│   │   ├── SchoolLocation.php
│   │   ├── SchoolYear.php
│   │   ├── Setting.php                     # Pasangan key-value pengaturan aplikasi/sekolah
│   │   ├── Student.php
│   │   └── User.php
│   └── Services/
│       ├── AttendanceStatusService.php     # Evaluasi status Hadir vs Terlambat
│       ├── BirthdayMessageService.php      # Generator ucapan ultah gaya Gen Z/Alpha
│       ├── GeofenceService.php             # Formula Haversine validasi radius GPS
│       ├── ImageCompressionService.php     # Kompresi foto instant WebP/JPEG
│       ├── ReportExportService.php         # Generasi PDF DomPDF lanskap A4
│       └── StudentImportService.php        # Parser & validator file Excel murid
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── admin.blade.php             # Layout admin + responsive drawer
│   │   │   ├── student.blade.php           # Layout murid + mobile bottom nav drawer
│   │   │   └── partials/admin-nav.blade.php
│   │   ├── livewire/
│   │   └── reports/
│   │       ├── attendance-excel.blade.php  # Template render Excel matriks
│   │       └── attendance-pdf.blade.php    # Template render PDF lanskap A4
│   ├── css/app.css                         # CSS Variables & theme tokens
│   └── js/app.js                           # Vite entry
├── routes/
│   ├── web.php                             # Web routes dengan middleware role
│   └── console.php                         # Task scheduler definitions
├── deploy-vps.sh                           # Automasi deployment VPS
├── PRD.md
├── Architecture.md
├── Design.md
├── Rules.md
├── Schema.md
└── TUTORIAL_MURID.md
```

---

## 4. Keamanan & Penanganan Khusus Livewire 3

### 4.1 Penanganan Livewire Blade Extender Parsing Bug
- **Masalah**: Livewire 3 Blade compiler secara agresif menganggap sintaks array JavaScript `[...]` di dalam tag `<script>` atau atribut `x-data` sebagai kode PHP Blade array, yang dapat memicu `ParseError: Unclosed '['`.
- **Solusi Arsitektur**:
  1. Semua data konfigurasi kompleks (seperti data peta `__mapConfig` di Admin Dashboard) di-encode terlebih dahulu di file PHP Class menggunakan `json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)`.
  2. Di file Blade view, data dipassing langsung menggunakan tag raw `{!! $mapConfigJson !!}` tanpa menggunakan directive `@json(...)` atau `@js(...)`.
  3. Kode logika JavaScript murni di dalam Blade dibungkus menggunakan directive `@verbatim ... @endverbatim`.

### 4.2 Proteksi Proxy & SSL HTTPS di VPS
- Di `app/Providers/AppServiceProvider.php` / Middleware bootstrap:
  ```php
  $middleware->trustProxies(at: '*');
  ```
- Nginx FastCGI configuration:
  ```nginx
  fastcgi_param HTTPS on;
  fastcgi_param HTTP_X_FORWARDED_PROTO https;
  ```
- Aset Livewire dipublish secara permanen menggunakan `php artisan livewire:publish --assets` untuk menghindari kegagalan pemuatan JS di lingkungan HTTPS.

### 4.3 Proteksi Data & Validasi Input
- Proteksi nama murid di sisi murid (`ProfileEdit.php`) dengan menghilangkan field nama dari array `$user->update(...)` di server-side.
- Validasi mime-type gambar (`image|max:2048`) dan kompresi instant via `ImageCompressionService`.
- Penggunaan rumus **Haversine** untuk mengukur jarak koordinat GPS secara akurat dan tidak bergantung pada API pihak ketiga yang berbayar.

---

## 5. Deployment Workflow (`deploy-vps.sh`)

Proses deployment pada VPS dilakukan secara otomatis menggunakan skrip `deploy-vps.sh`:

```bash
#!/usr/bin/env bash
set -e

echo "🚀 Starting deployment on VPS..."

cd /var/www/presensi

git pull origin main

composer install --no-dev --optimize-autoloader

php artisan migrate --force

php artisan storage:link || true

php artisan livewire:publish --assets

npm install
npm run build

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment completed successfully!"
```
