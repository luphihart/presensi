# Design.md — UI/UX & Design System Aplikasi Presensi Digital Sekolah

**Versi:** 2.0 (Production Deployed Design System)  
**Status:** Deployed & Production Ready  
**Tanggal Update:** Juli 2026  

---

## 1. Prinsip Desain

1. **Mobile-First & Responsif**: Tampilan murid dioptimalkan untuk perangkat smartphone dengan navigasi bawah (*Bottom Navigation*) dan transisi menjadi sidebar di layar desktop.
2. **Umpan Balik Visual Instan (Instant Visual Feedback)**: Indikator status presensi berbasis warna (Hadir = Hijau, Terlambat = Kuning, Izin/Sakit = Biru Muda, Alpa = Merah) dan toast notification yang informatif.
3. **Peta Interaktif Sebaran GPS Realtime**: Panel Admin dilengkapi peta Leaflet.js dengan ubin OpenStreetMap, lingkaran geofence sekolah, dan penanda murid berwarna dinamis.
4. **Desain Terpadu (Single Token System)**: Seluruh halaman (Admin & Murid) menggunakan variabel CSS yang sama di `resources/css/app.css` untuk mode Terlang (*Light Mode*) dan Mode Gelap (*Dark Mode*).
5. **Aksesibilitas & Keamanan**: Target sentuh minimal 44x44px, label pembaca layar, serta proteksi visibilitas field sensitif (seperti penguncian nama murid dengan ikon 🔒 gembok).

---

## 2. Design System & CSS Variables

### 2.1 CSS Variable Tokens (`resources/css/app.css`)

```css
:root {
  --color-primary: #4F46E5;         /* Indigo 600 */
  --color-primary-soft: #EEF2FF;    /* Indigo 50 */
  --color-bg: #F8FAFC;              /* Slate 50 */
  --color-surface: #FFFFFF;         /* White */
  --color-text: #0F172A;             /* Slate 900 */
  --color-text-muted: #64748B;       /* Slate 500 */
  --color-border: #E2E8F0;           /* Slate 200 */
  
  --color-success: #16A34A;          /* Emerald 600 - Hadir / Disetujui */
  --color-warning: #D97706;          /* Amber 600 - Terlambat / Pending */
  --color-danger: #DC2626;           /* Rose 600 - Alpa / Ditolak / Libur */
  --color-info: #0284C7;             /* Sky 600 - Izin / Sakit */
}

[data-theme="dark"] {
  --color-primary: #6366F1;         /* Indigo 500 */
  --color-primary-soft: #1E1B4B;    /* Indigo 950 */
  --color-bg: #0F172A;              /* Slate 900 */
  --color-surface: #1E293B;         /* Slate 800 */
  --color-text: #F8FAFC;             /* Slate 50 */
  --color-text-muted: #94A3B8;       /* Slate 400 */
  --color-border: #334155;           /* Slate 700 */
}
```

---

## 3. Komponen UI Utama (`resources/views/components/ui/`)

| Komponen | Sintaks | Penggunaan & Varian |
|---|---|---|
| **Button** | `<x-ui.button>` | Varian `primary`, `secondary`, `danger`, `ghost`. Ukuran `sm`, `md`, `lg`. |
| **Card** | `<x-ui.card>` | Kontainer dasar dengan radius `rounded-2xl` dan border `border-[var(--color-border)]`. |
| **Badge** | `<x-ui.badge>` | Badge status kehadiran (`hadir`, `terlambat`, `izin`, `sakit`, `alpa`, `pending`, `approved`, `rejected`). |
| **Stat Card** | `<x-ui.stat-card>` | Kartu angka ringkasan di Dashboard Admin & Murid. |
| **Notification Bell** | `<livewire:shared.notification-center />` | Lonceng notifikasi realtime dengan lencana unread merah. |

---

## 4. UI/UX Flow — Sisi Murid

### 4.1 Navigasi Utama
- **Mobile (< 1024px)**: Bottom Navigation Drawer 5 menu (**Beranda · Presensi · Izin · Riwayat · Profil**).
- **Desktop (≥ 1024px)**: Sidebar kiri dengan logo dan nama sekolah yang diambil dinamis dari `Setting::get('school_name')`.

### 4.2 Kalender Riwayat Presensi (`AttendanceHistory.php`)
- Tampilan matriks tanggal bulanan dengan titik indikator warna per status:
  - 🟢 **Titik Hijau**: Hadir Tepat Waktu
  - 🟡 **Titik Kuning**: Terlambat
  - 🔵 **Titik Biru**: Izin / Sakit
  - 🔴 **Titik Merah**: Alpa / Hari Libur (Sabtu & Minggu yang diatur `is_school_day = false`)
- **Detail Card**: Mengklik salah satu tanggal menampilkan rincian jam masuk, jam pulang, dan tombol pratinjau foto presensi.

### 4.3 Form Edit Profil (`ProfileEdit.php`)
- **Foto Profil**: Pengubahan foto profil dengan kompresi instant.
- **Nama Lengkap Dikunci**: Display nama murid ditampilkan dalam box read-only dengan ikon 🔒 gembok dan teks *"Nama hanya dapat diubah oleh Admin sekolah"*.
- **Pilihan Tema**: Tombol pilihan ☀️ Terang, 🌙 Gelap, atau 💻 Sistem HP.

---

## 5. UI/UX Flow — Sisi Admin

### 5.1 Dashboard Peta Live GPS (`Dashboard.php`)
- Integrasi Peta **Leaflet.js** dengan OpenStreetMap.
- Lingkaran radius geofence sekolah otomatis menggambar batas aman presensi.
- Pin lokasi murid lengkap dengan pop-up info nama, NIS, kelas, jam presensi, jarak GPS, dan thumbnail foto presensi.

### 5.2 Manajemen Kalender Libur (`HolidayCalendar.php`)
- Form Tambah/Edit Hari Libur dengan indikator mode edit `✏️ Edit Hari Libur` dan tombol `Batal`.
- Kolom Aksi dengan **Ikon Pensil ✏️** (Edit) dan **Ikon Tempat Sampah 🗑️** (Hapus).

### 5.3 Pengaturan Sekolah (`SchoolSettings.php`)
- Halaman dedicated di `/admin/settings` untuk mengedit Nama Resmi Sekolah, Alamat, dan Nomor Telepon.
