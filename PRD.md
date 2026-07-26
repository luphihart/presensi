# PRD — Aplikasi Presensi Digital Sekolah

**Versi:** 2.0 (Production Ready)  
**Status:** Deployed & Production Ready  
**Domain:** `https://presensi.sinaumedia.my.id`  
**Tanggal Update:** Juli 2026  

---

## 1. Latar Belakang

Sekolah membutuhkan sistem presensi digital modern yang menggantikan pencatatan manual berbasis kertas. Sistem ini menghadirkan verifikasi kehadiran yang kuat berbasis koordinat GPS (*Geofencing*) serta swafoto (*selfie*) langsung dari kamera, pengelolaan jadwal sekolah harian yang fleksibel, pengajuan izin/sakit susulan yang terintegrasi, serta visualisasi sebaran lokasi presensi murid secara realtime di peta interaktif Admin.

Aplikasi telah teruji dan berjalan stabil di VPS Linux (Ubuntu/Debian) dengan Nginx, PHP 8.2/8.3, MySQL, serta konfigurasi SSL HTTPS yang aman.

---

## 2. Tujuan Utama (Goals)

1. **Verifikasi Kehadiran Akurat**: Mengkombinasikan geofencing GPS dan swafoto langsung untuk menjamin kejujuran presensi murid.
2. **Peta Sebaran GPS Realtime**: Memberikan Admin kemampuan memantau posisi GPS murid saat presensi masuk secara visual di peta Leaflet.js.
3. **Fleksibilitas Pengaturan Sekolah**: Memberikan Admin kontrol penuh atas profil sekolah, lokasi GPS sekolah, jadwal jam kerja per hari, dan kalender hari libur.
4. **Kalkulasi Alpa & Notifikasi Otomatis**: Menjalankan perhitungan Alpa harian (pukul 23:00 WIB) dan pengiriman notifikasi in-app otomatis (ucapan ulang tahun, status izin, dan peringatan Alpa).
5. **Dukungan Izin Susulan**: Memungkinkan persetujuan izin/sakit untuk tanggal yang sudah lewat, yang secara otomatis memperbarui record `Alpa` menjadi `Izin`/`Sakit`.
6. **Laporan & Ekspor Berbahasa Indonesia**: Menghasilkan rekap matriks bulanan Excel (.xlsx) dan PDF (.pdf) dengan penanggalan dan nama hari Bahasa Indonesia yang rapi.
7. **Keamanan Profil Murid**: Mengunci nama murid di sisi murid (*read-only*) agar hanya dapat diubah oleh Admin sekolah.

---

## 3. Target Pengguna & Role

| Role | Deskripsi | Jumlah Pengguna |
|---|---|---|
| **Murid** | Presensi masuk/pulang, ajukan izin/sakit, lihat riwayat kalender presensi, edit foto/tema profil | 200–1.500 murid |
| **Admin** | Kelola data murid, kelas, lokasi GPS, jadwal, hari libur, approval izin, peta live GPS, rekap laporan, pengaturan sekolah | 1–10 administrator |

---

## 4. Scope Fitur Terimplementasi (Full Feature Set)

### 4.1 Autentikasi & Keamanan Profil
- Login multi-role (Murid & Admin) dengan pengalihan otomatis berdasarkan role.
- Proteksi Proxy & HTTPS (`$middleware->trustProxies(at: '*')`).
- **Profil Murid**: Murid dapat memperbarui foto profil dan tema tampilan (*Light / Dark / System*).
- **Proteksi Nama**: Kolom nama lengkap murid **dikunci** (*read-only* dengan ikon 🔒 gembok) dan tidak dapat diubah oleh murid sendiri.

### 4.2 Presensi GPS & Swafoto
- **Check-In & Check-Out**: Presensi 2x/hari dengan validasi jam pulang sesuai jadwal sekolah.
- **Geofence GPS**: Perhitungan jarak Haversine otomatis dari koordinat lokasi sekolah.
- **Kompresi Foto Asinkron/Sinkron**: Kompresi foto presensi secara instant menggunakan `ImageCompressionService` untuk menghemat ruang penyimpanan server.
- **Pop-up Preview Foto Modal**: Pratinjau foto presensi yang diambil di riwayat murid dan tabel presensi admin.

### 4.3 Peta Sebaran GPS Murid Realtime (Admin Dashboard)
- Peta interaktif Leaflet.js dengan ubin OpenStreetMap.
- Lingkaran radius geofence sekolah otomatis disesuaikan dengan `radius_meters`.
- Pin penanda murid berwarna dinamis (🟢 Hadir Tepat Waktu, 🟡 Terlambat, 🏢 Lokasi Sekolah).
- Pop-up info murid (Nama, Kelas, NIS, Jam Presensi, Jarak GPS, dan Pratinjau Foto Presensi).

### 4.4 Manajemen Jadwal & Kalender Libur
- Admin dapat mengatur jam masuk, toleransi keterlambatan, jam pulang, dan status hari sekolah (`is_school_day`) untuk setiap hari (Senin–Minggu).
- Kalender Libur Nasional & Sekolah dengan tombol aksi edit ✏️ dan hapus 🗑️.
- Kalender Riwayat Murid otomatis menyesuaikan hari libur Sabtu & Minggu sesuai pengaturan jadwal sekolah.

### 4.5 Pengajuan Izin & Approvals
- Murid mengajukan izin (Izin/Sakit) dengan memilih tanggal, keterangan, dan berkas lampiran bukti.
- Approval Admin: Pending → Disetujui / Ditolak.
- **Persetujuan Susulan**: Jika Admin menyetujui izin untuk tanggal yang telah berlalu (yang sebelumnya ditandai `Alpa`), sistem otomatis memperbarui statusnya menjadi `Izin` / `Sakit`.

### 4.6 Perhitungan Alpa Otomatis & Cron Scheduler
- Perintah CLI `attendance:calculate-absences` berjalan otomatis setiap pukul 23:00 WIB via Cron Job.
- Menandai murid aktif tanpa presensi dan tanpa izin disetujui pada hari sekolah sebagai `Alpa`.
- Mengirimkan notifikasi in-app `AbsenceReminder` ke akun murid.

### 4.7 Ucapan Ulang Tahun Gen Z / Alpha
- Perintah CLI `birthday:send-greetings` berjalan setiap pukul 06:00 WIB.
- Mengirimkan notifikasi ulang tahun otomatis & menampilkan banner ucapan kasual bergaya Gen Z / Alpha di dashboard murid.

### 4.8 Rekap & Ekspor Laporan (Excel & PDF)
- **Ekspor Excel Matriks (.xlsx)**: Rekap bulanan per tanggal lengkap dengan status `H`, `T`, `I`, `S`, `A`, persentase kehadiran, dan singkatan hari Bahasa Indonesia (`Rab`, `Kam`, `Jum`, `Sab`, `Min`, `Sen`, `Sel`).
- **Ekspor PDF Lanskap (.pdf)**: Laporan lanskap siap cetak berformat A4 dengan penanggalan Bahasa Indonesia.
- **Pengaturan Profil Sekolah (`/admin/settings`)**: Form pengubahan nama sekolah, alamat, dan nomor telepon yang terintegrasi langsung ke header laporan & sidebar murid.

---

## 5. Spesifikasi Teknis & Lingkungan Deployed

- **OS Server**: Linux Ubuntu / Debian (VPS 202.10.48.207)
- **Web Server & Reverse Proxy**: Nginx dengan SSL HTTPS (`presensi.sinaumedia.my.id`)
- **PHP**: PHP 8.2 / 8.3 dengan PHP-FPM (`memory_limit=256M`, `max_execution_time=300`)
- **SWAP Memory**: 2GB SWAP File
- **Database**: MySQL (`presensi2`)
- **Framework & Package Core**: Laravel 12.x, Livewire 3.x, Tailwind CSS v4, Leaflet.js 1.9, DomPDF, Maatwebsite Excel 3.1
- **Deployment Script**: `deploy-vps.sh` untuk deployment otomatis dari branch `main` GitHub.
