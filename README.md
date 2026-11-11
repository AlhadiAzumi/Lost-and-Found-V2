# 🔍 Campus Lost & Found Portal

Sistem Informasi Pusat Kehilangan dan Penemuan Barang (Lost & Found) berbasis web untuk lingkungan kampus. Aplikasi ini memfasilitasi pelaporan barang hilang, pendataan barang temuan, dan proses verifikasi klaim kepemilikan secara aman dan terstruktur.

Proyek ini dikembangkan untuk memenuhi tugas mata kuliah **Rekayasa Perangkat Lunak**.

## ✨ Fitur Utama

### 👥 Modul Pengguna (Mahasiswa/Umum)
- **Katalog Interaktif:** Pencarian barang hilang dan ditemukan dengan filter kategori, lokasi, dan tanggal.
- **Lapor Barang Hilang (Lost):** Formulir pelaporan detail barang yang hilang beserta unggahan foto.
- **Lapor Barang Temuan (Found):** Formulir pendataan barang temuan yang dilengkapi dengan pertanyaan dan jawaban keamanan. Jawaban disimpan sebagai hash dan tidak ditampilkan kepada publik.
- **Pengajuan Klaim:** Sistem pengajuan hak milik dengan menjawab pertanyaan verifikasi dan mengunggah bukti kepemilikan.

### 🛡️ Modul Admin (Satpam/Petugas Pos)
- **Dashboard Analitik:** Ringkasan statistik persentase barang kembali, total laporan, dan klaim tertunda.
- **Moderasi Laporan:** Persetujuan atau penolakan laporan baru untuk mencegah spam.
- **Verifikasi Klaim:** Jawaban keamanan dicocokkan tanpa menyimpan jawaban asli; petugas juga memeriksa bukti kepemilikan.
- **Manajemen Serah Terima:** Sistem verifikasi kode unik saat serah terima barang di pos satpam.

### 🏫 Portal Multi-Kampus
- Satu aplikasi melayani beberapa kampus dengan data akun, laporan, klaim, dan serah terima yang dipisahkan.
- Pengguna memilih portal dari halaman `/`; setiap portal memiliki URL `/kampus/{slug}`.
- Pengunjung dapat mengajukan portal kampus dari halaman pemilihan kampus. Setelah disetujui admin platform, pemohon menerima tautan sekali pakai untuk membuat akun admin kampus.
- Admin platform dapat membuat dan mengaktifkan/nonaktifkan portal melalui menu **Kelola Kampus** di web.
- Untuk menyiapkan admin platform pertama kali, buat akun admin kampus, lalu tetapkan emailnya sebagai `PLATFORM_ADMIN_EMAIL` di environment deployment. Perintah `campus:promote-admin` dapat digunakan untuk memberikan peran admin kampus; akses kelola kampus tetap dibatasi ke email platform admin yang dikonfigurasi.
- Perintah `campus:create` tetap tersedia sebagai opsi untuk pengelolaan melalui terminal.

## 🛠️ Tech Stack
- **Framework:** Laravel
- **Database:** Supabase (PostgreSQL)
- **Architecture:** MVC (Model-View-Controller)
- **Access Control:** Laravel Middleware

## 👨‍💻 Susunan Tim (Kelompok 04 - TI 2B)
- **Project Manager:** Alhadi Azumi
- **UI/UX Designer:** Rahmat Naufal, Fatia Azahara
- **Frontend Developer:** Ayla Azzura, Fadhlul Hadi
- **Backend & Database Developer:** Abdul Karim, Riska Ayu
- **Quality Assurance (QA):** All Members

## 🚀 Cara Instalasi (Pengembangan Lokal)

1. Clone repositori ini:
   ```bash
   git clone https://github.com/AlhadiAzumi/Lost-Found.git
   ```
2. Masuk ke direktori aplikasi Laravel:
   ```bash
   cd Lost-Found/lost-and-found
   ```
3. Instal dependensi Composer:
   ```bash
   composer install
   ```
4. Salin berkas `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
5. Untuk pengembangan lokal, SQLite dapat digunakan tanpa konfigurasi tambahan. Untuk Supabase, sesuaikan konfigurasi database di `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com # Sesuaikan dengan host Supabase
   DB_PORT=6543
   DB_DATABASE=postgres
   DB_USERNAME=postgres.xxxxxxx
   DB_PASSWORD=password-supabase-kamu
   ```
6. Generate *application key*:
   ```bash
   php artisan key:generate
   ```
7. Jalankan migrasi database ke Supabase:
   ```bash
   php artisan migrate
   ```
   Migrasi menyiapkan portal awal dari `DEFAULT_CAMPUS_NAME` dan `DEFAULT_CAMPUS_SLUG` (default: `Kampus Utama` dan `kampus-utama`). Tetapkan nilai tersebut sebelum migrasi pertama bila ingin mengganti portal awal.
8. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
Aplikasi dapat diakses melalui `http://localhost:8000`; halaman awal menampilkan pilihan kampus dan portal default berada di `/kampus/kampus-utama`.

Untuk menyiapkan pengelola platform, tetapkan `PLATFORM_ADMIN_EMAIL=email-admin@domain.ac.id` di `.env` lokal atau Environment Variables deployment. Email itu harus dimiliki akun dengan role `admin`; gunakan perintah `campus:promote-admin` satu kali bila akun belum menjadi admin. Setelah login, pengelola dapat membuka **Kelola Kampus** dari menu dan membuat portal tanpa perintah terminal. Pengelolaan terminal tetap tersedia melalui `campus:create`.

Pengiriman undangan admin kampus memerlukan mailer yang benar-benar mengirim email. `MAIL_MAILER=log` (nilai pengembangan lokal) hanya mencatat email ke log aplikasi; atur `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, dan `MAIL_FROM_ADDRESS` di environment deployment agar pemohon menerima tautan aktivasi.

## Deploy demo ke Vercel

Proyek Laravel ini menggunakan runtime PHP komunitas `vercel-php` di Vercel. Runtime ini tidak resmi didukung Vercel dan memiliki batasan filesystem serverless.

1. Hubungkan repositori GitHub ke Vercel dan atur **Root Directory** ke `lost-and-found`.
2. `vercel.json` menjalankan `npm run build`, menerbitkan `public`, dan mengarahkan permintaan dinamis ke `api/index.php`.
3. Tambahkan variabel rahasia melalui pengaturan Vercel, jangan simpan nilainya di repositori:
   - `APP_KEY` — gunakan application key yang sama dengan environment aplikasi.
   - `APP_URL` — URL deployment Vercel.
   - `DB_CONNECTION=pgsql`
   - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` — kredensial database Supabase.
   - `DB_SSLMODE=require`
   - `DB_EMULATE_PREPARES=true` — wajib untuk connection pooler Supabase.
   - `TRUSTED_PROXIES=*` — agar URL dan cookie mengikuti domain publik Vercel.
   - `SESSION_SECURE_COOKIE=true`
   - `APP_DEBUG=false`
4. Siapkan Supabase Storage untuk foto barang dan bukti klaim:
   - Buat bucket **publik** `lost-found-public` (foto barang) dan bucket **privat** `lost-found-private` (bukti klaim) di menu Storage.
   - Buka **Storage → S3** di dashboard Supabase, aktifkan koneksi S3 bila belum, lalu buat pasangan Access Key.
   - Tambahkan variabel berikut di Vercel (nilai dari halaman S3 tersebut):
     - `MEDIA_DISK_DRIVER=s3`
     - `SUPABASE_STORAGE_KEY`, `SUPABASE_STORAGE_SECRET`
     - `SUPABASE_STORAGE_REGION` — mis. `ap-south-1`
     - `SUPABASE_STORAGE_ENDPOINT` — `https://<project-ref>.supabase.co/storage/v1/s3`
     - `SUPABASE_PUBLIC_BUCKET=lost-found-public`
     - `SUPABASE_PRIVATE_BUCKET=lost-found-private`
     - `SUPABASE_STORAGE_PATH_STYLE=true`
   - `SUPABASE_PUBLIC_URL` boleh dikosongkan; aplikasi menurunkannya dari endpoint dan bucket publik.
5. Deploy aplikasi. Jalankan migration yang pending dari komputer lokal yang terhubung ke database Supabase:
   ```bash
   php artisan migrate --pretend
   php artisan migrate --force
   ```

`vercel.json` sudah mengarahkan function ke region `bom1` (Mumbai) agar dekat dengan database Supabase `ap-south-1`.

Vercel Hobby memiliki kuota penggunaan dan ditujukan untuk penggunaan personal/non-komersial. Runtime ini hanya dapat menulis ke direktori sementara, sehingga `MEDIA_DISK_DRIVER=s3` wajib diisi di deployment; tanpa itu unggahan foto dan bukti klaim akan gagal atau hilang setelah function didaur ulang.
