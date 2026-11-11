# Product Requirements Document (PRD)
## Campus Lost & Found Portal

| | |
|---|---|
| **Tim** | Kelompok 04 – TI 2B |
| **Mata Kuliah** | Rekayasa Perangkat Lunak |
| **Versi** | 1.0 (Draft) |
| **Tanggal** | November 2026 |
| **Sumber** | SRS Campus Lost & Found Portal v1.0 (Oktober 2026) |

> Dokumen ini menerjemahkan SRS menjadi arah produk: *mengapa* dibangun, *untuk siapa*, dan *apa* yang harus ada. Detail kebutuhan formal (FR/NFR) tetap mengacu ke SRS. Bagian bertanda **[Asumsi]** perlu dikonfirmasi tim/dosen.

---

## 1. Ringkasan Produk

Campus Lost & Found Portal adalah aplikasi web untuk melaporkan barang hilang, mendata barang temuan, dan memverifikasi klaim kepemilikan secara aman di lingkungan kampus. Pos satpam kampus menjadi titik penyimpanan dan serah terima fisik barang.

**Value proposition:** *"Barang hilang lebih cepat kembali ke pemilik aslinya — tanpa risiko klaim palsu."*

Satu deployment aplikasi dapat melayani beberapa kampus melalui portal terpisah. Setiap akun, laporan, klaim, dan serah terima hanya terlihat di portal kampus tempat data tersebut dibuat.

## 2. Latar Belakang & Masalah

| Masalah saat ini | Dampak |
|---|---|
| Pengumuman kehilangan tersebar di grup chat/media sosial dan mudah tenggelam | Barang jarang kembali, pelapor frustrasi |
| Penemu tidak tahu harus menyerahkan ke mana / ke siapa | Barang temuan menumpuk tanpa data |
| Tidak ada verifikasi kepemilikan yang baku | Risiko klaim palsu dan barang jatuh ke orang yang salah |
| Tidak ada pencatatan serah terima | Tidak ada akuntabilitas dan statistik |

## 3. Tujuan & Metrik Keberhasilan

| Tujuan | Metrik | Target **[Asumsi]** |
|---|---|---|
| Mempercepat pengembalian barang | Persentase laporan Found berstatus *Dikembalikan* | ≥ 40% per semester |
| Mencegah klaim palsu | Klaim disetujui yang kemudian disengketakan | 0 kasus |
| Mudah dipakai | Waktu membuat laporan oleh pengguna baru | < 5 menit (NFR 5.4) |
| Kinerja | Waktu muat katalog (≤ 1.000 laporan) | ≤ 3 detik (NFR 5.2) |
| Adopsi | Pengguna terdaftar di bulan pertama | ≥ 100 akun |

## 4. Persona Pengguna

### 4.1 Rina – Pemilik Barang (Pengguna)
Mahasiswa semester 3, kehilangan dompet di kantin. Ingin cepat tahu apakah ada yang menemukannya, dan ingin proses klaim jelas.
- **Kebutuhan:** cari barang lewat kata kunci/filter, lapor Lost, klaim dengan bukti, pantau status.
- **Frustrasi:** tidak tahu harus tanya ke siapa.

### 4.2 Dimas – Penemu (Pengguna)
Menemukan flashdisk di laboratorium, ingin menolong tapi tidak ingin repot.
- **Kebutuhan:** form lapor Found sederhana, bisa menitipkan ke pos satpam, tidak bertanggung jawab atas verifikasi.

### 4.3 Pak Joko – Petugas Pos Satpam (Admin)
Mengelola barang fisik dan harus memastikan barang diserahkan ke orang yang tepat.
- **Kebutuhan:** antrean moderasi, perbandingan jawaban klaim vs jawaban benar vs bukti dalam satu layar, input Kode Serah Terima, dashboard.

### 4.4 Tamu
Pengunjung belum login: boleh melihat katalog terbatas, registrasi, dan login.

## 5. Ruang Lingkup

### 5.1 In Scope (v1.0)
- Beranda pengantar di `/` yang mengarahkan pengguna ke katalog dan alur pelaporan
- Katalog Lost & Found dengan pencarian, filter, tab, paginasi
- Pelaporan Lost dan Found (dengan foto)
- Security Question pada laporan Found
- Pengajuan klaim + bukti kepemilikan
- Moderasi laporan, verifikasi klaim, Kode Serah Terima, riwayat serah terima
- Dashboard statistik admin, data master kategori & lokasi
- Notifikasi (in-app; email opsional)
- Portal multi-kampus berbasis path `/kampus/{slug}` dengan pemisahan data antarkampus

### 5.2 Out of Scope (v1.0)
Aplikasi mobile native · pembayaran/transaksi · integrasi SSO/SIAKAD · pelacakan GPS/IoT.

**Navigasi publik:** `/` menampilkan pilihan portal kampus. Beranda, katalog, dan halaman informasi berada di bawah prefix `/kampus/{slug}`; katalog dengan pencarian dan filter tersedia di `/kampus/{slug}/catalog`.

Halaman informasi menjelaskan penggunaan layanan untuk warga Politeknik Negeri Lhokseumawe tanpa menyatakan platform sebagai portal resmi sebelum ada persetujuan institusi. Kanal kontak yang ditampilkan harus diverifikasi pengelola; kebijakan retensi dan penghapusan data perlu disahkan sebelum penggunaan produksi.

## 6. Fitur & User Stories

Prioritas: **H** = High (wajib), **M** = Medium, **L** = Low. Kolom SRS memetakan ke ID kebutuhan.

### 6.1 Autentikasi & Akun
| ID | User Story | Prioritas | SRS |
|---|---|---|---|
| US-01 | Sebagai tamu, saya ingin registrasi (nama, email, telepon, sandi) agar bisa melapor | H | FR-01 |
| US-02 | Sebagai pengguna, saya ingin login/logout dengan aman | H | FR-02, FR-03, NFR-09 |
| US-03 | Sebagai pengguna, saya ingin mengubah profil dan sandi | M | FR-05 |
| US-04 | Sebagai pengguna, saya ingin reset sandi lewat email | L | FR-06 |
| US-05 | Sebagai sistem, halaman dibatasi sesuai peran | H | FR-04, NFR-04 |

### 6.2 Katalog
| ID | User Story | Prioritas | SRS |
|---|---|---|---|
| US-06 | Saya ingin melihat daftar laporan yang sudah disetujui | H | FR-07 |
| US-07 | Saya ingin mencari berdasarkan kata kunci | H | FR-08 |
| US-08 | Saya ingin memfilter kategori, lokasi, rentang tanggal | H | FR-09 |
| US-09 | Saya ingin memisahkan tab Lost dan Found | H | FR-10 |
| US-10 | Saya ingin melihat detail laporan (foto, lokasi, tanggal, status) | H | FR-11 |
| US-11 | Sebagai penemu, ciri khusus barang disembunyikan dari publik | M | FR-12 |
| US-12 | Saya ingin hasil dipaginasi | M | FR-13 |

### 6.3 Pelaporan
| ID | User Story | Prioritas | SRS |
|---|---|---|---|
| US-13 | Sebagai pemilik, saya ingin lapor barang hilang dengan form lengkap + foto opsional | H | FR-14, FR-15 |
| US-14 | Sebagai pemilik, saya ingin melihat/ubah/tutup laporan Lost saya | M | FR-17 |
| US-15 | Sebagai penemu, saya ingin lapor barang temuan + lokasi penitipan + foto | H | FR-18, FR-21 |
| US-16 | Sebagai penemu, saya wajib membuat Security Question dan jawabannya | H | FR-19, FR-20 |
| US-17 | Laporan baru otomatis berstatus *Menunggu Moderasi* | H | FR-16, FR-22 |

### 6.4 Klaim
| ID | User Story | Prioritas | SRS |
|---|---|---|---|
| US-18 | Sebagai pemilik, saya ingin mengajukan klaim pada laporan Found yang disetujui | H | FR-23 |
| US-19 | Saya wajib menjawab Security Question | H | FR-24 |
| US-20 | Saya wajib/dapat mengunggah bukti kepemilikan | H | FR-25 |
| US-21 | Saya hanya bisa punya satu klaim aktif per barang | M | FR-26 |
| US-22 | Saya ingin melihat status klaim dan alasan penolakan | H/M | FR-27, FR-28 |

### 6.5 Admin
| ID | User Story | Prioritas | SRS |
|---|---|---|---|
| US-23 | Sebagai admin, saya ingin dashboard ringkas (total, Lost/Found, klaim tertunda, % dikembalikan) | H | FR-29 |
| US-24 | Saya ingin menyetujui/menolak laporan (alasan wajib saat menolak) | H | FR-30 |
| US-25 | Saya ingin melihat jawaban klaim, jawaban benar, dan bukti berdampingan | H | FR-31 |
| US-26 | Saya ingin menyetujui/menolak klaim dengan catatan | H | FR-32 |
| US-27 | Sistem menerbitkan Kode Serah Terima unik saat klaim disetujui | H | FR-33, NFR-08 |
| US-28 | Saya ingin memasukkan kode saat pemilik datang; barang menjadi *Dikembalikan* | H | FR-34 |
| US-29 | Saya ingin menghapus/mengarsipkan laporan tidak sesuai | M | FR-35 |
| US-30 | Saya ingin mengelola kategori & lokasi | M | FR-36 |
| US-31 | Saya ingin melihat riwayat seluruh serah terima | M | FR-37 |

### 6.6 Notifikasi
| ID | User Story | Prioritas | SRS |
|---|---|---|---|
| US-32 | Pengguna diberi tahu saat laporan disetujui/ditolak | M | FR-38 |
| US-33 | Pengguna diberi tahu saat klaim diputuskan + Kode Serah Terima | M | FR-39 |
| US-34 | Admin diberi tahu saat ada laporan/klaim baru | L | FR-40 |

## 7. Alur Utama (Happy Path)

1. **Penemu** lapor Found + Security Question → status *Menunggu Moderasi*
2. **Admin** menyetujui → tampil di katalog (*Disetujui*)
3. **Pemilik** mengajukan klaim: jawab pertanyaan + unggah bukti → *Dalam Proses Klaim*
4. **Admin** membandingkan jawaban & bukti → setujui (terbit Kode Serah Terima) atau tolak (+ alasan)
5. **Pemilik** datang ke pos membawa kode + identitas
6. **Admin** input kode → valid → barang diserahkan → *Dikembalikan*, serah terima tercatat

**Alur alternatif:** klaim ditolak (boleh ajukan ulang sesuai kebijakan); kode tidak valid/sudah dipakai → serah terima ditolak.

**Siklus status laporan:** `Menunggu Moderasi → Disetujui | Ditolak → Dalam Proses Klaim → Dikembalikan`; laporan Lost dapat `Ditutup` oleh pemiliknya.

## 8. Kebutuhan Nonfungsional (Ringkas)

- **Keamanan:** hash bcrypt/argon2, CSRF, validasi server-side, middleware peran, upload divalidasi (JPG/PNG/WebP, maks. 2 MB), `.env` tidak di-commit, kode serah terima unik & sekali pakai, rate limiting login.
- **Kinerja:** katalog ≤ 3 detik (≤ 1.000 laporan), ≥ 100 pengguna bersamaan, indeks pada kategori/lokasi/tanggal/status.
- **Ketersediaan:** target 99% pada jam operasional kampus; backup Supabase.
- **Usability:** responsif, umpan balik sukses/gagal di tiap aksi penting, bahasa Indonesia.
- **Maintainability:** MVC, PSR-12, migrasi Laravel, kompatibel browser modern.

## 9. Pertimbangan Privasi & Keamanan Produk

- Jawaban Security Question dan bukti kepemilikan **hanya** terlihat admin.
- Detail identifikasi barang Found disembunyikan dari publik.
- Tamu hanya melihat informasi terbatas; kontak pelapor tidak ditampilkan publik **[Asumsi]**.
- Data pribadi (telepon) hanya dipakai untuk keperluan proses kehilangan/klaim.

## 10. Asumsi, Risiko, & Pertanyaan Terbuka

| # | Item | Jenis | Tindakan |
|---|---|---|---|
| 1 | Batas ukuran foto (2 MB) | Asumsi | Konfirmasi tim |
| 2 | Kebijakan pengajuan ulang klaim (berapa kali/jeda) | Pertanyaan | Putuskan dengan dosen/satpam |
| 3 | Notifikasi: in-app saja atau + email SMTP | Pertanyaan | Putuskan di sprint 1 |
| 4 | Siapa yang memutuskan barang temuan tak diklaim (masa simpan/dihibahkan) | Pertanyaan | Tanyakan pengelola pos |
| 5 | Ketergantungan Supabase & internet | Risiko | Siapkan pengaturan lokal untuk demo |
| 6 | Tim 8 orang dengan jadwal kuliah padat | Risiko | Pecah tugas per modul, review PR rutin |
| 7 | Foto berisi data sensitif (KTM, KTP) | Risiko | Admin dapat menyembunyikan/menghapus foto |

## 11. Rencana Rilis **[Asumsi]**

| Milestone | Isi |
|---|---|
| **M1 – Fondasi** | Setup Laravel + Supabase, migrasi, auth, middleware peran |
| **M2 – Pelaporan** | CRUD Lost/Found, upload foto, status awal |
| **M3 – Katalog** | Daftar, pencarian, filter, tab, detail, paginasi |
| **M4 – Klaim & Admin** | Klaim, verifikasi, kode serah terima, serah terima |
| **M5 – Penyempurnaan** | Dashboard, data master, notifikasi, QA, dokumentasi |

## 12. Kriteria Penerimaan (Definition of Done)

- Seluruh kebutuhan **H** pada SRS lulus uji.
- Alur happy path (§7) dapat didemokan end-to-end dengan akun Pengguna dan Admin.
- Tidak ada kredensial di repositori; semua formulir memiliki CSRF.
- Tampilan terbaca pada layar ponsel dan desktop.
- Migrasi + seeder dapat membangun ulang database dari nol.
