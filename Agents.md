# Agents.md
## Panduan untuk AI Coding Agent — Campus Lost & Found Portal

> File ini adalah "briefing" untuk agent AI (Claude Code, Cursor, Copilot, dll.) dan juga rujukan cepat anggota tim. **Baca file ini sebelum mengubah kode.** Dokumen pendukung: `PRD.md` (apa & mengapa), `Architecture.md` (struktur teknis), `Design_System.md` (UI), dan SRS v1.0.

---

## 1. Ringkasan Proyek

Campus Lost & Found Portal: aplikasi web untuk melapor barang hilang (**Lost**), mendata barang temuan (**Found**), dan memverifikasi klaim kepemilikan dengan aman. Barang fisik disimpan dan diserahkan di **pos satpam kampus**.

- **Stack:** Laravel (PHP) · MVC · PostgreSQL di Supabase · Blade + Tailwind **[Asumsi]**
- **Peran:** Tamu, Pengguna, Admin (satpam/petugas pos)
- **Bahasa UI:** Indonesia
- **Navigasi publik:** beranda pengantar di `/`, katalog barang di `/catalog`, dan informasi layanan di `/tentang`, `/panduan`, `/keamanan`, `/privasi`, `/bantuan`, `/ketentuan`
- **Konteks:** tugas kelompok mata kuliah Rekayasa Perangkat Lunak (Kelompok 04, TI 2B), tim 8 orang
- **Identitas institusi:** jangan menyebut aplikasi sebagai portal resmi Politeknik Negeri Lhokseumawe tanpa persetujuan kampus. Jangan mengarang kanal kontak, jam layanan, atau kebijakan pengelolaan data.

## 2. Aturan Emas (Wajib Dipatuhi)

1. **Ikuti MVC + Service.** Controller tipis; logika bisnis di `app/Services`.
2. **Jangan pernah** menampilkan `security_answer` atau bukti kepemilikan kepada selain admin.
3. **Jangan** commit `.env`, kunci, atau kredensial Supabase. Gunakan `.env.example`.
4. **Semua form** memakai `@csrf`; **semua input** divalidasi lewat Form Request.
5. **Jangan** menulis SQL mentah dari input pengguna; gunakan Eloquent/query binding.
6. **Jangan** memakai `{!! !!}` pada data pengguna (XSS). Gunakan `{{ }}`.
7. **Akses dikontrol** dengan middleware (`auth`, `admin`) **dan** Policy untuk objek.
8. **Perubahan skema = migrasi.** Jangan ubah database manual dari dashboard Supabase tanpa migrasi.
9. **Transisi status hanya lewat Service** (jangan `$item->status = ...` di controller/view).
10. **Kode Serah Terima** unik, acak kriptografis, sekali pakai.
11. Jangan menambah dependency besar tanpa alasan; tanyakan dulu.
12. Jangan keluar dari lingkup v1.0 (lihat §3).
13. Referensi UI mengarahkan bahasa visual, bukan konten/fitur produk lain; pertahankan alur dan kebutuhan Lost & Found pada PRD.

## 3. Lingkup

**Dalam lingkup:** katalog + pencarian/filter, lapor Lost/Found, Security Question, klaim + bukti, moderasi, verifikasi klaim, kode serah terima, dashboard, data master, notifikasi.

**Di luar lingkup (jangan dibangun):** aplikasi mobile native, pembayaran, SSO/SIAKAD, GPS/IoT.

## 4. Perintah Umum

```bash
# Setup awal
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Frontend
npm install
npm run dev          # pengembangan
npm run build        # produksi

# Jalankan
php artisan serve

# Kualitas
./vendor/bin/pint            # format PSR-12
php artisan test             # atau ./vendor/bin/pest

# Utilitas
php artisan migrate:fresh --seed   # reset DB LOKAL saja
php artisan route:list
```

> **Peringatan:** jangan jalankan `migrate:fresh` / `db:wipe` pada database Supabase bersama tanpa persetujuan tim.

## 5. Struktur & Penamaan

```
app/Http/Controllers/   (Admin/ untuk controller admin)
app/Http/Requests/      Store*Request, Update*Request
app/Models/             singular, PascalCase (Item, Claim)
app/Services/           ItemService, ClaimService, HandoverService, ...
app/Policies/
app/Enums/              ItemType, ItemStatus, ClaimStatus, UserRole
resources/views/        layouts/, components/, pages/
database/migrations/    timestamp_aksi_tabel
tests/Feature, tests/Unit
```

**Konvensi:**
- Kelas `PascalCase`, method `camelCase`, tabel `snake_case` jamak, kolom `snake_case`.
- Rute bernama (`->name('admin.claims.approve')`) dan dipakai lewat `route()`.
- Kode & komentar teknis boleh bahasa Inggris; **teks yang tampil ke pengguna wajib bahasa Indonesia**.
- Standar **PSR-12** (Laravel Pint).
- Satu tanggung jawab per method; hindari controller > ±100 baris.

## 6. Domain: Istilah & Status

| Istilah | Arti |
|---|---|
| Lost | Laporan barang hilang (pemilik) |
| Found | Laporan barang temuan (penemu) |
| Klaim | Pengajuan hak milik atas barang Found |
| Security Question | Pertanyaan verifikasi buatan penemu |
| Kode Serah Terima | Kode unik setelah klaim disetujui |

**Status Item:** `pending` → `approved` | `rejected` → `claim_in_progress` → `returned`; Lost bisa `closed`.
**Status Klaim:** `pending`, `approved`, `rejected`, `completed`.

Aturan penting:
- Laporan baru selalu `pending`.
- Hanya laporan `approved` tampil di katalog dan bisa diklaim.
- Penolakan laporan **wajib** `rejection_reason`; penolakan klaim wajib `admin_note`.
- Satu pengguna maksimal satu klaim aktif per barang.
- Serah terima: kode valid + klaim `approved` + belum pernah dipakai → item `returned`, `handovers` tercatat.
- Tamu hanya melihat info terbatas; ciri identifikasi barang Found disembunyikan dari publik.

## 7. Alur Kerja Agent

Saat diberi tugas:

1. **Pahami** — rujuk ID kebutuhan SRS/PRD terkait (mis. FR-33, US-27). Bila ambigu, tanya, jangan menebak aturan bisnis.
2. **Rencanakan singkat** — sebutkan file yang akan diubah sebelum menulis kode.
3. **Implementasi kecil & terarah** — ubah seperlunya, jangan refactor besar di luar tugas.
4. **Tulis/ubah tes** untuk perilaku yang berubah.
5. **Jalankan** `pint` dan `php artisan test`; perbaiki yang gagal.
6. **Ringkas** — jelaskan apa yang diubah, mengapa, dan cara mengujinya; sebutkan asumsi.

### Definition of Done per Tugas
- [ ] Sesuai ID kebutuhan yang dirujuk
- [ ] Validasi server-side + pesan error bahasa Indonesia
- [ ] Middleware/Policy terpasang
- [ ] Tes lulus; Pint bersih
- [ ] Tidak ada rahasia/kredensial di diff
- [ ] UI mengikuti `Design_System.md` dan responsif

## 8. Pola Kode yang Dianjurkan

### Controller tipis
```php
public function approve(Claim $claim, ClaimService $service)
{
    $this->authorize('verify', $claim);
    $service->approve($claim, auth()->user());

    return redirect()
        ->route('admin.claims.index')
        ->with('success', 'Klaim disetujui dan kode serah terima telah diterbitkan.');
}
```

### Transisi dalam transaksi
```php
public function complete(string $code, User $admin): Handover
{
    return DB::transaction(function () use ($code, $admin) {
        $claim = Claim::where('handover_code', $code)
            ->lockForUpdate()
            ->firstOrFail();

        abort_unless($claim->status === ClaimStatus::Approved
            && ! $claim->handover()->exists(), 422, 'Kode tidak valid atau sudah digunakan.');

        $handover = $claim->handover()->create([
            'admin_id' => $admin->id,
            'handed_over_at' => now(),
        ]);
        $claim->update(['status' => ClaimStatus::Completed]);
        $claim->item->update(['status' => ItemStatus::Returned]);

        return $handover;
    });
}
```

### Menyembunyikan data sensitif
```php
// app/Models/Item.php
protected $hidden = ['security_answer'];
```

### Validasi unggahan
```php
'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
```

## 9. Pengujian — Kasus Wajib

| Kasus | Hasil yang diharapkan |
|---|---|
| Pengguna biasa buka `/admin` | 403 |
| Tamu buka form lapor | Redirect ke login |
| Tamu/pengguna lain melihat respons item Found | `security_answer` tidak muncul |
| Upload berkas non-gambar sebagai foto | Validasi gagal |
| Klaim kedua aktif pada barang yang sama | Ditolak |
| Tolak laporan tanpa alasan | Validasi gagal |
| Kode serah terima dipakai dua kali | Kedua ditolak |
| Kode salah | Ditolak, tidak ada perubahan status |
| Percobaan login berulang | Terkena rate limit |

## 10. Yang Harus Ditanyakan, Bukan Diputuskan Sendiri

Konfirmasi ke tim/dosen sebelum mengimplementasikan:
- Kebijakan **pengajuan ulang klaim** (batas/jeda)
- **Notifikasi**: in-app saja atau + email
- Penyimpanan berkas: **Supabase Storage vs disk lokal**
- Perubahan skema yang memengaruhi tabel yang dipakai anggota lain
- Penambahan paket Composer/npm baru

## 11. Alur Git

- Branch: `main` (stabil) ← `develop` ← `feature/<modul>-<deskripsi>` (mis. `feature/claim-submit`).
- Commit kecil dan bermakna, format: `feat: ...`, `fix: ...`, `docs: ...`, `refactor: ...`, `test: ...`.
- Pull Request wajib direview minimal 1 anggota; sertakan ID kebutuhan (FR/US) dan langkah uji.
- Jangan push langsung ke `main`.
- Hindari konflik migrasi: koordinasikan sebelum membuat migrasi untuk tabel yang sama.

## 12. Pembagian Modul (Saran untuk Tim 8 Orang) **[Asumsi]**

| Modul | Cakupan | SRS |
|---|---|---|
| Auth & Akun | Registrasi, login, profil, middleware peran | FR-01–06 |
| Katalog | Daftar, pencarian, filter, detail | FR-07–13 |
| Lapor Lost | Form, foto, riwayat | FR-14–17 |
| Lapor Found | Form, Security Question, foto | FR-18–22 |
| Klaim | Pengajuan, bukti, status | FR-23–28 |
| Admin: Moderasi & Master | Moderasi, hapus/arsip, kategori/lokasi | FR-30, 35, 36 |
| Admin: Verifikasi & Serah Terima | Panel klaim, kode, riwayat | FR-31–34, 37 |
| Dashboard, Notifikasi & UI/QA | Statistik, notifikasi, design system, pengujian | FR-29, 38–40 |

## 13. Hal yang Harus Dihindari

- ❌ Logika bisnis di Blade atau controller
- ❌ Menampilkan `security_answer`/bukti di halaman non-admin
- ❌ Hardcode kredensial atau URL produksi
- ❌ Mengubah status item/klaim di luar Service
- ❌ Teks UI berbahasa Inggris untuk pengguna akhir
- ❌ Fitur di luar lingkup v1.0
- ❌ Memperbaiki "sekalian" hal tak terkait dalam PR yang sama
- ❌ Menghapus tes agar build lulus

## 14. Template Prompt untuk Agent

```
Tugas: [deskripsi singkat]
Rujukan: [FR-xx / US-xx dari SRS/PRD]
Batasan: ikuti Agents.md, Architecture.md, Design_System.md.
Output: daftar file yang diubah, kode, tes, dan cara menguji.
Tanyakan dulu bila ada aturan bisnis yang belum jelas.
```

---

*Perbarui file ini bila arsitektur atau keputusan tim berubah, supaya agent dan anggota baru selalu memakai informasi terkini.*
