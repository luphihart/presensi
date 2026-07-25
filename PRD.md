# PRD — Aplikasi Presensi Murid

**Versi:** 1.0
**Status:** Draft untuk MVP
**Tanggal:** Juli 2026

---

## 1. Latar Belakang

Sekolah membutuhkan sistem presensi digital yang menggantikan pencatatan manual, dengan validasi kehadiran yang kuat (lokasi + foto), pengelolaan jadwal masuk yang fleksibel per hari, serta pengelolaan izin/sakit yang terstruktur. Sistem di-deploy mandiri di **web hosting (shared hosting cPanel) milik sekolah** (bukan SaaS pihak ketiga), sehingga data sepenuhnya dikuasai sekolah dengan biaya infrastruktur yang jauh lebih terjangkau dibanding VPS.

---

## 2. Tujuan (Goals)

1. Menggantikan presensi manual/kertas dengan presensi digital yang tervalidasi (GPS + selfie).
2. Memberi admin kontrol penuh atas jadwal masuk, hari libur, dan tanggal merah — tanpa perlu mengubah kode program.
3. Menyediakan perhitungan otomatis status kehadiran (hadir, terlambat, izin, sakit, alpa) tanpa intervensi manual harian.
4. Memberi murid transparansi atas riwayat dan rekap kehadirannya sendiri.
5. Menjaga sistem tetap ringan dan sederhana — dua role saja (murid & admin), tanpa lapisan role tambahan yang menambah kompleksitas.
6. Menghadirkan pengalaman yang terasa personal dan relevan untuk pengguna muda (murid), salah satunya lewat ucapan ulang tahun otomatis bergaya Gen Z/Alpha.

### Non-Goals (di luar cakupan MVP)
- Tidak ada notifikasi WhatsApp/email ke orang tua.
- Tidak ada role tambahan (guru/wali kelas/wali murid).
- Tidak ada kuota izin/cuti — pengajuan izin tidak dibatasi jumlah harinya.
- Tidak ada aplikasi mobile native (Android/iOS) — cukup web responsif (PWA-ready untuk pengembangan lanjutan).
- Tidak ada integrasi payroll/akademik lain (nilai, jadwal pelajaran, dll).

---

## 3. Target Pengguna

| Role | Deskripsi | Skala |
|---|---|---|
| Murid | Melakukan presensi harian, mengajukan izin, mengelola profil sendiri | 200–1.000 pengguna |
| Admin | Mengelola data murid, jadwal, libur, approval izin, laporan | 1–5 pengguna |

---

## 4. Scope Fitur MVP

### 4.1 Autentikasi & Profil
- Login (murid & admin), lupa password (reset via token).
- Rate limiting pada percobaan login untuk mencegah brute force.
- Murid dapat mengedit profil (nama, kontak, dll) dan foto profil.
- Foto profil dikompresi otomatis oleh sistem (mempertahankan rasio, kualitas dijaga, format dioptimalkan) tanpa mengurangi kejelasan wajah.

### 4.2 Presensi
- Presensi 2x/hari: **masuk** dan **pulang**.
- Validasi **GPS geofencing** — presensi hanya valid jika murid berada dalam radius koordinat sekolah yang ditentukan admin.
- Validasi **selfie langsung** (live capture dari kamera, bukan unggah galeri) — hasil foto **tidak mirror** (di-flip otomatis agar natural).
- Foto presensi dikompresi otomatis, resolusi dioptimalkan untuk kebutuhan bukti visual (bukan resolusi tinggi).
- Sistem mencegah presensi ganda dalam sesi yang sama (masuk/pulang) di hari yang sama.
- Status kehadiran dihitung otomatis: **Hadir**, **Terlambat** (berdasarkan toleransi/grace period), **Izin**, **Sakit**, **Alpa**.
- Admin dapat melakukan koreksi manual atas presensi (dengan audit log siapa & kapan mengubah).

### 4.3 Jadwal & Kalender Akademik
- Admin mengatur jam masuk per hari (Senin–Minggu), dapat berbeda tiap hari.
- Admin mengatur hari libur, tanggal merah, dan libur khusus (bukan istilah "cuti" — untuk murid disebut **hari libur**).
- Sistem terikat pada **tahun ajaran** aktif — data murid, kelas, dan presensi terpisah per tahun ajaran untuk menjaga histori tetap rapi saat kenaikan kelas.
- Validasi: admin mendapat peringatan jika menetapkan hari libur pada tanggal yang sudah memiliki data presensi.

### 4.4 Pengajuan Izin
- Murid mengajukan izin (tidak masuk) atau sakit, dengan tanggal, keterangan, dan lampiran bukti (opsional).
- Tidak ada kuota/batas jumlah hari izin.
- Alur approval oleh admin: Pending → Disetujui/Ditolak.
- Admin mendapat notifikasi in-app saat ada pengajuan baru.
- Murid dapat melihat status pengajuannya secara real-time.

### 4.5 Perhitungan Alpa
- Job terjadwal (harian, otomatis) menandai murid sebagai **Alpa** jika: bukan hari libur, tidak ada presensi masuk, dan tidak ada izin/sakit yang disetujui untuk tanggal tersebut.

### 4.6 Notifikasi (In-App Saja)
- Notification center di dalam aplikasi (bukan WA/email) untuk:
  - Status pengajuan izin (disetujui/ditolak).
  - Reminder belum presensi pulang.
  - Ucapan ulang tahun.
  - (Admin) pengajuan izin baru masuk.

### 4.7 Ucapan Ulang Tahun
- Sistem otomatis mendeteksi murid yang berulang tahun pada hari itu.
- Menampilkan banner/kartu ucapan dengan gaya bahasa kasual ala Gen Z/Gen Alpha (dinamis, tidak template kaku setiap tahun — dapat dirotasi dari beberapa varian teks).

### 4.8 Rekap & Laporan
- Statistik kehadiran per murid, per kelas, per bulan (persentase hadir/izin/sakit/alpa).
- Kalender visual presensi per murid (warna per status).
- Export laporan ke Excel/PDF.
- Import data murid massal via Excel (setup awal tahun ajaran).

### 4.9 Operasional Sistem
- Audit log untuk aksi sensitif (koreksi presensi, approval izin, perubahan jadwal/libur).
- Backup database otomatis terjadwal.

---

## 5. Technical Requirements (Ringkasan)

> Detail lengkap ada di `Architecture.md`.

- Deploy di **shared hosting berbasis cPanel** (tanpa akses root/SSH penuh) — bukan VPS.
- Backend: Laravel (PHP).
- Panel admin: Livewire custom (satu design system dengan sisi murid, tanpa package panel admin pihak ketiga).
- Sisi murid: Laravel Livewire (mobile-first, responsif).
- Database: **MySQL** (disediakan default oleh hosting via cPanel).
- Kompresi gambar: Intervention Image (driver GD), output WebP.
- Web server & SSL: dikelola oleh panel hosting (Apache/LiteSpeed + AutoSSL), tanpa konfigurasi reverse proxy manual.
- Scheduler & Queue: Laravel Scheduler dipicu **cPanel Cron Job** (interval 1 menit); queue diproses via `queue:work --stop-when-empty` yang dipanggil scheduler — tidak ada worker/daemon persisten.
- Autentikasi web berbasis session (Laravel default), dengan rate limiting.
- Deployment bersifat file-based (Git deploy cPanel / upload manual + Composer via Terminal cPanel), tanpa container/Docker.

---

## 6. Success Metrics

| Metrik | Target MVP |
|---|---|
| Waktu rata-rata proses presensi (buka app → selesai) | < 15 detik |
| Tingkat keberhasilan presensi tervalidasi (GPS+selfie) tanpa error | > 95% |
| Akurasi perhitungan alpa otomatis (tanpa koreksi manual) | > 98% |
| Waktu admin memproses satu pengajuan izin | < 30 detik |
| Uptime sistem | > 99% (di luar maintenance terjadwal) |
| Adopsi murid (aktif presensi mandiri dalam 2 minggu pertama) | > 90% |
| Waktu render halaman utama murid (mobile, koneksi 4G) | < 2 detik |

---

## 7. Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| GPS palsu (fake GPS) di Android | Kombinasi radius + selfie wajib; admin dapat meninjau & mengoreksi kejanggalan secara manual. Tidak realistis mencapai deteksi 100% via browser. |
| Penyalahgunaan selfie (foto orang lain) | Live capture langsung dari kamera (tanpa upload galeri) untuk meminimalkan foto lama/hasil edit. |
| Beban server saat jam presensi serentak (pagi hari) | Optimasi query, image processing di queue asinkron, indexing database yang tepat. |
| Kehilangan data | Backup otomatis terjadwal (mysqldump via cron) + audit log, idealnya dikombinasikan dengan fitur Backup Wizard bawaan cPanel. |
| Keterbatasan resource shared hosting (CPU/RAM dibagi tenant lain, tidak ada proses daemon persisten) | Semua job berat (kompresi foto, notifikasi) diproses async lewat cron tiap menit, bukan real-time; query dioptimalkan dengan index sejak awal (lihat `Schema.md`). Jalur upgrade ke VPS sudah disiapkan di `Architecture.md` jika beban tumbuh. |

---

## 8. Roadmap Setelah MVP (Opsional, tidak dikerjakan sekarang)
- Aplikasi mobile native / PWA installable.
- Notifikasi WhatsApp/email ke orang tua.
- Role tambahan (wali kelas).
- Kuota izin dengan kebijakan sekolah.
