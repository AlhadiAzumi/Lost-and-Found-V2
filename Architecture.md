# Architecture
## Campus Lost & Found Portal

> Dokumen arsitektur teknis berdasarkan SRS v1.0 (§2.1, §2.4, §4, §5, §6). Bagian bertanda **[Asumsi]** perlu dikonfirmasi tim.

---

## 1. Gambaran Umum

Aplikasi web monolitik multi-tenant dengan pola **MVC** di atas **Laravel (PHP)**, menyimpan data di **PostgreSQL (Supabase)**. Satu deployment melayani banyak kampus melalui path `/kampus/{slug}`; tenant middleware dan Eloquent scopes membatasi data pada kampus aktif. Akses dibatasi per peran memakai **Laravel Middleware**. Foto dan bukti disimpan di layanan penyimpanan berkas.

```
┌─────────────┐   HTTPS    ┌──────────────────────────────────┐
│  Browser    │ ─────────▶ │  Laravel App (MVC)               │
│ (Desktop/HP)│ ◀───────── │  Routes → Tenant/Auth Middleware → Controller│
└─────────────┘            │  → Service → Model → View (Blade)│
                           └──────┬──────────────┬────────────┘
                                  │ pgsql (SSL)  │ filesystem / S3 API
                          ┌───────▼──────┐  ┌────▼──────────────┐
                          │ Supabase     │  │ File Storage      │
                          │ PostgreSQL   │  │ (Supabase Storage │
                          │ (pooler)     │  │  / Laravel disk)  │
                          └──────────────┘  └───────────────────┘
                                  │
                          ┌───────▼──────┐
                          │ SMTP (opsi)  │  reset sandi & notifikasi email
                          └──────────────┘
```

## 2. Tech Stack

| Lapisan | Teknologi | Catatan |
|---|---|---|
| Backend | Laravel (PHP 8.2+) **[Asumsi versi]** | Routing, Eloquent, middleware, validasi |
| View | Blade + Tailwind CSS **[Asumsi]** | Lihat `Design_System.md` |
| Database | PostgreSQL di Supabase | Driver `pgsql`, lewat connection pooler |
| Penyimpanan | Supabase Storage atau disk Laravel | Foto barang & bukti klaim |
| Email | SMTP (opsional) | Reset sandi, notifikasi |
| Dependency | Composer, npm/Vite | |
| Standar kode | PSR-12 (Laravel Pint) | NFR 5.5 |
| Kontrol versi | Git + GitHub | |

## 3. Arsitektur Lapisan (MVC + Service)

| Lapisan | Tanggung jawab | Lokasi |
|---|---|---|
| **Route** | Memetakan URL ke controller, menempelkan middleware | `routes/web.php` |
| **Middleware** | Autentikasi, cek peran, rate limit | `app/Http/Middleware` |
| **Form Request** | Validasi input server-side | `app/Http/Requests` |
| **Controller** | Menerima request, memanggil service, mengembalikan view/redirect (tipis) | `app/Http/Controllers` |
| **Service** | Logika bisnis (alur klaim, kode serah terima, transisi status) | `app/Services` |
| **Model (Eloquent)** | Representasi tabel + relasi + scope | `app/Models` |
| **Policy** | Otorisasi per objek (mis. hanya pemilik boleh edit) | `app/Policies` |
| **View (Blade)** | Presentasi | `resources/views` |

Aturan: **controller tipis, logika bisnis di Service**, query kompleks di scope Model.

## 4. Struktur Direktori

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── CatalogController.php
│   │   ├── LostReportController.php
│   │   ├── FoundReportController.php
│   │   ├── ClaimController.php
│   │   ├── NotificationController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── ReportModerationController.php
│   │       ├── ClaimVerificationController.php
│   │       ├── HandoverController.php
│   │       └── MasterDataController.php
│   ├── Middleware/
│   │   └── EnsureUserIsAdmin.php
│   └── Requests/
├── Models/  User, Item, Category, Location, Claim, Handover, Notification
├── Policies/ ItemPolicy, ClaimPolicy
├── Services/ ItemService, ClaimService, HandoverService, FileUploadService, NotificationService
├── Enums/    ItemType, ItemStatus, ClaimStatus, UserRole
database/
├── migrations/
├── seeders/
resources/views/ ...
routes/web.php
tests/Feature, tests/Unit
```

## 5. Model Data

### 5.1 ERD

```
campuses 1───* users 1───* items *───1 categories
    │                       │
    ├──────────────* items  │
    │                       1
    ├──────────────* claims *───1 users
    │                   │
    └──────────────* handovers (via claims)

users 1───* activity_logs
items *───* item_matches
```

### 5.2 Skema Tabel

**users**
| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint PK | |
| name | varchar | |
| email | varchar unique | |
| phone | varchar | |
| password | varchar | hash bcrypt/argon2 |
| role | enum(`user`,`admin`) | default `user` |
| created_at / updated_at | timestamp | |

**categories** / **locations**: `id`, `name` (unique), timestamps.

**items**
| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint PK | |
| user_id | FK users | pelapor |
| type | enum(`lost`,`found`) | |
| name | varchar | |
| category_id, location_id | FK | many-to-one |
| description | text | |
| event_date | date | tanggal hilang/ditemukan |
| photo_path | varchar null | |
| status | enum | lihat §5.3 |
| rejection_reason | text null | wajib saat ditolak |
| security_question | text null | hanya `found` |
| security_answer | text null | hanya `found`; **jangan** masuk `$visible`/respons publik |
| storage_location | varchar null | lokasi penitipan (`found`) |
| contact | varchar null | kontak (`lost`) |
| created_at / updated_at | timestamp | |

**claims**
| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint PK | |
| item_id | FK items | |
| user_id | FK users | pengklaim |
| answer | text | jawaban security question |
| proof_path | varchar | bukti kepemilikan |
| status | enum | lihat §5.3 |
| admin_note | text null | |
| handover_code | varchar unique null | diterbitkan saat disetujui |
| verified_by | FK users null | admin |
| created_at / updated_at | timestamp | |

**handovers**: `id`, `claim_id` (unique FK), `admin_id` (FK users), `handed_over_at`.

**notifications**: `id`, `user_id`, `message`, `read_at null`, `created_at`.

### 5.3 Isolasi Multi-Kampus

Tabel `campuses` menyimpan nama, slug portal, dan status aktif. Tabel `users`, `items`, `claims`, dan `handovers` memiliki `campus_id`. Middleware menetapkan konteks dari URL `/kampus/{slug}` sebelum route model binding; global scope menerapkan filter tenant dan pengisian tenant pada record baru. Akun hanya dapat masuk dan menggunakan workflow pada kampusnya, termasuk panel admin. Kategori tetap menjadi data master bersama.

### 5.4 Status (Enum)

| Objek | Nilai |
|---|---|
| Item | `pending`, `approved`, `rejected`, `claim_in_progress`, `returned`, `closed` |
| Claim | `pending`, `approved`, `rejected`, `completed` |

Tampilan bahasa Indonesia dipetakan di enum (`label()`), bukan disimpan sebagai teks di database.

### 5.5 State Machine Item

```
pending ──approve──▶ approved ──claim submitted──▶ claim_in_progress ──handover──▶ returned
   │                    │  ▲                              │
   └──reject──▶ rejected│  └──claim rejected / no pending claim
                        └──(owner closes, type=lost)──▶ closed
```

Transisi dikodekan di `ItemService` (satu tempat) dan dibungkus transaksi database.

### 5.5 Indeks

- `items(status, type, created_at)`
- `items(category_id)`, `items(location_id)`, `items(event_date)`
- `items` pencarian teks: `ILIKE` pada `name`/`description`; opsi lanjutan GIN `to_tsvector('indonesian', ...)` bila perlu
- `claims(item_id, user_id)` — mendukung aturan satu klaim aktif per pengguna per barang (FR-26); pertimbangkan *partial unique index* untuk klaim berstatus `pending`/`approved`
- `claims.handover_code` unique

## 6. Modul & Rute

### 6.1 Rute Publik / Tamu
| Method | URI | Keterangan |
|---|---|---|
| GET | `/` | Katalog (pencarian, filter, tab, paginasi) |
| GET | `/items/{item}` | Detail (info terbatas untuk tamu) |
| GET/POST | `/register`, `/login` | |
| POST | `/logout` | |

### 6.2 Rute Pengguna (`auth`)
| Method | URI | Keterangan |
|---|---|---|
| GET/POST | `/lost/create`, `/found/create` | Form & simpan laporan |
| GET/PUT | `/my/items/{item}/edit` | Ubah laporan sendiri |
| POST | `/my/items/{item}/close` | Tutup laporan Lost |
| GET/POST | `/items/{item}/claim` | Form & ajukan klaim |
| GET | `/my/history` | Riwayat laporan & klaim |
| GET/PUT | `/profile` | Profil & ganti sandi |
| GET | `/notifications` | Notifikasi |

### 6.3 Rute Admin (`auth` + `admin`, prefix `/admin`)
| Method | URI | Keterangan |
|---|---|---|
| GET | `/admin` | Dashboard |
| GET/POST | `/admin/reports`, `/admin/reports/{item}/approve|reject` | Moderasi |
| GET/POST | `/admin/claims`, `/admin/claims/{claim}/approve|reject` | Verifikasi |
| GET/POST | `/admin/handovers` | Input kode & riwayat |
| DELETE | `/admin/items/{item}` | Hapus/arsip |
| resource | `/admin/categories`, `/admin/locations` | Data master |

## 7. Alur Teknis Kritis

### 7.1 Pengajuan Klaim
1. `ClaimController@store` → `StoreClaimRequest` memvalidasi (jawaban wajib, bukti: MIME & ukuran).
2. `ClaimPolicy@create`: item berstatus `approved`, tipe `found`, user bukan pelapor, belum ada klaim aktif.
3. `ClaimService::submit()` dalam **transaksi**: simpan bukti → buat `claims(status=pending)` → ubah item ke `claim_in_progress` → notifikasi admin.

### 7.2 Persetujuan Klaim & Kode Serah Terima
1. Admin membuka panel: jawaban pengklaim, `security_answer`, dan bukti berdampingan (FR-31).
2. `ClaimService::approve()`: buat `handover_code` acak kriptografis (mis. `Str::random` / `random_bytes`, format `LFP-XXXX-XXXX`), cek keunikan, set `verified_by`, notifikasi pengklaim.
3. Bila ditolak: `admin_note` wajib, klaim `rejected`, item kembali `approved` jika tidak ada klaim aktif lain.

### 7.3 Serah Terima
1. Admin memasukkan kode → `HandoverService::complete()` dalam transaksi dengan `lockForUpdate()`.
2. Valid bila klaim `approved` dan belum punya handover → buat `handovers`, klaim `completed`, item `returned`.
3. Kode tidak valid / sudah dipakai → tolak dengan pesan jelas (kode **sekali pakai**, NFR-08).

### 7.4 Unggah Berkas
`FileUploadService`: validasi `mimes:jpg,jpeg,png,webp|max:2048`, nama acak (UUID), simpan di disk privat untuk **bukti klaim** (diakses via rute terautentikasi admin), disk publik untuk foto katalog. Kegagalan unggah → tampilkan pesan jelas tanpa menghapus isian form (NFR 5.3).

## 8. Keamanan

| Ancaman | Mitigasi | SRS |
|---|---|---|
| Penyimpanan sandi | `Hash::make` (bcrypt/argon2) | FR-03, NFR-01 |
| CSRF | `@csrf` di semua form | NFR-02 |
| SQL injection | Eloquent / query binding, tanpa `DB::raw` dari input | NFR-03 |
| XSS | Blade `{{ }}` (escape otomatis); hindari `{!! !!}` | NFR-03 |
| Akses tidak sah | Middleware `auth` + `admin`, Policy per objek | FR-04, NFR-04 |
| Kebocoran jawaban/bukti | `security_answer` di `$hidden`; bukti di disk privat, rute khusus admin | NFR-05 |
| Upload berbahaya | Validasi MIME+ukuran, nama acak, tanpa eksekusi di folder upload | NFR-06 |
| Rahasia bocor | `.env` di `.gitignore`; `.env.example` tanpa nilai asli | NFR-07 |
| Kode serah terima ditebak | Acak kriptografis, unik, sekali pakai | NFR-08 |
| Brute force login | `RateLimiter` / throttle (mis. 5 percobaan/menit) | NFR-09 |
| Transport | HTTPS, koneksi DB ber-SSL | §4.4 |
| IDOR | Policy memeriksa kepemilikan sebelum edit/lihat | — |

## 9. Konfigurasi & Lingkungan

### 9.1 `.env` (contoh, tanpa nilai asli)
```env
APP_NAME="Campus Lost & Found"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=            # host pooler Supabase
DB_PORT=            # port pooler (mis. 6543 / 5432)
DB_DATABASE=postgres
DB_USERNAME=
DB_PASSWORD=
DB_SSLMODE=require

FILESYSTEM_DISK=public
MAIL_MAILER=log     # ganti smtp bila notifikasi email aktif
```

> Catatan: bila memakai pooler Supabase mode transaksi, perhatikan batasan *prepared statements*; uji koneksi lebih awal.

### 9.2 Lingkungan
| Lingkungan | Cara |
|---|---|
| Lokal | `composer install` → `cp .env.example .env` → `php artisan key:generate` → `php artisan migrate --seed` → `php artisan serve` |
| Produksi **[Asumsi]** | VPS/hosting PHP + Composer, `APP_DEBUG=false`, `php artisan config:cache route:cache view:cache`, HTTPS |

## 10. Kinerja & Skalabilitas

- Eager loading (`with('category','location')`) untuk menghindari N+1 di katalog.
- Paginasi (mis. 12 item/halaman), indeks sesuai §5.5.
- Thumbnail/kompresi foto agar halaman katalog ≤ 3 detik.
- Caching ringan untuk data master dan statistik dashboard (`Cache::remember`, TTL singkat).
- Target: 100 pengguna bersamaan; monolit cukup untuk skala kampus.

## 11. Ketersediaan & Operasional

- Backup berkala memakai fitur backup Supabase.
- Log aplikasi di `storage/logs`; pantau error 500.
- Migrasi & seeder membuat database dapat direproduksi (NFR 5.5).
- Seeder awal: akun admin, kategori (Dompet, HP, Kunci, Tas, Dokumen/KTM, Elektronik, dll.), lokasi kampus.

## 12. Strategi Pengujian

| Level | Alat | Fokus |
|---|---|---|
| Unit | PHPUnit/Pest | Service: transisi status, pembuatan kode, aturan klaim |
| Feature | PHPUnit/Pest + `RefreshDatabase` | Alur HTTP, middleware peran, validasi upload, Policy |
| Manual/QA | Skenario SRS §7.2 | Happy path & alur alternatif |
| Browser (opsional) | Laravel Dusk | Katalog, filter, form |

Kasus uji wajib: pengguna biasa mengakses `/admin` → 403 · kode serah terima dipakai dua kali → ditolak · tamu tidak melihat `security_answer` · upload file non-gambar → ditolak · klaim ganda aktif → ditolak.

## 13. Keputusan Arsitektur (ADR Ringkas)

| # | Keputusan | Alasan |
|---|---|---|
| 1 | Monolit Laravel MVC | Wajib oleh SRS; sederhana untuk tim mahasiswa |
| 2 | Service layer untuk logika bisnis | Controller tetap tipis, mudah diuji |
| 3 | Enum untuk status | Mencegah string liar, transisi terpusat |
| 4 | Bukti klaim di disk privat | Hanya admin yang boleh melihat (NFR-05) |
| 5 | Transaksi + `lockForUpdate` pada serah terima | Mencegah kode dipakai ganda |
| 6 | Notifikasi in-app dulu, email opsional | Mengurangi ketergantungan SMTP |

## 14. Pertanyaan Terbuka

1. Supabase Storage atau disk lokal Laravel untuk berkas?
2. Notifikasi: in-app saja atau + email?
3. Kebijakan pengajuan ulang klaim (batas jumlah/jeda)?
4. Tempat deploy produksi/demo?
