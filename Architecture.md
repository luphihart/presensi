# Architecture.md — Aplikasi Presensi Murid

**Versi:** 1.0

---

## 1. Ringkasan Arsitektur

Aplikasi dibangun sebagai **monolith modular** menggunakan Laravel, dijalankan di **shared hosting berbasis cPanel** (bukan VPS). Pendekatan monolith tetap dipilih karena skala pengguna (200–1.000 murid) tidak membutuhkan microservices — dan pada shared hosting, monolith adalah satu-satunya pilihan realistis karena tidak ada orkestrasi container.

> **Perbedaan penting dari VPS:** shared hosting cPanel umumnya **tidak menyediakan akses root, tidak bisa menjalankan Docker, dan tidak bisa menjalankan proses daemon/worker yang hidup terus-menerus** (mis. `queue:work` sebagai service). Semua proses berjalan (a) langsung di request PHP-FPM/LiteSpeed yang dikelola panel hosting, atau (b) lewat **Cron Jobs** yang disediakan cPanel dengan interval minimum umumnya 1 menit. Arsitektur di bawah disesuaikan dengan batasan ini.

```
┌───────────────────────────────────────────────────────────────┐
│                    Shared Hosting (cPanel)                      │
│                                                                   │
│  ┌───────────────────────────────────────────────────────────┐ │
│  │  Web Server (Apache/LiteSpeed, dikelola panel)              │ │
│  │  Document root diarahkan ke /public_html/public (Laravel)   │ │
│  │  AutoSSL (Let's Encrypt via cPanel) — tanpa konfigurasi manual│ │
│  └───────────────────────────────────────────────────────────┘ │
│                          │                                       │
│                          ▼                                       │
│  ┌───────────────────────────────────────────────────────────┐ │
│  │                     Laravel App (PHP-FPM)                   │ │
│  │   ┌───────────┐        ┌─────────────┐                      │ │
│  │   │ Livewire  │        │  Livewire   │                      │ │
│  │   │ (Murid)   │        │  (Admin)    │                      │ │
│  │   └───────────┘        └─────────────┘                      │ │
│  │        └──────────┬──────────┘                               │ │
│  │           Shared Blade Component Library (Design System)     │ │
│  └───────────────────────────────────────────────────────────┘ │
│                          │                                       │
│         ┌────────────────┼─────────────────┐                    │
│         ▼                ▼                 ▼                    │
│  ┌─────────────┐  ┌──────────────┐  ┌──────────────────────┐   │
│  │   MySQL      │  │ cPanel Cron   │  │  Local Storage        │   │
│  │  (via panel) │  │ (tiap 1 menit:│  │  (foto profil &       │   │
│  │              │  │  schedule:run)│  │  presensi, di dalam    │   │
│  │              │  │               │  │  storage/app/public)   │   │
│  └─────────────┘  └──────────────┘  └──────────────────────┘   │
└───────────────────────────────────────────────────────────────┘
```

---

## 2. Tech Stack

| Layer | Pilihan | Alasan |
|---|---|---|
| Backend Framework | Laravel 11+ | Ekosistem lengkap (auth, queue, validation, storage bawaan), cocok untuk tim kecil, mudah cari developer pengganti |
| Panel Admin | **Livewire 3 custom** (satu stack dengan sisi murid) | Dibangun sendiri memakai komponen Blade yang sama dengan sisi murid, supaya tampilan admin & murid konsisten 100% dengan satu design system — bukan "generic admin panel" dari package pihak ketiga |
| Frontend Murid | Laravel Livewire 3 + Alpine.js | Satu codebase dengan backend, reaktif tanpa perlu API terpisah, cukup untuk kebutuhan MVP |
| Tabel Data (Admin) | Livewire component custom, atau package ringan seperti **Livewire Powergrid** (opsional) sebagai basis tabel yang di-restyle penuh pakai Tailwind + token desain sendiri | Menghindari beban membangun sorting/filtering/pagination dari nol, tanpa terikat tampilan bawaan package |
| Export Excel/PDF | **Laravel Excel (maatwebsite)** untuk Excel, **DomPDF/Browsershot** untuk PDF | Package generator murni (tanpa UI bawaan), sehingga tetap sejalan dengan pendekatan "admin custom" |
| Styling | Tailwind CSS | Utility-first, mudah dikustom sesuai design system |
| Database | **MySQL 8.0+** | Tersedia default di hampir semua paket shared hosting cPanel (via phpMyAdmin), stabil untuk data relasional skala ini |
| Image Processing | Intervention Image v3 (driver GD) | Resize + kompresi + konversi ke WebP, mempertahankan aspect ratio. Driver **GD** dipilih (bukan Imagick) karena GD tersedia default di hampir semua shared hosting, sedangkan ekstensi Imagick sering tidak diaktifkan/tidak tersedia |
| Queue Driver | **Database**, diproses via Cron (`queue:work --stop-when-empty`) | Tidak ada proses daemon persisten di shared hosting; queue "dijalankan sebentar lalu berhenti sendiri" tiap kali cron memicu, bukan worker yang hidup terus |
| Scheduler | Laravel Task Scheduler, dipicu **cPanel Cron Job** (`* * * * * php artisan schedule:run`) | Satu-satunya cara menjalankan job terjadwal di shared hosting — cron cPanel memanggil Laravel scheduler tiap 1 menit |
| Auth | Laravel session-based auth + rate limiter | Standar, aman, tidak perlu kompleksitas token/JWT untuk aplikasi web monolith |
| Web Server | Apache/LiteSpeed (dikelola panel hosting) | Tidak dikonfigurasi manual — cukup arahkan document root ke folder `public/` Laravel, atau gunakan `.htaccess` redirect jika document root tidak bisa diubah |
| SSL | AutoSSL bawaan cPanel (Let's Encrypt) | Aktif otomatis dari panel hosting, tanpa setup manual |
| Deployment | Git deploy cPanel (jika tersedia) atau upload manual via File Manager/FTP + `composer install` lewat SSH terbatas/Terminal cPanel | Tidak ada Docker; deployment bersifat file-based, bukan image-based |
| File Storage | Local disk di dalam `public_html` (via symlink `storage:link`, atau taruh langsung di public jika symlink tidak didukung host) | Tidak ada volume/object storage; struktur folder tetap disiapkan rapi agar mudah dimigrasi ke object storage (S3-compatible) nanti jika pindah ke VPS |
| Geofencing | Haversine formula (PHP native) | Ringan, tidak butuh ekstensi database khusus (PostGIS/MySQL Spatial) yang belum tentu tersedia di shared hosting |

---

## 3. Struktur Folder (Laravel Project)

```
presensi-app/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── CalculateDailyAbsences.php      # job harian hitung alpa
│   │       ├── SendBirthdayGreetings.php       # job harian ucapan ultah
│   │       └── BackupDatabase.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Auth/
│   │   ├── Middleware/
│   │   │   ├── EnsureGeofenceValid.php
│   │   │   └── EnsureIsAdmin.php                 # proteksi route/komponen khusus admin
│   │   └── Requests/                            # form request validation
│   ├── Livewire/
│   │   ├── Student/
│   │   │   ├── Dashboard.php
│   │   │   ├── AttendanceCheckIn.php
│   │   │   ├── AttendanceCheckOut.php
│   │   │   ├── LeaveRequestForm.php
│   │   │   ├── AttendanceHistory.php
│   │   │   └── ProfileEdit.php
│   │   ├── Admin/
│   │   │   ├── Dashboard.php                     # widget ringkasan + grafik tren
│   │   │   ├── StudentManagement/
│   │   │   │   ├── StudentTable.php
│   │   │   │   ├── StudentForm.php
│   │   │   │   └── StudentImport.php              # import massal Excel
│   │   │   ├── AttendanceManagement/
│   │   │   │   ├── AttendanceTable.php
│   │   │   │   └── AttendanceCorrectionModal.php
│   │   │   ├── ScheduleManagement.php             # jam masuk per hari
│   │   │   ├── HolidayCalendar.php                # kalender visual libur/tanggal merah
│   │   │   ├── LeaveRequestManagement.php         # approval izin
│   │   │   └── ReportGenerator.php                # rekap & export
│   │   └── Shared/
│   │       └── NotificationCenter.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── SchoolYear.php
│   │   ├── ClassRoom.php
│   │   ├── Schedule.php
│   │   ├── Holiday.php
│   │   ├── Attendance.php
│   │   ├── LeaveRequest.php
│   │   ├── Notification.php (custom, atau pakai Laravel Notifications)
│   │   └── AuditLog.php
│   ├── Services/
│   │   ├── GeofenceService.php                 # hitung jarak Haversine
│   │   ├── ImageCompressionService.php          # resize+compress+flip
│   │   ├── AttendanceStatusService.php          # tentukan status hadir/telat
│   │   ├── BirthdayMessageService.php           # generate variasi ucapan
│   │   ├── StudentImportService.php             # parsing & validasi import Excel
│   │   └── ReportExportService.php              # generate Excel/PDF rekap
│   ├── Jobs/
│   │   ├── ProcessAttendancePhoto.php           # kompresi foto async
│   │   └── ProcessProfilePhoto.php
│   └── Observers/
│       └── AttendanceObserver.php               # auto-log ke audit_logs
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── resources/
│   ├── views/
│   │   ├── livewire/
│   │   │   ├── student/
│   │   │   └── admin/
│   │   ├── components/                          # Blade component (Design System) — dipakai bersama murid & admin
│   │   │   ├── ui/                               # button, card, badge, table, dll
│   │   │   └── layout/
│   │   └── layouts/
│   │       ├── student.blade.php
│   │       ├── admin.blade.php                   # sidebar admin, sama design token dgn student.blade.php
│   │       └── guest.blade.php
│   ├── css/
│   │   └── app.css                               # Tailwind entry
│   └── js/
│       ├── app.js
│       └── camera-capture.js                     # getUserMedia + flip horizontal
│
├── routes/
│   ├── web.php
│   └── console.php
│
├── storage/
│   └── app/
│       └── public/
│           ├── profile-photos/
│           └── attendance-photos/
│
├── public/                                       # document root diarahkan ke sini
│   └── storage/                                  # symlink ke storage/app/public
│
├── .env.example
└── tests/
    ├── Feature/
    └── Unit/
```

---

## 4. Flow Data Antar Komponen

### 4.1 Flow Presensi Masuk

```
Murid buka halaman Presensi
        │
        ▼
Browser minta izin kamera & lokasi (getUserMedia + Geolocation API)
        │
        ▼
Frontend (Livewire + Alpine.js):
  - Ambil koordinat GPS real-time
  - Tampilkan indikator radius (dalam/luar jangkauan)
  - Capture foto dari kamera, flip horizontal via <canvas> (hilangkan efek mirror)
        │
        ▼
Submit ke server (Livewire component: AttendanceCheckIn)
        │
        ▼
Middleware: EnsureGeofenceValid
  - Hitung jarak (Haversine) antara koordinat murid vs koordinat sekolah
  - Tolak jika di luar radius yang ditentukan admin
        │
        ▼
AttendanceStatusService
  - Bandingkan waktu submit vs jadwal jam masuk hari itu (Schedule)
  - Tentukan status: Hadir / Terlambat
  - Cek apakah sudah ada presensi masuk hari ini (cegah duplikat)
        │
        ▼
Simpan record ke tabel `attendances` (status: pending photo processing)
        │
        ▼
Dispatch Job: ProcessAttendancePhoto (masuk antrian database, diproses oleh
  cron `queue:work --stop-when-empty` yang berjalan tiap 1 menit — lihat
  Architecture.md §6)
  - Resize + kompresi via Intervention Image (driver GD) → WebP
  - Simpan ke storage/app/public/attendance-photos/
  - Update path foto di record attendance
        │
        ▼
Response ke murid: "Presensi berhasil, jam 07:02 (Hadir)"
```

### 4.2 Flow Perhitungan Alpa (Job Terjadwal Harian)

```
Laravel Scheduler (cron, jalan tiap malam pukul 23:00)
        │
        ▼
Command: CalculateDailyAbsences
        │
        ▼
Untuk setiap murid aktif (tahun ajaran berjalan):
  - Cek: apakah hari ini hari libur/tanggal merah? → skip jika ya
  - Cek: apakah ada record attendance (masuk) hari ini? → skip jika ada
  - Cek: apakah ada LeaveRequest berstatus "Disetujui" untuk tanggal ini? → skip jika ada
  - Jika tidak ada semua di atas → buat record attendance dengan status "Alpa"
        │
        ▼
Simpan ke `attendances` + trigger notifikasi in-app ke murid (opsional info)
```

### 4.3 Flow Pengajuan Izin

```
Murid isi form LeaveRequestForm (Livewire)
        │
        ▼
Validasi input (tanggal, jenis, keterangan, lampiran opsional)
        │
        ▼
Simpan ke `leave_requests` (status: Pending)
        │
        ▼
Buat Notification in-app untuk semua admin ("Pengajuan izin baru dari [nama]")
        │
        ▼
Admin buka LeaveRequestManagement (Livewire) → Approve/Reject langsung dari tabel
        │
        ▼
Update status leave_request + catat AuditLog
        │
        ▼
Buat Notification in-app untuk murid ("Izin kamu telah [disetujui/ditolak]")
```

### 4.4 Flow Ucapan Ulang Tahun

```
Scheduler (cron, jalan tiap pagi pukul 06:00)
        │
        ▼
Command: SendBirthdayGreetings
        │
        ▼
Query murid dengan tanggal_lahir (bulan+hari) = hari ini
        │
        ▼
BirthdayMessageService → pilih random dari kumpulan template gaya Gen Z/Alpha
        │
        ▼
Buat Notification in-app + tampilkan banner di Dashboard murid ybs
```

---

## 5. Keamanan

- Rate limiting login (Laravel default `throttle` middleware).
- CSRF protection bawaan Laravel/Livewire.
- Validasi file upload ketat (mime-type, ukuran maksimum) sebelum diproses.
- Semua foto disimpan dengan nama file ter-hash (bukan nama asli) untuk mencegah enumerasi.
- Audit log untuk semua aksi sensitif admin (koreksi presensi, approval izin, ubah jadwal/libur).
- HTTPS wajib (dipaksa via redirect di `.htaccess` + `APP_FORCE_HTTPS`/`URL::forceScheme('https')` di Laravel, karena tidak ada layer reverse proxy seperti Nginx di shared hosting).
- Environment variable (`.env`) tidak pernah masuk ke version control.

---

## 6. Deployment (Shared Hosting cPanel — Ringkasan)

Tidak ada container/orkestrasi. Alur deployment bersifat file-based:

1. **Setup awal**:
   - Buat database MySQL + user via **MySQL Databases** di cPanel.
   - Upload kode aplikasi (via Git Version Control cPanel jika tersedia, atau upload zip lewat File Manager, atau `git clone` via SSH/Terminal cPanel jika diizinkan hosting).
   - Jalankan `composer install --optimize-autoloader --no-dev` lewat **Terminal cPanel** (kebanyakan hosting cPanel modern menyediakan ini meski akses SSH penuh dibatasi).
   - Set document root domain/subdomain ke folder `public/` Laravel (via **Domains** di cPanel). Jika hosting tidak mengizinkan ubah document root, gunakan pendekatan alternatif: taruh isi `public/` di `public_html` langsung dan sesuaikan path di `index.php`.
   - Jalankan `php artisan migrate --force` dan `php artisan storage:link` lewat Terminal cPanel.
   - Setup **Cron Job** di cPanel:
     ```
     * * * * * cd /home/USERNAME/namaapp && php artisan schedule:run >> /dev/null 2>&1
     ```
     Cron inilah yang menjalankan scheduler Laravel (termasuk memicu queue processing, perhitungan alpa harian, ucapan ulang tahun, backup).
2. **Queue processing**: karena tidak ada worker persisten, job antrian diproses lewat scheduled command tambahan yang dipanggil scheduler tiap menit:
   ```php
   Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute()->withoutOverlapping();
   ```
   Ini membuat queue "terasa" hampir real-time (jeda maksimal ~1 menit) tanpa butuh proses daemon.
3. **SSL**: aktifkan **AutoSSL** di cPanel (biasanya otomatis by default untuk domain yang sudah terarah dengan benar).
4. **Backup**: gunakan `mysqldump` terjadwal via scheduler Laravel (bukan `pg_dump`), hasil disimpan di folder di luar `public/` (agar tidak bisa diakses via web), idealnya juga dikonfigurasi untuk memicu fitur **Backup Wizard** bawaan cPanel jika tersedia di paket hosting, supaya ada salinan di luar disk utama.
5. **Update/rollback aplikasi**: karena tidak ada image container untuk rollback cepat, wajib disiplin backup kode (Git) sebelum tiap deployment, dan uji migrasi database di staging/local terlebih dahulu sebelum `migrate --force` di production.

### Keterbatasan yang Perlu Disadari
- **Tidak ada proses background yang benar-benar real-time** — semua bergantung pada interval cron minimal 1 menit dari cPanel. Untuk kebutuhan aplikasi ini (presensi, notifikasi in-app, ucapan ulang tahun), keterlambatan hingga ±1 menit dapat diterima.
- **Resource CPU/RAM dibagi dengan tenant lain** (shared hosting) — proses kompresi gambar sebaiknya tetap ringan (resize ke resolusi wajar) agar tidak terkena limit resource hosting.
- **Ekstensi PHP terbatas** pada apa yang disediakan hosting (pastikan GD, mbstring, fileinfo, pdo_mysql aktif — cek lewat **PHP Selector** di cPanel sebelum development dimulai).
- Jika kelak jumlah murid tumbuh signifikan atau kebutuhan proses background jadi lebih berat, migrasi ke VPS (sudah dirancang di awal, lihat bagian 7) tetap menjadi jalur upgrade yang mudah karena kode aplikasi tidak berubah — hanya lapisan infrastruktur yang berbeda.

---

## 7. Pertimbangan Skalabilitas (Masa Depan)

Jika jumlah murid tumbuh signifikan (>2000), butuh multi-sekolah, atau performa shared hosting mulai jadi bottleneck:
- Migrasi ke VPS dengan Docker — kode Laravel (termasuk panel admin Livewire custom) tetap sama, hanya infrastruktur yang berubah. Karena admin dibangun custom (bukan terikat package panel tertentu), migrasi ini tidak membawa risiko "kehilangan fitur admin" seperti jika sebelumnya terikat ke package pihak ketiga.
- Ganti queue processing dari cron-based menjadi worker persisten dengan Redis sebagai driver.
- Migrasi storage foto ke object storage (MinIO/S3-compatible).
- Pertimbangkan migrasi database dari MySQL ke PostgreSQL jika butuh fitur lanjutan (JSONB, ekstensi geografis) — struktur tabel di `Schema.md` dirancang agar migrasi ini tidak memerlukan perombakan skema besar.
- Jika tabel admin (Livewire Powergrid atau custom) mulai terasa berat untuk fitur kompleks (bulk action rumit, relasi berlapis), evaluasi ulang apakah tetap custom atau adopsi package admin panel — keputusan ini murni trade-off waktu development vs konsistensi desain, sudah didiskusikan di awal proyek.
- Pertimbangkan memisahkan API (Laravel) dari frontend murid (Next.js PWA) jika dibutuhkan pengalaman mobile yang lebih native.
