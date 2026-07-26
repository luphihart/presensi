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

Dilengkapi dengan desain antarmuka **Split-Screen Modern 2025**, fitur peta interaktif lokasi GPS murid, rekap laporan matriks lanskap berformat Excel & PDF, notifikasi otomatis, dan pengalaman pengguna (*UI/UX*) yang dioptimalkan untuk perangkat smartphone maupun desktop.

---

## ✨ Fitur Unggulan

### 🎓 1. Fitur Sisi Murid
- 📱 **Desain Mobile-First & Tema Dinamis**: Pilihan tema *Light/Dark Mode* interaktif berbasis ikon.
- 📍 **Presensi Berbasis GPS & Geofence**: Deteksi lokasi otomatis dengan validasi radius kilometer/meter dari titik lokasi sekolah.
- 📸 **Verifikasi Swafoto (Selfie Real-time)**: Kompresi foto otomatis saat melangsungkan presensi masuk atau pulang.
- 🖼️ **Pop-up Modal Foto Presensi**: Pratinjau foto presensi yang telah diambil langsung di riwayat & dashboard murid.
- 🔒 **Proteksi Profil Murid**: Kolom nama murid dikunci (*read-only*) dengan ikon gembok agar data resmi sekolah tidak dapat diubah sendiri.
- 📅 **Kalender Riwayat Kehadiran Sinkron**: Visualisasi status kehadiran harian berbasis titik (*Dot Indicators*) yang otomatis menyesuaikan libur Sabtu & Minggu sesuai jadwal sekolah.
- 📝 **Pengajuan Izin & Sakit**: Formulir pengajuan izin melampirkan keterangan dan berkas bukti.
- 🥳 **Ucapan Ulang Tahun Otomatis**: Banner ucapan ulang tahun gaya Gen Z / Alpha interaktif + notifikasi otomatis di hari ulang tahun murid.

### 🛡️ 2. Fitur Sisi Administrator Sekolah
- 🗺️ **Peta Sebaran GPS Murid Realtime (Leaflet.js)**: Visualisasi lokasi presensi murid secara realtime di peta interaktif dengan lingkaran radius geofence sekolah & pop-up foto presensi.
- 📊 **Rekap Laporan Matriks Excel & PDF (Lanskap)**: Ekspor rekapitulasi kehadiran bulanan ke berkas `.xlsx` dan `.pdf` dengan penanggalan Bahasa Indonesia penuh (*Hadir, Terlambat, Izin, Sakit, Alpa*).
- ⚙️ **Pengaturan Profil Sekolah**: Kelola nama resmi sekolah, alamat, dan nomor telepon langsung dari panel admin (`/admin/settings`).
- 📥 **Import Murid Massal Cerdas**: Unggah berkas Excel murid dengan *Smart Header Mapping* otomatis + tombol *Download Template (.xlsx)*.
- ☒ **Bulk Selection & Action (Hapus Banyak)**: Fitur centang (*checklist*) massal untuk menghapus banyak data murid sekaligus dengan pembersihan bersih (*Force Delete*).
- 🔤 **Paginasi Global & Sorting Tabel**: Pengurutan tabel interaktif (A-Z, Z-A, NIS) serta paginasi global berdesain modern.
- 🏫 **Manajemen Jadwal & Jam Kerja**: Pengaturan hari sekolah (Senin-Jumat / Sabtu) serta validasi jam pulang tepat waktu.
- 📅 **Kalender Libur & Tanggal Merah**: Manajemen hari libur nasional & sekolah lengkap dengan tombol aksi ikon edit ✏️ dan hapus 🗑️.

---

## 🛠️ Teknologi yang Digunakan

- **Framework Core**: [Laravel 12.x](https://laravel.com)
- **Reactivity Engine**: [Livewire 3.x](https://livewire.laravel.com)
- **Styling & Theme**: Vanilla CSS Variables + [Tailwind CSS v4](https://tailwindcss.com)
- **Interactive Map**: [Leaflet.js v1.9](https://leafletjs.com) (OpenStreetMap Tiles)
- **Database Engine**: MySQL / MariaDB / SQLite
- **Excel Processor**: `maatwebsite/excel` (Laravel Excel v3.1)
- **PDF Renderer**: `barryvdh/laravel-dompdf` (DomPDF v3.0)
- **Iconography**: Inline Custom SVG System

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

6. **Publish Asset Livewire & Link Storage**:
   ```bash
   php artisan storage:link
   php artisan livewire:publish --assets
   ```

7. **Build Asset & Jalankan Server Dev**:
   ```bash
   npm run build
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser di `http://127.0.0.1:8000`.

---

## 👤 Akun Login Bawaan (Default Credentials)

| Peran (Role) | Email | Password |
|--------------|-------|----------|
| **Administrator** | `admin@sekolah.sch.id` | `password` |
| **Murid (Contoh)** | `budi@sekolah.sch.id` | `password` |

---

## ⏰ Fitur Tugas Otomatis (Cron / Scheduler)

Untuk mengaktifkan kalkulasi otomatis murid Alpa harian dan notifikasi ulang tahun otomatis, tambahkan perintah berikut di VPS Crontab (`crontab -e`):

```bash
* * * * * cd /var/www/presensi && php artisan schedule:run >> /dev/null 2>&1
```

Atau jalankan scheduler secara manual di lokal:
```bash
php artisan schedule:work
```

Perintah individual:
- **Pengiriman Ucapan Ulang Tahun**: `php artisan birthday:send-greetings`
- **Kalkulasi Alpa Otomatis**: `php artisan attendance:calculate-absences`

---

## 📄 Lisensi

Aplikasi ini dilindungi di bawah lisensi [MIT License](LICENSE).
