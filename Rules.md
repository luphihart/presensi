# Rules.md — Coding Convention & Style Guide

**Versi:** 1.0
**Stack:** Laravel + Livewire (murid & admin, satu design system) + MySQL (shared hosting cPanel)

---

## 1. Prinsip Umum

1. **Konsisten lebih penting daripada preferensi personal.** Ikuti aturan di dokumen ini walau ada cara lain yang "lebih disukai".
2. **Explicit lebih baik daripada implicit.** Hindari magic number/string, gunakan konstanta/enum bernama.
3. **Fat model/service, skinny controller.** Logika bisnis tidak boleh hidup di controller atau Livewire component — taruh di `Service` class.
4. **Satu tanggung jawab per class/method** (Single Responsibility).
5. Semua kode baru wajib disertai **type hint** (parameter & return type) — proyek ini menargetkan PHP 8.2+.

---

## 2. Struktur & Penamaan

### 2.1 Penamaan File & Class

| Jenis | Konvensi | Contoh |
|---|---|---|
| Model | Singular, PascalCase | `Student.php`, `LeaveRequest.php` |
| Controller | PascalCase + suffix `Controller` | `AuthController.php` |
| Livewire Component | PascalCase, dikelompokkan per folder role | `Livewire/Student/AttendanceCheckIn.php` |
| Livewire Component (Admin) | PascalCase, dikelompokkan per folder fitur | `Livewire/Admin/StudentManagement/StudentTable.php` |
| Service | PascalCase + suffix `Service` | `GeofenceService.php` |
| Job | PascalCase, kata kerja + objek | `ProcessAttendancePhoto.php` |
| Console Command | PascalCase, deskriptif | `CalculateDailyAbsences.php` |
| Migration | snake_case sesuai konvensi Laravel | `2026_07_25_000001_create_students_table.php` |
| Blade Component | kebab-case | `x-ui.progress-ring` |

### 2.2 Penamaan Database

- Tabel: snake_case, plural (`attendances`, `leave_requests`).
- Kolom: snake_case (`check_in_at`, `is_active`).
- Foreign key: `{singular_table}_id` (`student_id`, `class_room_id`).
- Boolean: awali dengan `is_`/`has_` (`is_active`, `has_attachment`).
- Timestamp: akhiri dengan `_at` (`reviewed_at`, `read_at`).

### 2.3 Penamaan Route

- Route name: `{role}.{resource}.{action}`, contoh: `student.attendance.check-in`, `admin.leave-requests.approve`.
- URL path: kebab-case (`/attendance/check-in`, bukan `/attendanceCheckIn`).

---

## 3. Laravel Convention

### 3.1 Model

- Selalu definisikan `$fillable` (bukan `$guarded = []`) — eksplisit field mana yang boleh diisi massal.
- Relasi didefinisikan dengan return type eksplisit:

```php
public function attendances(): HasMany
{
    return $this->hasMany(Attendance::class);
}
```

- Gunakan **Enum PHP native** (bukan string biasa) untuk kolom status:

```php
enum AttendanceStatus: string
{
    case Hadir = 'hadir';
    case Terlambat = 'terlambat';
    case Izin = 'izin';
    case Sakit = 'sakit';
    case Alpa = 'alpa';
}
```

- Cast kolom enum di model: `protected $casts = ['status' => AttendanceStatus::class];`

### 3.2 Service Layer

- Semua logika bisnis non-trivial (perhitungan geofencing, penentuan status kehadiran, kompresi gambar) wajib berada di `app/Services/`, dipanggil dari Livewire component/controller — bukan ditulis langsung di sana.
- Service class tidak boleh mengetahui detail HTTP (request/response) — hanya menerima data murni (DTO/array/primitive) dan mengembalikan hasil.

Contoh pola:

```php
class GeofenceService
{
    public function isWithinRadius(float $lat, float $lng, SchoolLocation $location): bool
    {
        $distance = $this->calculateDistanceMeters($lat, $lng, $location->latitude, $location->longitude);
        return $distance <= $location->radius_meters;
    }

    private function calculateDistanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        // Haversine formula
    }
}
```

### 3.3 Form Request Validation

- Semua input dari form (bukan hanya Livewire `rules()`) yang melalui controller wajib menggunakan `FormRequest` class terpisah, bukan validasi inline di controller.
- Untuk Livewire, gunakan method `rules()` di dalam component, dengan pesan error berbahasa Indonesia yang jelas.

### 3.4 Job & Queue

- Job yang melibatkan proses berat (kompresi gambar, kirim notifikasi massal) **wajib async** via queue, tidak boleh dijalankan sync dalam request cycle.
- Setiap Job harus idempotent (aman dijalankan ulang tanpa efek samping ganda) — penting untuk job seperti `CalculateDailyAbsences`.

### 3.5 Command (Scheduler)

- Semua scheduled command didaftarkan di `routes/console.php` (Laravel 11+) dengan komentar jelas jadwal & tujuannya:

```php
Schedule::command(CalculateDailyAbsences::class)
    ->dailyAt('23:00')
    ->withoutOverlapping();
```

---

## 4. Livewire Convention

- Satu component = satu tanggung jawab (contoh: `AttendanceCheckIn` hanya urus presensi masuk, bukan digabung dengan riwayat).
- Property public Livewire hanya untuk data yang perlu di-bind ke view — data internal gunakan property protected/private atau computed property.
- Gunakan `#[Validate]` attribute (Livewire 3) untuk validasi properti, bukan validasi manual di setiap method.
- Event antar component menggunakan nama event eksplisit & terdokumentasi: `attendance-submitted`, `leave-request-approved` (kebab-case).
- Hindari query N+1 — selalu eager load relasi yang dipakai di view (`with()`).

---

## 5. Admin Panel Convention (Livewire Custom)

- Panel admin **tidak menggunakan package panel admin pihak ketiga** (mis. Filament) — dibangun sebagai kumpulan Livewire component sendiri, memakai komponen Blade yang sama persis dengan sisi murid (`resources/views/components/ui/`), demi konsistensi visual penuh dengan design system di `Design.md`.
- Satu Livewire component = satu tanggung jawab, sama seperti aturan §4 — hindari satu component raksasa yang menangani tabel + form + modal sekaligus (pisah menjadi component terpisah, mis. `StudentTable.php` vs `StudentForm.php`).
- Tabel data admin (murid, presensi, izin) wajib mendukung minimal: pagination, filter kolom relevan, dan sorting — dibangun sebagai component reusable (`<x-ui.data-table>`) yang dipakai ulang di semua halaman manajemen, bukan diimplementasikan berulang per halaman.
- Aksi sensitif (koreksi presensi, approve/reject izin, ubah jadwal/libur) wajib memicu pencatatan ke `audit_logs` — gunakan Model Observer (bukan ditulis manual di tiap Livewire component) supaya konsisten dan tidak mudah lupa ditambahkan di component baru.
- Import Excel (data murid) dan export Excel/PDF (laporan) menggunakan package generator murni (Laravel Excel, DomPDF) yang dipanggil dari Service class (`StudentImportService`, `ReportExportService`) — bukan logic parsing/generate langsung di Livewire component.
- Layout admin (`layouts/admin.blade.php`) memakai CSS variable & token warna yang sama dengan `layouts/student.blade.php` — dilarang hardcode warna terpisah untuk admin.

---

## 6. Frontend (Blade + Tailwind + Alpine.js)

- Semua elemen UI berulang wajib jadi Blade component di `resources/views/components/ui/`, tidak boleh copy-paste markup Tailwind panjang di banyak file.
- Kelas Tailwind ditulis dengan urutan konsisten: layout → spacing → typography → warna → state (`hover:`, `dark:`) — gunakan Prettier plugin `prettier-plugin-tailwindcss` untuk auto-sort agar tidak perlu didebat manual.
- Warna **tidak boleh hardcode** hex di markup — selalu lewat token Tailwind config yang sudah dipetakan ke CSS variable (lihat `Design.md`).
- Alpine.js hanya untuk interaksi UI murni (toggle, camera preview) — logika yang menyentuh data server tetap lewat Livewire.

---

## 7. Testing

- Setiap Service class wajib punya Unit Test (`tests/Unit/`), terutama `GeofenceService`, `AttendanceStatusService`, `ImageCompressionService`.
- Flow kritikal (presensi, approval izin, perhitungan alpa) wajib punya Feature Test (`tests/Feature/`).
- Job scheduler (`CalculateDailyAbsences`, `SendBirthdayGreetings`) wajib punya test yang memverifikasi idempotency (dijalankan 2x tidak menghasilkan duplikat data).
- Minimum coverage yang disepakati untuk `app/Services/` dan `app/Jobs/`: 80%.

---

## 8. Git & Commit Convention

- Commit message menggunakan **Conventional Commits**:
  - `feat: tambah validasi geofencing saat presensi masuk`
  - `fix: perbaiki duplikasi presensi saat submit ganda`
  - `refactor: pindahkan logika status kehadiran ke service`
  - `docs: update Schema.md untuk tabel notifications`
  - `chore: update dependency Intervention Image`
- Satu branch = satu fitur/fix (`feat/geofence-validation`, `fix/duplicate-attendance`).
- PR wajib direview minimal 1 orang sebelum merge ke `main` (jika tim >1 orang); jika solo developer, gunakan checklist self-review sebelum merge.
- Tidak ada commit langsung ke `main` untuk perubahan fitur — hanya hotfix darurat yang boleh, dan wajib diikuti PR retroaktif untuk dokumentasi.

---

## 9. Environment & Konfigurasi

- Semua nilai yang bisa berubah antar environment (radius default geofence, grace period keterlambatan default, dsb.) disimpan sebagai **setting di database** (tabel `settings` key-value, dikelola lewat halaman "Pengaturan Sistem" di admin) — bukan hardcode di `.env`, karena admin sekolah perlu mengubahnya tanpa deploy ulang.
- `.env` hanya untuk kredensial & konfigurasi infrastruktur (DB, mail, queue driver, app key).
- Tidak ada nilai sensitif (password, API key) yang ditulis langsung di kode maupun migration/seeder yang masuk version control.

---

## 10. Penamaan Bahasa (UI vs Kode)

- **Kode** (variable, function, comment teknis): Bahasa Inggris, konsisten.
- **UI-facing text** (label, pesan error, notifikasi): Bahasa Indonesia, disimpan di file `lang/id/*.php` (Laravel localization) — tidak hardcode string di Blade/Livewire, agar mudah diaudit & diubah konsisten di satu tempat.

Contoh:

```php
// Salah — hardcode di view
<button>Presensi Sekarang</button>

// Benar — lewat file bahasa
<button>{{ __('attendance.check_in_now') }}</button>
```

---

## 11. Performance Guideline

- Query list (tabel presensi, riwayat murid) wajib menggunakan pagination — tidak boleh load seluruh data sekaligus.
- Index database wajib ditambahkan untuk setiap kolom yang dipakai di `WHERE`/`ORDER BY` pada query yang sering dipanggil (lihat `Schema.md` bagian index).
- Proses kompresi gambar & pengiriman notifikasi **wajib** lewat queue (lihat Rules 3.4) — tidak boleh memperlambat response time request presensi.
