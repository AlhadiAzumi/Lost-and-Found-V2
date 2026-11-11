# Design System
## Campus Lost & Found Portal

> Panduan visual dan komponen UI agar tampilan konsisten di seluruh halaman. Diasumsikan frontend memakai **Laravel Blade + Tailwind CSS** **[Asumsi — sesuaikan jika tim memakai stack lain]**. Antarmuka berbahasa Indonesia, responsif, dan mobile-first (SRS §4.1, NFR 5.4).

---

## 1. Prinsip Desain

1. **Jelas dan tepercaya** — aplikasi ini menangani barang berharga; tampilan harus terasa aman dan rapi.
2. **Cepat dipakai** — buat laporan < 5 menit tanpa pelatihan.
3. **Mobile-first** — kebanyakan pengguna membuka dari ponsel (foto dari kamera).
4. **Umpan balik selalu ada** — setiap aksi penting menampilkan sukses/gagal.
5. **Status mudah dipindai** — warna + ikon + teks (jangan hanya warna).

## 2. Warna

> Palet mengambil referensi dari desain **Brainfish AI**: latar langit cyan → mint → biru, **lime** sebagai warna aksi utama, kartu putih dengan **outline hitam tegas** dan **bayangan offset keras** (gaya neo-brutalist yang ramah), serta chip pastel berwarna untuk kategori. Nilai hex diambil dari sampel gambar referensi lalu disesuaikan agar kontras teks tetap lolos WCAG AA.

### 2.1 Palet Dasar
| Token | Hex | Penggunaan |
|---|---|---|
| `ink` | `#111111` | **Teks utama, outline, bayangan offset** |
| `lime-300` | `#BEF264` | Hover ringan, highlight |
| `lime-400` | `#A3E636` | **Tombol utama (CTA), header kartu penting, tab aktif** |
| `lime-500` | `#8EC92F` | Hover/pressed tombol utama |
| `sky-200` | `#B6EFF6` | Latar hero (bagian bawah gradien) |
| `sky-300` | `#8EE6F0` | Latar hero (bagian atas gradien) |
| `mint-200` | `#AFEAD4` | Latar seksi sekunder, ilustrasi |
| `blue-400` | `#64B0EA` | Gradien latar peralihan |
| `blue-600` | `#326BEC` | Tautan, fokus, info, seksi biru (teks putih) |
| `blue-700` | `#2A56C4` | Hover tautan |
| `amber-400` | `#FCB827` | Aksen: sorotan angka, peringatan lembut |
| `amber-200` | `#FCD88A` | Latar label statistik |
| `paper` | `#FFFFFF` | Kartu, form, modal |
| `cloud` | `#F4FBFC` | Latar halaman (putih kebiruan) |
| `gray-100` | `#EFEFEF` | Panel netral |
| `gray-500` | `#6B7280` | Teks sekunder (kontras 4.8:1 di atas putih) |

### 2.2 Gradien Latar
| Nama | Definisi | Pemakaian |
|---|---|---|
| `bg-sky` | `linear-gradient(180deg, #8EE6F0 0%, #B6EFF6 50%, #AFEAD4 100%)` | Hero/Beranda, halaman auth |
| `bg-ocean` | `linear-gradient(180deg, #64B0EA 0%, #326BEC 100%)` | Seksi penegasan/CTA (teks putih) |

Halaman admin memakai latar polos `cloud` agar fokus pada data, bukan dekorasi.

### 2.3 Warna Pastel (Chip & Kategori)
Dipakai untuk chip kategori dan label kecil: latar pastel + border pastel lebih gelap + teks `ink`.

| Token | Latar | Border | Contoh pemakaian |
|---|---|---|---|
| `chip-pink` | `#FCE7F6` | `#F0ABFC` | Kategori Elektronik |
| `chip-yellow` | `#FEFBEC` | `#FDE68A` | Kategori Dokumen/KTM |
| `chip-mint` | `#D1FAE8` | `#6EE7B7` | Kategori Tas |
| `chip-lime` | `#ECFCCB` | `#BEF264` | Kategori Kunci |
| `chip-blue` | `#DBEAFE` | `#93C5FD` | Kategori Dompet |
| `chip-red` | `#FEE2E2` | `#FCA5A5` | Kategori Lainnya / peringatan |
| `chip-orange` | `#FFEDD5` | `#FDBA74` | Kategori Pakaian |
| `chip-purple` | `#EDE9FE` | `#C4B5FD` | Kategori Alat Tulis |

Kategori diberi warna chip secara tetap lewat tabel `categories` (kolom opsional `color`) **[Asumsi]**; ikon + teks tetap wajib agar tidak bergantung pada warna.

### 2.4 Warna Semantik
| Token | Hex | Latar lembut | Penggunaan |
|---|---|---|---|
| `success` | `#16A34A` | `#D1FAE8` | Berhasil, Disetujui, Dikembalikan |
| `warning` | `#D97706` | `#FEF3C7` | Menunggu, perlu perhatian |
| `danger` | `#DC2626` | `#FEE2E2` | Error, Ditolak, aksi destruktif |
| `info` | `#326BEC` | `#DBEAFE` | Informasi, Dalam Proses Klaim |

### 2.5 Warna Tipe Laporan
| Tipe | Warna | Catatan |
|---|---|---|
| **Lost** (Hilang) | latar `chip-red` `#FEE2E2`, teks `ink`, border `#EF4444` | Badge "Hilang" |
| **Found** (Temuan) | latar `lime-400` `#A3E636`, teks `ink`, border `ink` | Badge "Ditemukan" |

### 2.6 Kontras
- Teks `ink` di atas `lime-400`, `sky-*`, `mint-200`, `amber-*`, dan semua chip pastel: **lolos AA** (≥ 4.5:1).
- Teks **putih hanya** di atas `blue-600` atau lebih gelap, dan `ink`.
- **Jangan** memakai teks putih di atas lime atau cyan (kontras tidak cukup).
- Teks bantu `gray-500` hanya di atas putih/`cloud`, jangan di atas gradien.

## 3. Tipografi

- **Font:** `Plus Jakarta Sans` (geometris, ramah; fallback: `Inter, system-ui, -apple-system, "Segoe UI", sans-serif`). Judul besar di hero boleh `font-weight 500` dengan *tracking* sedikit rapat seperti referensi.
- **Aksen serif (opsional, angka statistik):** `Fraunces` / `Georgia`
- **Mono (kode serah terima):** `JetBrains Mono` / `ui-monospace`

| Peran | Ukuran | Berat | Tailwind |
|---|---|---|---|
| H1 – judul halaman | 30 / 36 px | 700 | `text-3xl font-bold` |
| H2 – judul seksi | 24 / 32 px | 600 | `text-2xl font-semibold` |
| H3 – judul kartu | 18 / 28 px | 600 | `text-lg font-semibold` |
| Body | 16 / 24 px | 400 | `text-base` |
| Small / label | 14 / 20 px | 500 | `text-sm font-medium` |
| Caption | 12 / 16 px | 400 | `text-xs` |

Panjang baris teks bacaan maksimal ±70 karakter.

## 4. Spasi, Radius, Bayangan

- **Skala spasi (4 px):** 4, 8, 12, 16, 24, 32, 48, 64 → `1, 2, 3, 4, 6, 8, 12, 16`
- **Radius:** input/tombol `10px` (`rounded-[10px]`), kartu `16px` (`rounded-2xl`), kartu besar/modal `24px` (`rounded-3xl`), badge `9999px` (`rounded-full`)
- **Outline:** semua kartu, tombol, dan input memakai border **`1.5px solid ink`** (ciri khas gaya referensi)
- **Bayangan (offset keras, tanpa blur):** kartu `4px 4px 0 ink`, tombol `2px 2px 0 ink`, modal `6px 6px 0 ink`. Saat hover tombol bergeser `translate(1px,1px)` dan bayangan mengecil; saat ditekan bayangan hilang.
- **Dekorasi (opsional):** awan outline putih di hero, pembatas bergelombang (*wave divider*) antar seksi latar, ikon kecil bertema barang (kunci, dompet). Jangan dipakai di halaman admin.
- **Container:** `max-w-6xl mx-auto px-4 sm:px-6 lg:px-8`

## 5. Breakpoint & Grid

| Nama | Lebar | Layout katalog |
|---|---|---|
| default (mobile) | < 640 px | 1 kolom |
| `sm` | ≥ 640 px | 2 kolom |
| `lg` | ≥ 1024 px | 3 kolom + sidebar filter |
| `xl` | ≥ 1280 px | 4 kolom |

Target sentuh minimal **44 × 44 px**.

## 6. Ikon

Gunakan satu set ikon saja, misalnya **Heroicons** (outline 24 px, stroke 1.5). Ikon selalu disertai label atau `aria-label`.

## 7. Komponen

### 7.1 Tombol
| Varian | Gaya | Pemakaian |
|---|---|---|
| Primary | bg `lime-400`, teks `ink`, border `ink`, bayangan `2px 2px 0 ink` | Aksi utama (Kirim Laporan, Ajukan Klaim, Cari) |
| Secondary | bg putih, teks `ink`, border `ink`, bayangan `2px 2px 0 ink` | Batal, Kembali, Pelajari |
| Info | bg `blue-600`, teks putih, border `ink` | Aksi sekunder penting (Lihat Detail) |
| Danger | bg `danger`, teks putih, border `ink` | Tolak, Hapus |
| Ghost | tanpa border/bayangan, teks `ink` | Aksi tersier di tabel |

Keadaan: hover (bg `lime-500`, bergeser 1px), focus (ring 3 px `blue-600` offset 2 px), pressed (tanpa bayangan), disabled (opacity 50%, tanpa bayangan, `cursor-not-allowed`), loading (spinner + teks "Memproses…").

### 7.2 Form
- Label di atas input, wajib diberi tanda `*`.
- Teks bantu di bawah input (`text-sm text-neutral-500`).
- Pesan error di bawah input, warna `danger`, **berbahasa Indonesia yang jelas** (mis. "Ukuran foto maksimal 2 MB").
- Input error: border `danger` + ikon peringatan.
- Upload foto: area drag-and-drop + pratinjau, info "JPG/PNG/WebP, maks. 2 MB".
- Semua form memuat `@csrf`.

### 7.3 Kartu Barang (Katalog)
```
┌──────────────────────┐
│ [Foto 4:3]  [Badge]  │  ← badge tipe (Hilang/Ditemukan) di pojok foto
├──────────────────────┤
│ Nama barang          │  H3, maks 2 baris
│ 📍 Lokasi            │
│ 🗓 Tanggal           │
│ [Badge status]       │
└──────────────────────┘
```
Gaya kartu: bg putih, border `1.5px ink`, radius `16px`, bayangan `4px 4px 0 ink`; hover naik `translate(-1px,-1px)` dengan bayangan membesar sedikit. Chip kategori memakai warna pastel (§2.3). Tanpa foto → placeholder ikon kategori di atas `gray-100`.

### 7.4 Badge Status
Bentuk pil (`rounded-full`), border `1px` warna semantik, ukuran `text-xs font-medium`, selalu dengan ikon kecil + teks.

| Status | Latar | Border/ikon | Teks |
|---|---|---|---|
| Menunggu Moderasi | `#FEF3C7` | `warning` | Menunggu Moderasi |
| Disetujui | `#D1FAE8` | `success` | Disetujui |
| Ditolak | `#FEE2E2` | `danger` | Ditolak |
| Dalam Proses Klaim | `#DBEAFE` | `info` | Dalam Proses Klaim |
| Dikembalikan | `lime-400` | `ink` | Dikembalikan |
| Ditutup | `gray-100` | `gray-500` | Ditutup |
| Klaim: Menunggu / Disetujui / Ditolak / Selesai | warning-soft / success-soft / danger-soft / lime-400 | sesuai | — |

### 7.5 Navigasi
- **Navbar publik:** ikon logo, Beranda, Katalog, Tentang, Keamanan, Bantuan, dan menu "Lainnya" untuk Panduan, Privasi, serta Ketentuan; di layar ≤1340px seluruh navigasi tersedia lewat menu hamburger.
- **Navbar pengguna:** + Riwayat Saya, Notifikasi (ikon lonceng + penanda belum dibaca), menu profil.
- **Footer publik:** Tentang, Panduan, Keamanan, Privasi, Bantuan & Kontak, Ketentuan Penggunaan, dan situs institusi. Jangan klaim status resmi tanpa persetujuan institusi.
- **Sidebar admin:** Dashboard, Moderasi Laporan, Verifikasi Klaim, Serah Terima, Data Master.

### 7.6 Pencarian & Filter
- Kolom pencarian dengan ikon kaca pembesar di atas katalog.
- **Tab** "Semua | Hilang | Ditemukan".
- Filter: Kategori, Lokasi, Rentang Tanggal. Di mobile dibuka sebagai *bottom sheet/drawer*.
- Chip filter aktif dengan tombol hapus + "Reset filter".

### 7.7 Tabel (Admin)
Header `cloud`, baris zebra halus, aksi di kolom kanan, paginasi di bawah. Di mobile tabel berubah menjadi daftar kartu atau digulir horizontal.

### 7.8 Modal & Konfirmasi
Aksi destruktif/penting (tolak laporan, setujui klaim, serah terima) memakai modal konfirmasi dengan judul jelas, ringkasan aksi, dan kolom alasan bila diperlukan (mis. alasan penolakan **wajib**, FR-30).

### 7.9 Notifikasi / Alert
| Jenis | Gaya |
|---|---|
| Sukses | Toast hijau, otomatis hilang 4 detik |
| Error | Alert merah, tetap tampil sampai ditutup |
| Peringatan | Alert kuning |
| Info | Alert biru |

Gunakan `role="alert"` untuk error dan `aria-live="polite"` untuk toast.

### 7.10 Komponen Khusus Domain

**a) Panel Verifikasi Klaim (Admin)** — tiga kolom berdampingan (satu kolom bertumpuk di mobile):
| Jawaban Pengklaim | Jawaban Benar | Bukti Kepemilikan |
|---|---|---|
Dengan tombol **Setujui** / **Tolak** dan kolom catatan di bawahnya.

**b) Kode Serah Terima** — ditampilkan besar, font mono, jarak antarhuruf lebar, dengan tombol "Salin". Contoh: `LFP-7K4M-X92Q`. Disertai teks: "Tunjukkan kode ini beserta identitas diri ke pos satpam. Kode hanya berlaku sekali."

**c) Kartu Statistik Dashboard** — angka besar + label + ikon: Total Laporan, Lost, Found, Klaim Tertunda, % Dikembalikan.

**d) Timeline Status** — langkah horizontal/vertikal: Menunggu Moderasi → Disetujui → Dalam Proses Klaim → Dikembalikan; langkah aktif berwarna `lime-400` / `blue-600`.

**e) Banner "Info Terbatas"** untuk tamu: "Login untuk melihat detail dan mengajukan klaim."

## 8. State Kosong, Loading, Error

| State | Perlakuan |
|---|---|
| Kosong | Ilustrasi/ikon + teks ("Belum ada laporan yang cocok") + CTA (mis. "Lapor Barang Hilang") |
| Loading | Skeleton kartu / spinner pada tombol |
| Error 403 | "Kamu tidak punya akses ke halaman ini." + tombol kembali |
| Error 404 | "Halaman tidak ditemukan." + tautan ke katalog |
| Error 500 | Pesan ramah, tanpa detail teknis |
| Gagal unggah foto | Jelaskan bahwa laporan **tidak hilang**, tawarkan coba unggah ulang (NFR 5.3) |

## 9. Aksesibilitas

- Semua gambar punya `alt` (nama barang).
- Fokus keyboard terlihat; urutan tab logis.
- Form: `label for`, `aria-describedby` untuk error.
- Jangan menyampaikan status hanya dengan warna.
- Uji pada layar 360 px.

## 10. Tone of Voice (Bahasa Indonesia)

- Sopan, singkat, aktif: "Laporan berhasil dikirim dan menunggu moderasi."
- Hindari jargon teknis pada pesan kesalahan.
- Gunakan "kamu" (santai-sopan) secara konsisten **[Asumsi]**, atau "Anda" bila dosen menginginkan formal — pilih salah satu.

## 11. Konfigurasi Tailwind (Contoh)

```js
// tailwind.config.js
module.exports = {
  content: ["./resources/**/*.blade.php", "./resources/**/*.js"],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Plus Jakarta Sans", "Inter", "system-ui", "sans-serif"],
        serif: ["Fraunces", "Georgia", "serif"],
        mono: ["JetBrains Mono", "ui-monospace", "monospace"],
      },
      colors: {
        ink: "#111111",
        cloud: "#F4FBFC",
        lime:  { 300: "#BEF264", 400: "#A3E636", 500: "#8EC92F" },
        sky:   { 200: "#B6EFF6", 300: "#8EE6F0" },
        mint:  { 200: "#AFEAD4" },
        blue:  { 400: "#64B0EA", 600: "#326BEC", 700: "#2A56C4" },
        amber: { 200: "#FCD88A", 400: "#FCB827" },
        success: "#16A34A",
        warning: "#D97706",
        danger:  "#DC2626",
        info:    "#326BEC",
      },
      backgroundImage: {
        sky:   "linear-gradient(180deg,#8EE6F0 0%,#B6EFF6 50%,#AFEAD4 100%)",
        ocean: "linear-gradient(180deg,#64B0EA 0%,#326BEC 100%)",
      },
      boxShadow: {
        hard:    "2px 2px 0 0 #111111",
        "hard-md": "4px 4px 0 0 #111111",
        "hard-lg": "6px 6px 0 0 #111111",
      },
      borderWidth: { 1.5: "1.5px" },
      borderRadius: { xl2: "16px", xl3: "24px" },
    },
  },
};
```

Contoh kelas:
```html
<!-- Tombol primary -->
<button class="bg-lime-400 text-ink border-[1.5px] border-ink rounded-[10px] shadow-hard
               px-4 py-2 font-semibold hover:bg-lime-500 hover:translate-x-px hover:translate-y-px
               active:shadow-none focus-visible:ring-4 focus-visible:ring-blue-600/40">
  Kirim Laporan
</button>

<!-- Kartu barang -->
<article class="bg-white border-[1.5px] border-ink rounded-2xl shadow-hard-md overflow-hidden">…</article>
```

## 12. Struktur Komponen Blade

```
resources/views/
├── layouts/        app.blade.php, admin.blade.php, guest.blade.php
├── components/
│   ├── button.blade.php
│   ├── input.blade.php
│   ├── badge-status.blade.php
│   ├── item-card.blade.php
│   ├── alert.blade.php
│   ├── modal.blade.php
│   ├── handover-code.blade.php
│   └── stat-card.blade.php
└── pages/ ...
```

Gunakan komponen ini kembali, jangan menyalin markup antarhalaman.

---

## 13. Inventaris Halaman

Prioritas: **MVP** = wajib untuk demo happy path, **+** = tambahan setelah MVP. Peran: T = Tamu, P = Pengguna, A = Admin.

### 13.1 Halaman Publik & Pengguna

| # | Halaman | Rute | Peran | SRS | Prioritas | Komponen utama | State penting |
|---|---|---|---|---|---|---|---|
| 0 | Beranda / Landing | `/` | T, P, A | — | MVP | Hero, CTA, pratinjau laporan, angka ringkas, cara kerja, keamanan | Katalog kosong |
| 1 | Katalog Barang | `/catalog` | T, P, A | FR-07–10, 13 | MVP | Navbar, pencarian, tab Semua/Hilang/Ditemukan, filter, kartu barang, paginasi | Kosong, loading, hasil filter kosong |
| 2 | Detail Barang | `/barang/{item}` | T (terbatas), P, A | FR-11, 12 | MVP | Foto, badge tipe & status, info lokasi/tanggal, tombol Ajukan Klaim, banner info terbatas (tamu) | Tamu vs login, barang sudah dikembalikan, tombol klaim nonaktif bila bukan `Disetujui` |
| 3 | Login | `/login` | T | FR-02, NFR-09 | MVP | Form email + sandi, tautan lupa sandi | Error kredensial, terkena rate limit |
| 4 | Registrasi | `/register` | T | FR-01 | MVP | Form nama, email, telepon, sandi | Error validasi per field |
| 5 | Form Lapor Lost | `/lost/create` | P | FR-14–16 | MVP | Form, upload foto + pratinjau, select kategori/lokasi, date picker | Gagal upload foto, sukses → redirect + toast |
| 6 | Form Lapor Found | `/found/create` | P | FR-18–22 | MVP | Form, Security Question + jawaban, lokasi penitipan, upload foto | Idem, plus validasi Security Question wajib |
| 7 | Form Klaim | `/barang/{item}/claim` | P | FR-23–26 | MVP | Ringkasan barang, Security Question, input jawaban, upload bukti | Sudah punya klaim aktif, barang tidak bisa diklaim |
| 8 | Riwayat Saya | `/my/history` | P | FR-17, 27, 28 | MVP | Tab Laporan Saya / Klaim Saya, badge status, alasan penolakan, Kode Serah Terima | Kosong, klaim ditolak (alasan), klaim disetujui (kode) |
| 9 | Edit / Tutup Laporan | `/my/items/{id}/edit` | P | FR-17 | + | Form edit, tombol Tutup Laporan (modal konfirmasi) | Hanya boleh bila status memungkinkan |
| 10 | Profil & Ganti Sandi | `/profile` | P, A | FR-05 | + | Form profil, form ganti sandi | Sandi lama salah |
| 11 | Notifikasi | `/notifications` | P, A | FR-38–40 | + | Daftar notifikasi, penanda belum dibaca, "Tandai semua dibaca" | Kosong |
| 12 | Lupa / Reset Sandi | `/forgot-password`, `/reset-password` | T | FR-06 | + (Low) | Form email, form sandi baru | Email tidak terdaftar, token kedaluwarsa |
| 21 | Tentang Layanan | `/tentang` | T, P, A | — | + | Tujuan, cakupan, peran petugas | Penjelasan status pengelolaan |
| 22 | Panduan Penggunaan | `/panduan` | T, P, A | — | + | Langkah laporan, klaim, FAQ | Tautan bantuan |
| 23 | Keamanan | `/keamanan` | T, P, A | — | + | Praktik aman, kode serah-terima, data sensitif | Peringatan lampiran bukti |
| 24 | Privasi Data | `/privasi` | T, P, A | — | + | Jenis data, informasi publik, batas kebijakan | Masa simpan menunggu persetujuan |
| 25 | Bantuan & Kontak | `/bantuan` | T, P, A | — | + | Email, telepon, jam layanan, pos satpam, situs PNL | Kanal layanan |
| 26 | Ketentuan Penggunaan | `/ketentuan` | T, P, A | — | + | Aturan penggunaan dan batas pengelolaan | Persetujuan operasional kampus |

### 13.2 Halaman Admin

| # | Halaman | Rute | SRS | Prioritas | Komponen utama | State penting |
|---|---|---|---|---|---|---|
| 13 | Dashboard | `/admin` | FR-29 | MVP | 5 kartu statistik (total, Lost, Found, klaim tertunda, % dikembalikan), daftar antrean terbaru | Data kosong (0) |
| 14 | Moderasi Laporan | `/admin/reports` | FR-30, 35 | MVP | Tabel/daftar, filter status, detail laporan, tombol Setujui/Tolak, modal alasan penolakan, aksi arsip/hapus | Antrean kosong, alasan wajib saat tolak |
| 15 | Verifikasi Klaim | `/admin/claims` + detail | FR-31–33 | MVP | Panel 3 kolom (jawaban pengklaim / jawaban benar / bukti), catatan, Setujui/Tolak | Bukti gagal dimuat, klaim sudah diputuskan |
| 16 | Serah Terima | `/admin/handovers` | FR-34, 37 | MVP | Input Kode Serah Terima, ringkasan klaim & barang, tombol Konfirmasi, tab Riwayat | Kode valid, tidak valid, sudah dipakai |
| 17 | Data Master | `/admin/categories`, `/admin/locations` | FR-36 | + | Tab Kategori / Lokasi, tabel, form tambah/ubah (modal), hapus | Item masih dipakai (tidak bisa dihapus) |

### 13.3 Halaman Sistem

| # | Halaman | Keterangan |
|---|---|---|
| 18 | Error 403 | "Kamu tidak punya akses ke halaman ini." |
| 19 | Error 404 | "Halaman tidak ditemukan." + tautan ke katalog |
| 20 | Error 500 | Pesan ramah tanpa detail teknis |

**Total: 24 halaman fungsional + 3 halaman error.** MVP = 13 halaman (nomor 0–8 dan 13–16).

### 13.4 Peta Navigasi

```
Tamu ─▶ Beranda ─▶ Katalog ─▶ Detail ─▶ (login) ─▶ Form Klaim
   │        └─▶ Login ◀─▶ Registrasi ─▶ Lupa Sandi
   │
Pengguna ─▶ Katalog / Detail
   ├─▶ Lapor Lost ─▶ Riwayat Saya ─▶ Edit/Tutup
   ├─▶ Lapor Found ─▶ Riwayat Saya
   ├─▶ Form Klaim ─▶ Riwayat Saya (status + kode)
   └─▶ Profil · Notifikasi

Admin ─▶ Dashboard
   ├─▶ Moderasi Laporan
   ├─▶ Verifikasi Klaim ─▶ (terbit kode)
   ├─▶ Serah Terima (input kode · riwayat)
   └─▶ Data Master
```

---

## 14. Wireframe (Low-Fidelity)

Tata letak desktop; di mobile semua kolom ditumpuk 1 kolom.

### 14.1 Beranda / Landing

Landing page memakai palet langit-cyan → mint → biru, aksen lime, kartu outline/hard-shadow, dan tipografi display yang ramah seperti referensi. Isi harus tetap relevan untuk Lost & Found kampus; jangan menyalin teks atau fitur produk pada gambar. Sediakan CTA katalog sejak hero dan di bagian bawah agar alur pencarian selalu mudah dijangkau.

```
┌──────────────────────────────────────────────────────────┐
│ [Logo] Beranda  Katalog  Lapor       [Masuk] [Daftar]   │
├──────────────────────────┬───────────────────────────────┤
│ Barang hilang?           │ [Pratinjau laporan terbaru]  │
│ Harapan belum.           │ [chip aman] [status kembali] │
│ [Jelajahi] [Laporkan]    │                               │
├──────────────────────────┴───────────────────────────────┤
│ Statistik ringkas · Laporan terbaru · Kategori           │
├──────────────────────────────────────────────────────────┤
│ Cara kerja 3 langkah                                     │
├──────────────────────────┬───────────────────────────────┤
│ Keamanan & verifikasi    │ Ilustrasi perisai/kode       │
├──────────────────────────┴───────────────────────────────┤
│ CTA penutup                                               │
└──────────────────────────────────────────────────────────┘
```

### 14.2 Katalog Barang (`/catalog`)

```
┌──────────────────────────────────────────────────────────┐
│ [Logo] Katalog   Lapor ▾          🔔  [Nama ▾] / Masuk   │
├──────────────────────────────────────────────────────────┤
│  Temukan barangmu, kembalikan barang orang lain          │
│  [🔍 Cari nama / deskripsi barang...            ] [Cari] │
│  [ Semua ] [ Hilang ] [ Ditemukan ]                      │
├───────────────┬──────────────────────────────────────────┤
│ FILTER        │ 24 hasil          Urutkan: Terbaru ▾     │
│ Kategori ▾    │ ┌────────┐ ┌────────┐ ┌────────┐         │
│ Lokasi ▾      │ │ foto   │ │ foto   │ │ foto   │         │
│ Tanggal       │ │[Hilang]│ │[Temuan]│ │[Hilang]│         │
│ [dari][s/d]   │ │ Dompet │ │ Kunci  │ │ Tas    │         │
│ [Reset]       │ │ 📍 Kantin│ 📍 Lab │ │ 📍 Perpus│        │
│               │ │ 🗓 3 Nov │ │ 🗓 2 Nov│ │ 🗓 1 Nov │       │
│               │ └────────┘ └────────┘ └────────┘         │
│               │        ‹ 1 2 3 4 ›                       │
└───────────────┴──────────────────────────────────────────┘
```

### 14.3 Detail Barang

```
┌──────────────────────────────────────────────────────────┐
│ ← Kembali ke katalog                                     │
├───────────────────────┬──────────────────────────────────┤
│                       │ [Ditemukan] [Disetujui]          │
│     [ FOTO BESAR ]    │ Flashdisk Kingston 32GB          │
│                       │ Kategori : Elektronik            │
│                       │ 📍 Lab Komputer 2                │
│                       │ 🗓 2 Nov 2026                    │
│                       │ Dititipkan di: Pos Satpam        │
│                       │ Deskripsi: ...                   │
│                       │ ┌──────────────────────────────┐ │
│                       │ │ [ Ajukan Klaim ]  (primary)  │ │
│                       │ └──────────────────────────────┘ │
│                       │ ℹ Tamu: "Login untuk mengklaim"  │
└───────────────────────┴──────────────────────────────────┘
```

### 14.3 Form Lapor Found

```
┌──────────────────────────────────────────────────────────┐
│ Lapor Barang Temuan                                      │
│ Nama barang *      [______________________________]      │
│ Kategori *  [Pilih ▾]     Lokasi ditemukan * [Pilih ▾]   │
│ Tanggal ditemukan * [dd/mm/yyyy]                         │
│ Deskripsi *        [______________________________]      │
│ Lokasi penitipan * [______________________________]      │
│ Foto   ┌──────────────────────────┐                      │
│        │  Tarik foto ke sini      │  JPG/PNG/WebP, 2 MB  │
│        └──────────────────────────┘                      │
│ ─── Verifikasi Kepemilikan ───────────────────────────── │
│ ℹ Buat pertanyaan yang hanya bisa dijawab pemilik asli.  │
│ Security Question * [______________________________]     │
│ Jawaban benar *     [______________________________]     │
│                          [Batal]  [Kirim Laporan]        │
└──────────────────────────────────────────────────────────┘
```

### 14.4 Form Klaim

```
┌──────────────────────────────────────────────────────────┐
│ Ajukan Klaim                                             │
│ ┌────┐ Flashdisk Kingston 32GB · Ditemukan di Lab 2      │
│ │foto│                                                   │
│ └────┘                                                   │
│ Pertanyaan keamanan:                                     │
│ "Apa stiker yang menempel di flashdisk?"                 │
│ Jawabanmu *        [______________________________]      │
│ Bukti kepemilikan * ┌────────────────────────┐           │
│ (foto/nota/dokumen) │  Unggah berkas         │           │
│                     └────────────────────────┘           │
│ ⚠ Klaim palsu dapat ditolak dan dicatat.                 │
│                          [Batal]  [Kirim Klaim]          │
└──────────────────────────────────────────────────────────┘
```

### 14.5 Riwayat Saya (Klaim Disetujui)

```
┌──────────────────────────────────────────────────────────┐
│ Riwayat Saya     [ Laporan Saya ] [ Klaim Saya ]         │
│ ┌──────────────────────────────────────────────────────┐ │
│ │ Flashdisk Kingston 32GB            [Klaim Disetujui] │ │
│ │ Kode Serah Terima:                                   │ │
│ │   LFP-7K4M-X92Q   [Salin]                            │ │
│ │ Bawa kode + identitas diri ke Pos Satpam.            │ │
│ └──────────────────────────────────────────────────────┘ │
│ ┌──────────────────────────────────────────────────────┐ │
│ │ Dompet Hitam                          [Klaim Ditolak]│ │
│ │ Alasan: jawaban tidak sesuai.   [Ajukan Ulang]       │ │
│ └──────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────┘
```

### 14.6 Admin – Dashboard

```
┌─────────┬────────────────────────────────────────────────┐
│ Sidebar │ Dashboard                                      │
│ ▸ Dash  │ ┌────────┐┌────────┐┌────────┐┌────────┐┌─────┐│
│ ▸ Lapor │ │ Total  ││ Lost   ││ Found  ││ Klaim  ││ 38% ││
│ ▸ Klaim │ │  120   ││  70    ││  50    ││ tunda 6││kembali│
│ ▸ Serah │ └────────┘└────────┘└────────┘└────────┘└─────┘│
│ ▸ Master│ Perlu tindakan                                 │
│         │ • 4 laporan menunggu moderasi   [Buka]         │
│         │ • 6 klaim menunggu verifikasi   [Buka]         │
└─────────┴────────────────────────────────────────────────┘
```

### 14.7 Admin – Verifikasi Klaim

```
┌─────────┬────────────────────────────────────────────────┐
│ Sidebar │ Klaim #123 · Flashdisk Kingston 32GB           │
│         │ Pengklaim: Rina (081xxx)    Status: [Menunggu] │
│         │ ┌──────────────┬──────────────┬──────────────┐ │
│         │ │ Jawaban      │ Jawaban      │ Bukti        │ │
│         │ │ pengklaim    │ benar        │ kepemilikan  │ │
│         │ │ "Stiker      │ "Stiker      │ [ foto/nota ]│ │
│         │ │  Naruto"     │  Naruto"     │              │ │
│         │ └──────────────┴──────────────┴──────────────┘ │
│         │ Catatan admin [________________________]      │
│         │            [ Tolak ]  [ Setujui & Terbitkan ]  │
└─────────┴────────────────────────────────────────────────┘
```

### 14.8 Admin – Serah Terima

```
┌─────────┬────────────────────────────────────────────────┐
│ Sidebar │ Serah Terima      [ Input Kode ] [ Riwayat ]   │
│         │ Kode Serah Terima                              │
│         │ [ LFP-____-____ ]            [Cek Kode]        │
│         │ ┌────────────────────────────────────────────┐ │
│         │ │ ✔ Kode valid                               │ │
│         │ │ Barang : Flashdisk Kingston 32GB           │ │
│         │ │ Pemilik: Rina · cocokkan dengan identitas  │ │
│         │ │            [ Konfirmasi Serah Terima ]     │ │
│         │ └────────────────────────────────────────────┘ │
│         │ ✖ Kode tidak valid / sudah dipakai (error)     │
└─────────┴────────────────────────────────────────────────┘
```

### 14.9 Urutan Pengerjaan UI yang Disarankan

1. Layout dasar (`app`, `admin`, `guest`) + komponen: tombol, input, badge, alert, modal
2. Katalog + Detail (paling sering dilihat, jadi bahan demo)
3. Login/Registrasi → Form Lost/Found → Form Klaim → Riwayat Saya
4. Dashboard → Moderasi → Verifikasi Klaim → Serah Terima
5. Halaman tambahan (profil, notifikasi, data master, reset sandi, error)
