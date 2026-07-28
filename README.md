<div align="center">

# 🏫 Presensi Digital Sekolah
### *Sistem Kehadiran Digital Modern Berbasis GPS, Swafoto, Gamifikasi Streak, & Laporan Excel Matriks*

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-4E5BA6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4.0-38BDF8?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

</div>

---

## 🌟 Tentang Aplikasi

**Presensi Digital Sekolah** adalah platform pencatatan dan manajemen kehadiran sekolah modern yang dirancang untuk mendukung operasional sekolah secara cepat, akurat, dan efisien. Diperkuat dengan fitur verifikasi lokasi GPS (*Geofencing*) serta pengambilan swafoto (*selfie*) real-time untuk menjamin kejujuran data kehadiran murid.

Dilengkapi dengan gamifikasi **Streak Kehadiran**, leaderboard per kelas, **Pengumuman Sekolah**, peta sebaran GPS murid, notifikasi in-app real-time, rekap laporan matriks lanskap berformat Excel & PDF, dan pengalaman pengguna (*UI/UX*) yang dioptimalkan untuk perangkat smartphone maupun desktop.

---

## ✨ Fitur Unggulan

### 🎓 1. Fitur Sisi Murid
- 📱 **Desain Mobile-First & Tema Dinamis**: Pilihan tema *Light/Dark Mode* interaktif berbasis ikon.
- 🔥 **Streak Kehadiran (Gamifikasi)**:
  - Hitungan hari hadir berturut-turut tanpa Alpa.
  - Progress bar "Target Berikutnya" menuju milestone badge berikutnya.
  - **System Badge Milestone**: 🔥 *On Fire* (5 hr), ⚡ *Konsisten* (10 hr), 🌟 *Rajin Banget* (20 hr), 🏆 *Iron Attendance* (30 hr), 💎 *Legend* (50 hr), 👑 *Hadir Champion* (100 hr).
  - Proteksi izin/sakit disetujui agar streak tidak hangus.
- 📢 **Pengumuman Sekolah di Dashboard**: Card pengumuman sekolah langsung di bawah shortcut dengan preview 2 baris dan modal "Baca Selengkapnya".
- 📍 **Presensi Berbasis GPS & Geofence**: Deteksi lokasi otomatis dengan validasi radius kilometer/meter dari titik lokasi sekolah.
- 📸 **Verifikasi Swafoto (Selfie Real-time)**: Kompresi foto otomatis saat melangsungkan presensi masuk atau pulang.
- 🖼️ **Pop-up Modal Foto Presensi**: Pratinjau foto presensi yang telah diambil langsung di riwayat & dashboard murid.
- 📝 **Pengajuan Izin & Sakit**: Formulir pengajuan izin melampirkan keterangan dan berkas bukti.
- 🔑 **Self-Service Profil & Kontak**: Murid dapat memperbarui Nomor HP/WhatsApp, Alamat Tempat Tinggal, serta mengubah password akun secara mandiri.
- 🥳 **Ucapan Ulang Tahun Gen Z / Alpha Style**: Banner ucapan ulang tahun interaktif dengan 15 variasi pantun, lelucon, doa, dan motivasi + notifikasi otomatis di hari ultah murid.

### 🛡️ 2. Fitur Sisi Administrator Sekolah
- 🏆 **Leaderboard Streak (Global & Per Kelas)**: Menampilkan Top 5 murid paling konsisten di sekolah maupun per kelas.
- 🔄 **Hitung Ulang (Recalculate) Streak Retroaktif**: Perhitungan ulang streak otomatis saat izin disetujui susulan + tombol manual *Recalculate Streak* individual & massal.
- 📱 **Integrasi Kontak Murid & Direct WA**: Kolom No. HP murid di tabel admin yang terhubung langsung ke WhatsApp (`wa.me`) serta detail alamat tempat tinggal.
- 📢 **Manajemen Pengumuman Sekolah**: Pembuatan pengumuman dengan status *Draft* / *Publish Now* + notifikasi in-app massal otomatis ke seluruh murid.
- 🔑 **Reset Password Massal & Individual**: Fitur reset password acak untuk murid secara individual maupun sekaligus (*Bulk Reset Password*) dengan modal hasil yang aman.
- 🗺️ **Peta Sebaran GPS Murid Realtime (Leaflet.js)**: Visualisasi lokasi presensi murid secara realtime di peta interaktif dengan lingkaran radius geofence sekolah & pop-up foto presensi.
- 📊 **Rekap Laporan Matriks Excel & PDF (Lanskap)**: Ekspor rekapitulasi kehadiran bulanan ke berkas `.xlsx` dan `.pdf` dengan penanggalan Bahasa Indonesia penuh (*Hadir, Terlambat, Izin, Sakit, Alpa*).
- ⚙️ **Pengaturan Profil Sekolah**: Kelola nama resmi sekolah, alamat, dan nomor telepon langsung dari panel admin (`/admin/settings`).
- 📥 **Import Murid Massal Cerdas**: Unggah berkas Excel murid dengan *Smart Header Mapping* otomatis + tombol *Download Template (.xlsx)*.
- ☒ **Bulk Selection & Action**: Centang massal untuk reset password atau hapus banyak data murid sekaligus (*Force Delete*).
- 🔤 **Paginasi Global & Sorting Tabel**: Pengurutan tabel interaktif (A-Z, Z-A, NIS, Streak) serta paginasi global berdesain modern.
- 🏫 **Manajemen Jadwal & Jam Kerja**: Pengaturan hari sekolah (Senin-Jumat / Sabtu) serta validasi jam pulang tepat waktu.
- 📅 **Kalender Libur & Tanggal Merah**: Manajemen hari libur nasional & sekolah.

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

Untuk mengaktifkan kalkulasi otomatis murid Alpa harian, recalculate streak harian, dan notifikasi ulang tahun otomatis, tambahkan perintah berikut di VPS Crontab (`crontab -e`):

```bash
* * * * * cd /var/www/presensi && php artisan schedule:run >> /dev/null 2>&1
```

Atau jalankan scheduler secara manual di lokal:
```bash
php artisan schedule:work
```

Perintah individual:
- **Kalkulasi Alpa Otomatis & Recalculate Streak**: `php artisan attendance:calculate-absences`
- **Pengiriman Ucapan Ulang Tahun**: `php artisan birthday:send-greetings`

---

## 📄 Lisensi

Aplikasi ini dilindungi di bawah lisensi [MIT License](LICENSE).
