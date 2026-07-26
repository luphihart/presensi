# Rules.md — Coding Conventions, Rules, & Style Guide

**Versi:** 2.0 (Production Rules & Conventions)  
**Stack:** Laravel 12.x + Livewire 3.x + Tailwind CSS v4 + MySQL + Nginx (VPS Linux)  

---

## 1. Prinsip Utama Kode & Keamanan

1. **Konsistensi di Atas Preferensi Personal**: Semua developer dan AI assistant wajib mengikuti konvensi proyek ini.
2. **Explicit Over Implicit**: Selalu sebutkan type hints (parameter & return type) serta validasi atribut eksplisit.
3. **Single Responsibility & Thin Components**: Logika kalkulasi (mis. Haversine distance, kompresi gambar, status keterlambatan) wajib ditempatkan di `app/Services/`.
4. **Proteksi Pengubahan Data Sensitif**:
   - Kolom **nama murid** dikunci di sisi murid (`ProfileEdit.php`) dan tidak pernah dimasukkan ke dalam query `$user->update(...)` dari input murid.
   - Perubahan data sensitive oleh Admin (koreksi presensi, approval) tercatat dalam sistem audit/log.

---

## 2. Aturan Khusus Livewire 3 & Blade Engine

### 2.1 Menghindari Livewire Blade Extender Parsing Bug (`ParseError: Unclosed '['`)
Livewire 3 Blade extender dapat keliru menginterpretasikan sintaks array JavaScript `[...]` di dalam skrip atau atribut `x-data` sebagai sintaks PHP array.
- **Aturan Mandatory**:
  1. Dilarang memasukkan array JS kompleks yang diparsing dengan `@json([...])` atau `@js([...])` langsung di dalam tag `<script>` utama yang disentuh Blade compiler.
  2. Data kompleks (seperti data peta Leaflet `__mapConfig`) wajib di-encode di PHP Class (`render()` atau `mount()`) menggunakan `json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)`.
  3. Di file Blade view, data dipassing sebagai string raw: `<script>window.__mapConfig = {!! $mapConfigJson !!};</script>`.
  4. Blok logika JS murni di Blade dapat dibungkus dengan `@verbatim ... @endverbatim`.

---

## 3. Konvensi Penamaan & Struktur

| Jenis | Konvensi | Contoh File |
|---|---|---|
| Model | Singular, PascalCase | `Student.php`, `LeaveRequest.php`, `Setting.php` |
| Livewire Component | PascalCase per Role/Fitur | `App\Livewire\Admin\SchoolSettings.php`, `App\Livewire\Student\AttendanceCheckIn.php` |
| Service | PascalCase + suffix `Service` | `GeofenceService.php`, `ImageCompressionService.php` |
| Command | PascalCase | `CalculateDailyAbsences.php`, `SendBirthdayGreetings.php` |
| Database Table | snake_case, plural | `attendances`, `leave_requests`, `schedules`, `settings` |
| Route Name | `{role}.{resource}.{action}` | `admin.settings.index`, `student.leave.index` |

---

## 4. Aturan Format Penanggalan (Indonesian Locale Mandatory Rule)

- Seluruh penanggalan yang ditampilkan di UI Web, Ekspor Excel (`.xlsx`), Ekspor PDF (`.pdf`), dan Notifikasi **WAJIB** menggunakan Bahasa Indonesia.
- **Aturan Implementasi**:
  1. `config/app.php` disetel `'locale' => 'id'`.
  2. `AppServiceProvider.php` menginisialisasi `Carbon::setLocale('id')` dan `setlocale(LC_TIME, 'id_ID')`.
  3. Di seluruh perenderan string tanggal, sertakan eksplisit `->locale('id')->isoFormat('...')`:
     - Tanggal lengkap: `$date->locale('id')->isoFormat('D MMMM YYYY')` (contoh: `26 Juli 2026`).
     - Nama bulan: `$date->locale('id')->isoFormat('MMMM YYYY')` (contoh: `Juli 2026`).
     - Singkatan hari Excel: `$date->locale('id')->isoFormat('ddd')` (contoh: `Rab`, `Kam`, `Jum`, `Sab`, `Min`, `Sen`, `Sel`).

---

## 5. Aturan Alur Presensi, Izin, & Alpa Otomatis

1. **Aturan Presensi Masuk & Pulang**:
   - Geofence radius dihitung menggunakan rumus **Haversine** (`GeofenceService`). Jika di luar radius, presensi ditolak.
   - Peta Live GPS di Admin Dashboard memperbarui posisi murid secara visual di atas peta Leaflet.js.
   - Presensi pulang ditahan sampai memasuki `check_out_time` pada jadwal sekolah hari tersebut.

2. **Aturan Hari Libur & Sabtu/Minggu**:
   - Hari libur nasional/sekolah yang terdaftar di `holidays` secara otomatis memblokir presensi dan meliburkan siswa.
   - Hari dengan `is_school_day = false` di tabel `schedules` (seperti Sabtu & Minggu) secara otomatis ditandai sebagai hari libur di kalender murid.

3. **Aturan Kalkulasi Alpa & Persetujuan Susulan**:
   - Perintah `attendance:calculate-absences` berjalan setiap pukul 23:00 WIB untuk menandai murid tanpa presensi & tanpa izin sebagai `Alpa`, serta mengirimkan notifikasi in-app `AbsenceReminder`.
   - **Persetujuan Susulan**: Jika Admin menyetujui izin/sakit susulan untuk tanggal yang telah lewat, sistem menggunakan `Attendance::updateOrCreate(...)` untuk secara otomatis mengganti status `Alpa` menjadi `Izin` atau `Sakit`.

---

## 6. Git & Commit Convention

 Commit message wajib mengikuti **Conventional Commits**:
- `feat: tambah halaman pengaturan profil sekolah dan route admin.settings.index`
- `fix: pre-encode map config sebagai JSON string untuk menghindari Blade Extender bug`
- `docs: perbarui PRD.md, Architecture.md, Design.md, Rules.md, dan Schema.md`
