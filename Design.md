# Design.md — UI/UX & Design System Aplikasi Presensi Murid

**Versi:** 1.0
**Filosofi:** Dua pengalaman berbeda dalam satu sistem — **sisi Murid** terasa personal, ringan, dan cepat (mobile-first); **sisi Admin** terasa efisien dan padat informasi (desktop-first, tetap responsif).

---

## 1. Prinsip Desain

1. **Mobile-first untuk murid.** Mayoritas interaksi murid (presensi 2x/hari) terjadi di HP, sering dalam kondisi terburu-buru (sebelum bel masuk). UI harus cepat dipahami dalam hitungan detik.
2. **Feedback instan.** Setiap aksi penting (presensi, submit izin) harus memberi respons visual jelas — bukan cuma spinner kosong.
3. **Minim friksi, maksimal kejelasan.** Sedikit tap, informasi status selalu terlihat di permukaan (tidak perlu digali).
4. **Konsisten lewat komponen, bukan halaman unik satu-satu.** Semua halaman dibangun dari komponen Blade yang sama di `resources/views/components/ui/`.
5. **Personal tapi tidak lebay.** Sentuhan Gen Z/Alpha (micro-animation, ucapan ulang tahun) hadir di momen tertentu saja — bukan di seluruh UI, supaya tetap terasa kredibel sebagai alat sekolah.

---

## 2. Design System

### 2.1 Warna

Palet dua-warna utama + netral, cukup untuk membedakan status tanpa jadi ramai.

| Token | Hex (contoh) | Penggunaan |
|---|---|---|
| `--color-primary` | `#4F46E5` (Indigo 600) | Aksi utama, tombol primer, elemen brand |
| `--color-primary-soft` | `#EEF2FF` | Background kartu aktif, highlight ringan |
| `--color-success` | `#16A34A` | Hadir, disetujui, dalam radius |
| `--color-warning` | `#D97706` | Terlambat, pending |
| `--color-danger` | `#DC2626` | Alpa, ditolak, di luar radius |
| `--color-info` | `#0891B2` | Izin/sakit, informasi netral |
| `--color-neutral-50` → `--color-neutral-900` | skala abu Tailwind | Teks, border, background |

Semua warna didefinisikan sebagai **CSS variable** di `resources/css/app.css` agar dark mode tinggal swap variable, bukan rewrite komponen.

```css
:root {
  --color-primary: #4F46E5;
  --color-bg: #FFFFFF;
  --color-surface: #F8FAFC;
  --color-text: #0F172A;
  --color-text-muted: #64748B;
  --color-border: #E2E8F0;
}
[data-theme="dark"] {
  --color-bg: #0F172A;
  --color-surface: #1E293B;
  --color-text: #F1F5F9;
  --color-text-muted: #94A3B8;
  --color-border: #334155;
}
```

### 2.2 Tipografi

- Font: **Inter** (UI umum) — netral, sangat terbaca di layar kecil.
- Skala:
  - Display (judul kartu status): `text-2xl` / `text-3xl`, `font-bold`
  - Heading section: `text-lg`, `font-semibold`
  - Body: `text-sm` / `text-base`
  - Caption/meta: `text-xs`, `text-neutral-500`

### 2.3 Spacing & Radius

- Spacing dasar mengikuti skala Tailwind (4px increment).
- Radius besar untuk kesan modern & ramah: kartu `rounded-2xl` (16px), tombol `rounded-xl` (12px), badge/pill `rounded-full`.
- Shadow lembut: `shadow-sm` untuk kartu biasa, `shadow-lg` hanya untuk elemen mengambang (modal, bottom sheet).

### 2.4 Ikonografi

- Gunakan satu set ikon konsisten (mis. Heroicons/Lucide, outline untuk state default, solid untuk state aktif).

### 2.5 Motion

- Durasi transisi singkat: 150–250ms, easing `ease-out`.
- Micro-animation khusus pada:
  - Presensi berhasil → checkmark animasi + confetti ringan (SVG/CSS, tidak berat).
  - Badge status berubah (pending → approved) → fade+scale.
- Tidak ada animasi berlebihan yang memperlambat interaksi.

### 2.6 Dark Mode

- Didukung sejak awal via CSS variable di atas.
- Toggle tersimpan di preferensi user (kolom `theme_preference` di tabel users) + fallback ke `prefers-color-scheme` browser.

---

## 3. Komponen UI (Blade Components)

Lokasi: `resources/views/components/ui/`

| Komponen | Fungsi |
|---|---|
| `<x-ui.button>` | Varian: primary, secondary, ghost, danger. Ukuran: sm/md/lg. |
| `<x-ui.card>` | Container dasar dengan padding & radius konsisten. |
| `<x-ui.badge>` | Status pill (Hadir/Terlambat/Izin/Sakit/Alpa/Pending/Disetujui/Ditolak) — warna otomatis mengikuti status via mapping. |
| `<x-ui.avatar>` | Foto profil dengan fallback inisial nama. |
| `<x-ui.progress-ring>` | Ring persentase kehadiran bulanan (SVG). |
| `<x-ui.bottom-nav>` | Navigasi bawah khusus mobile (murid). |
| `<x-ui.sidebar>` | Navigasi sisi kiri untuk desktop (murid & admin). |
| `<x-ui.empty-state>` | Ilustrasi + copy ramah untuk data kosong. |
| `<x-ui.skeleton>` | Placeholder loading (bukan spinner polos). |
| `<x-ui.toast>` | Notifikasi sesaat (berhasil/gagal aksi). |
| `<x-ui.modal>` / `<x-ui.bottom-sheet>` | Modal di desktop, berubah jadi bottom sheet di mobile. |
| `<x-ui.calendar-heatmap>` | Kalender bulanan dengan warna per status kehadiran. |
| `<x-ui.notification-bell>` | Ikon lonceng + dropdown/list notifikasi in-app. |
| `<x-ui.camera-capture>` | Wrapper Alpine.js untuk `getUserMedia`, live preview, capture, flip horizontal otomatis. |
| `<x-ui.geo-indicator>` | Indikator status radius real-time ("📍 Dalam radius sekolah" / "Di luar radius"). |
| `<x-ui.data-table>` | Tabel data admin: sorting, filter, pagination — dibangun custom (opsional berbasis Livewire Powergrid yang di-restyle penuh), dipakai di semua halaman manajemen data admin. |
| `<x-ui.stat-card>` | Kartu angka ringkasan di dashboard admin (mis. total Hadir/Izin/Alpa hari ini), varian warna sesuai token status. |
| `<x-ui.calendar-editable>` | Kalender bulanan yang bisa diklik untuk menandai hari libur (dipakai admin) — varian interaktif dari `<x-ui.calendar-heatmap>`. |
| `<x-ui.confirm-action>` | Konfirmasi ringan sebelum aksi sensitif (approve/reject izin, koreksi presensi) — dropdown/inline, bukan modal berat. |

---

## 4. UI/UX Flow — Sisi Murid

### 4.1 Navigasi

- **Mobile:** Bottom navigation, 5 item: **Beranda · Presensi · Izin · Riwayat · Profil**.
- **Desktop (≥1024px):** Bottom nav berubah menjadi sidebar kiri, konten utama tetap dalam container maksimal `max-w-2xl` (tidak melebar penuh — menjaga kesan aplikasi personal, bukan dashboard data).

### 4.2 Halaman Beranda (Dashboard Murid)

**Tujuan:** dalam 2 detik, murid tahu apa yang harus dilakukan hari ini.

Urutan komponen dari atas:
1. **Header** — sapaan nama + avatar + ikon lonceng notifikasi.
2. **Banner ulang tahun** (jika berulang tahun hari ini) — kartu penuh warna, copy Gen Z/Alpha, muncul paling atas, dismissable.
3. **Kartu status presensi hari ini** — elemen paling dominan:
   - Belum masuk → "Belum presensi masuk" + tombol besar "Presensi Sekarang" + info jam masuk hari ini.
   - Sudah masuk, belum pulang → "Sudah masuk jam 07:02" + badge status (Hadir/Terlambat) + tombol "Presensi Pulang" (aktif setelah jam tertentu).
   - Sudah lengkap → ringkasan hari ini + centang hijau besar.
4. **Progress ring kehadiran bulan ini** — persentase Hadir, dengan breakdown kecil di bawahnya (Izin: x, Sakit: x, Alpa: x).
5. **Shortcut cepat** — 2 tombol sekunder: "Ajukan Izin" & "Lihat Riwayat".

### 4.3 Halaman Presensi

**Tujuan:** proses tercepat, minim tap, validasi jelas sebelum submit.

Flow:
1. Murid tap "Presensi Sekarang" dari Beranda.
2. Full-screen camera view terbuka (bukan modal kecil) — kamera depan aktif otomatis.
3. **Geo-indicator** muncul persisten di atas viewfinder: hijau "Dalam radius sekolah" / merah "Di luar radius, presensi tidak dapat dilakukan" (tombol capture disabled jika merah).
4. Tombol capture besar bulat di tengah-bawah.
5. Setelah capture: preview foto (sudah di-flip, tidak mirror) + tombol "Kirim" / "Ulangi".
6. Setelah submit: state loading singkat (skeleton, bukan spinner kosong) → transisi ke **Success State**: checkmark animasi + jam presensi + status (Hadir/Terlambat) → auto-redirect ke Beranda setelah 2 detik.

### 4.4 Halaman Izin

Dua tab: **Ajukan Baru** & **Riwayat Pengajuan**.

**Form Ajukan Baru:**
- Pilih tanggal (date picker, tidak bisa pilih tanggal lampau lebih dari batas wajar).
- Pilih jenis: Izin / Sakit (segmented control, bukan dropdown — lebih cepat di mobile).
- Keterangan (textarea).
- Lampiran bukti (opsional, upload foto/dokumen).
- Tombol "Kirim Pengajuan".

**Riwayat Pengajuan:** list card, masing-masing menampilkan tanggal, jenis, badge status (Pending/kuning · Disetujui/hijau · Ditolak/merah), tap untuk detail.

### 4.5 Halaman Riwayat

- **Kalender heatmap bulanan** sebagai tampilan utama (bukan tabel) — warna per hari sesuai status.
- Legend warna di bawah kalender.
- Tap satu tanggal → bottom sheet detail (jam masuk/pulang, foto presensi, status).
- Navigasi ganti bulan dengan swipe/tombol panah.
- Toggle di atas untuk beralih ke tampilan tabel (opsional, untuk yang lebih suka lihat data mentah).

### 4.6 Halaman Profil

- Foto profil besar di atas (tap untuk ganti — otomatis dikompresi & rasio dijaga sebelum diunggah, ada preview crop sebelum submit).
- Form edit data diri (nama, kontak, dll — field yang boleh diedit murid dibatasi sesuai kebijakan admin).
- Toggle dark mode.
- Tombol logout.

### 4.7 Empty & Error States

- Riwayat kosong (murid baru) → ilustrasi ringan + copy: *"Belum ada riwayat presensi. Yuk mulai presensi pertamamu!"*
- Gagal ambil lokasi (GPS off) → panduan singkat cara mengaktifkan GPS, bukan pesan error teknis.
- Gagal akses kamera → panduan izinkan akses kamera di browser.

---

## 5. UI/UX Flow — Sisi Admin (Livewire Custom)

Karena admin dibangun custom (bukan pakai package panel admin), sidebar, topbar, dan komponen tabel dibangun sendiri memakai **komponen Blade yang sama dengan sisi murid** (lihat §3) — ditambah beberapa komponen khusus admin (`<x-ui.data-table>`, `<x-ui.stat-card>`, `<x-ui.calendar-editable>`). Pendekatan ini memastikan admin & murid terasa sebagai satu aplikasi, bukan dua sistem yang ditempel.

### 5.1 Dashboard Admin

Widget di baris atas:
- **Ringkasan hari ini**: total Hadir / Terlambat / Izin / Sakit / Alpa (angka besar + ikon, warna sesuai token status).
- **Grafik tren kehadiran mingguan** (line/bar chart).
- **Pengajuan izin pending** — list ringkas dengan tombol Approve/Reject langsung di row (tanpa buka halaman baru), maksimal 5 ditampilkan + link "Lihat semua".

### 5.2 Manajemen Murid

- Tabel dengan filter: kelas, tahun ajaran, status aktif.
- Aksi massal: import Excel, export Excel/PDF.
- Detail murid menampilkan riwayat kehadiran & foto profil.

### 5.3 Manajemen Presensi

- Tabel dengan filter kuat: tanggal, kelas, status.
- Klik row → detail modal: foto selfie, koordinat presensi (mini-map), jam, status.
- Tombol "Koreksi Manual" pada tiap row (dengan wajib isi alasan koreksi → tercatat di audit log).

### 5.4 Manajemen Jadwal & Kalender Akademik

- **Kalender visual** untuk atur hari libur/tanggal merah (klik tanggal → tandai libur, bukan input form manual satu-satu).
- Form pengaturan jam masuk per hari (Senin–Minggu) dengan preview jadwal mingguan dalam bentuk tabel ringkas.
- Validasi otomatis: warning jika menandai libur pada tanggal yang sudah punya data presensi.

### 5.5 Manajemen Pengajuan Izin

- Tabel dengan filter status.
- Aksi cepat Approve/Reject langsung dari tabel (dengan konfirmasi ringan, bukan modal berat).

### 5.6 Laporan

- Generator rekap (pilih rentang tanggal + kelas) → preview di layar sebelum export ke Excel/PDF.

---

## 6. Responsif — Breakpoint

| Breakpoint | Perilaku |
|---|---|
| `< 640px` (mobile) | Bottom nav, layout single-column, bottom sheet untuk modal |
| `640–1024px` (tablet) | Layout tetap single-column diperlebar, bottom nav tetap dipakai untuk konsistensi |
| `≥ 1024px` (desktop) | Sidebar navigasi, layout container terbatas (murid) / penuh dengan sidebar (admin) |

---

## 7. Gaya Bahasa Ucapan Ulang Tahun

- Disimpan sebagai kumpulan template di `BirthdayMessageService`, dipilih acak agar tidak repetitif tiap tahun.
- Contoh nada (bukan final copy, hanya arah gaya): singkat, penuh emoji relevan, istilah kekinian yang natural (tidak dipaksakan), tetap sopan untuk konteks sekolah.
- Ditampilkan sebagai banner dismissable di Beranda + entri di notification center, tidak mengganggu alur presensi.

---

## 8. Aksesibilitas

- Kontras warna teks vs background minimal memenuhi WCAG AA.
- Semua tombol ikon memiliki `aria-label`.
- Ukuran tap target minimal 44×44px (standar mobile).
- Form memiliki label eksplisit (bukan hanya placeholder).
