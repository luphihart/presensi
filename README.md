<div align="center">

# 🏫 Presensi Digital Sekolah
### *Sistem Kehadiran Digital Modern Berbasis GPS, Swafoto, & Laporan Excel Matriks*

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-4E5BA6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4.0-38BDF8?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

</div>

---

## 🌟 Tentang Aplikasi

**Presensi Digital Sekolah** adalah platform pencatatan dan manajemen kehadiran sekolah modern yang dirancang untuk mendukung operasional sekolah secara cepat, akurat, dan efisien. Diperkuat dengan fitur verifikasi lokasi GPS (*Geofencing*) serta pengambilan swafoto (*selfie*) real-time untuk menjamin kejujuran data kehadiran murid.

Dilengkapi dengan desain antarmuka **Split-Screen Modern 2025**, fitur rekap laporan matriks lanskap berformat Excel, notifikasi otomatis, dan pengalaman pengguna (*UI/UX*) yang dioptimalkan untuk perangkat smartphone maupun desktop.

---

## ✨ Fitur Unggulan

### 🎓 1. Fitur Sisi Murid
- 📱 **Desain Mobile-First & Tema Dinamis**: Pilihan tema *Light/Dark Mode* interaktif berbasis ikon.
- 📍 **Presensi Berbasis GPS & Geofence**: Deteksi lokasi otomatis dengan validasi radius kilometer/meter dari titik lokasi sekolah.
- 📸 **Verifikasi Swafoto (Selfie Real-time)**: Kamera otomatis aktif saat melangsungkan presensi masuk atau pulang.
- 📅 **Kalender Riwayat Kehadiran (Dot Indicators)**: Visualisasi status kehadiran harian dengan panel detail per tanggal.
- 📝 **Pengajuan Izin & Sakit**: Formulir pengajuan izin melampirkan keterangan dan berkas bukti.
- 🥳 **Ucapan Ulang Tahun Otomatis**: Banner ucapan ulang tahun gaya Gen Z / Alpha interaktif + notifikasi otomatis di hari ulang tahun murid.

### 🛡️ 2. Fitur Sisi Administrator Sekolah
- 📊 **Rekap Laporan Matriks Excel (Lanskap)**: Ekspor rekapitulasi kehadiran bulanan ke berkas `.xlsx` dengan rincian status tanggal (*Hadir, Terlambat, Izin, Sakit, Alpa*) & *Sticky Column* nama murid.
- 📥 **Import Murid Massal Cerdas**: Unggah berkas Excel murid dengan *Smart Header Mapping* otomatis + tombol *Download Template (.xlsx)*.
- ☒ **Bulk Selection & Action (Hapus Banyak)**: Fitur centang (*checklist*) massal untuk menghapus banyak data murid sekaligus dengan pembersihan bersih (*Force Delete*).
- 🔤 **Paginasi Global & Sorting Tabel**: Pengurutan tabel interaktif (A-Z, Z-A, NIS) serta paginasi global berdesain modern.
- 🏫 **Pengaturan Sekolah & Lokasi**: Konfigurasi nama sekolah, koordinat GPS, radius presensi, toleransi keterlambatan, dan jam masuk/pulang.
- 📅 **Manajemen Hari Libur**: Kalender blokir presensi otomatis pada tanggal merah atau libur nasional/sekolah.

---

## 🛠️ Teknologi yang Digunakan

- **Framework Core**: [Laravel 12.x](https://laravel.com)
- **Reactivity Engine**: [Livewire 3.x](https://livewire.laravel.com)
- **Styling & Theme**: Vanilla CSS Variables + [Tailwind CSS v4](https://tailwindcss.com)
- **Database Engine**: MySQL / SQLite
- **Excel Processor**: `maatwebsite/excel` (Laravel Excel v3.1)
- **Iconography**: Inline Custom SVG & Emoji System

---

## 🚀 Panduan Instalasi & Penggunaan

### 📋 Prasyarat Sistem
- PHP >= 8.2 (dengan ekstensi `pdo`, `mbstring`, `openssl`, `gd`, `zip`)
- Composer >= 2.x
- Node.js >= 18.x & NPM
- Database MySQL / MariaDB

### 🛠️ Langkah Instalasi

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/luphihart/presensi.git
   cd presensi
   ```

2. **Instal Dependensi PHP & Node.js**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Pengaturan Database (`.env`)**:
   Sesuaikan kredensial database pada berkas `.env`:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=presensi2
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Migrasi & Seeder Database**:
   ```bash
   php artisan migrate --seed
   ```

6. **Build Asset & Jalankan Server Dev**:
   ```bash
   npm run build
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser di `http://127.0.0.1:8000`.

---

## 👤 Akun Login bawaan (Default Credentials)

| Peran (Role) | Email | Password |
|--------------|-------|----------|
| **Administrator** | `admin@sekolah.sch.id` | `password` |
| **Murid (Contoh)** | `budi@sekolah.sch.id` | `password` |

---

## ⏰ Fitur Tugas Otomatis (Cron / Scheduler)

Untuk mengaktifkan kalkulasi otomatis murid Alpa harian dan notifikasi ulang tahun otomatis, jalankan scheduler Laravel:

```bash
php artisan schedule:work
```

Atau jalankan perintah secara manual:
- **Pengiriman Ucapan Ulang Tahun**: `php artisan birthday:send-greetings`
- **Kalkulasi Alpa Otomatis**: `php artisan attendance:calculate-absences`

---

## 📄 Lisensi

Aplikasi ini dilindungi di bawah lisensi [MIT License](LICENSE).
