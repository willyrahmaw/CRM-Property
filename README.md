# PROPFlow — Enterprise Property CRM & Inventory Management System

<p align="center">
  <img src="public/images/properties/project_grand_harmony.jpg" alt="PROPFlow Banner" width="100%" style="max-height: 400px; object-fit: cover; border-radius: 12px; border: 1px solid #E8E4DA;" />
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12" />
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/Vite-8.x-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite" />
  <img src="https://img.shields.io/badge/Tests-85%20Passed-15803D?style=for-the-badge&logo=githubactions&logoColor=white" alt="Tests Passed" />
  <img src="https://img.shields.io/badge/License-Proprietary-B89B5E?style=for-the-badge" alt="License" />
</p>

**PROPFlow** adalah platform enterprise *Property Sales CRM & Real Estate Inventory Management* yang dirancang khusus untuk pengembang properti (*property developers*), agensi pemasaran, tim penjualan, sales manager, dan tim keuangan. 

Dibangun dengan filosofi desain **Luxury Corporate Minimalist** berpalet solid elegan (*Gold Accent, Deep Charcoal, Soft Cream* — tanpa gradien) serta arsitektur backend Laravel yang tangguh, aman, dan siap pakai untuk produksi skala besar.

---

## 🌟 Fitur Unggulan Sistem

### 1. 💼 Manajemen Prospek & CRM (Omnichannel Leads)
- **Omnichannel Lead Intake**: Pencatatan prospek masuk dari berbagai sumber (*Instagram Ads, Facebook, Google Ads, Walk-In, Referral, Expo Pameran*).
- **Automated Lead Scoring**: Kalkulasi otomatis skor potensi prospek berdasarkan kesesuaian anggaran, urgensi pembelian, jadwal survei, dan simulasi KPR.
- **Indikator Suhu Minat**: Klasifikasi instan prospek menjadi `COLD`, `WARM`, dan `HOT`.
- **Strategi Distribusi Prospek (Lead Assignment)**: Pembagian prospek ke sales agent menggunakan strategi *Round-Robin*, *Workload-Based*, atau *Project PIC*.
- **Aktivitas & Site Visits**: Penjadwalan survei lokasi fisik dan pencatatan interaksi WhatsApp / panggilan telepon terintegrasi.
- **Database Konsumen**: Konversi otomatis prospek yang closing menjadi data konsumen terdaftar.

### 2. 📊 Interactive Sales Pipeline Kanban
- Papan visual Kanban dengan perpindahan tahap prospek yang terstruktur:
  `NEW` → `CONTACTED` → `QUALIFIED` → `SITE_VISIT` → `NEGOTIATION` → `BOOKING` → `WON` / `LOST`.
- Alasan pembatalan (*Lost Reason*) wajib dicatat untuk analisis kinerja kampanye.
- **Touch-Friendly Mobile Swipe**: Dilengkapi *horizontal scroll snap* halus di perangkat seluler.

### 3. 🏡 Master Inventori Properti & Arsitektur
- **Manajemen Proyek Kawasan**: Master kawasan hunian, lokasi kota, legalitas developer, dan foto lanskap utama.
- **Cluster dengan Galeri Multi-Foto**: Kemampuan mengunggah banyak foto per cluster (hingga 10 foto) dengan pratinjau thumbnail dan manajemen penghapusan berkas parsial.
- **Spesifikasi Bangunan & Fisik Lengkap**:
  - Dimensi kaveling (P x L dalam meter), Luas Bangunan (LB), Luas Tanah (LT), Jumlah Lantai, Kamar Tidur, Kamar Mandi, dan Kapasitas Carport.
  - Daya Listrik PLN (`1.300 VA` s/d `7.700 VA+`), Sumber Air (`PDAM`, `WTP Mandiri`, `Sumur Bor`), dan Legalitas Sertifikat (`SHM`, `HGB Split`).
  - Rincian material arsitektural: Pondasi, Dinding, Rangka Atap, Lantai Utama, Kusen Pintu/Jendela, dan Sanitair.
- **Interactive Siteplan**: Denah zonasi visual kaveling interaktif untuk memeriksa nomor unit, blok, dan status penjualan secara visual.

### 4. 🔒 Pemesanan Unit (Booking SPP) & Anti Double-Booking
- **Concurrency Protection**: Pencegahan perebutan unit yang sama oleh dua pembeli pada detik yang bersamaan menggunakan database lock (`lockForUpdate`) dalam satu transaksi ACID.
- Validasi ketersediaan unit secara *real-time* di tingkat database sebelum surat pesanan (SPP) diterbitkan.

### 5. 🤝 Pengajuan Diskon & Negosiasi Berjenjang
- Pengajuan diskon oleh Sales Agent dengan matriks batas kewenangan:
  - Diskon standar: Cukup persetujuan **Sales Manager**.
  - Diskon tinggi: Membutuhkan persetujuan langsung **Company Owner**.

### 6. 💳 Keuangan, KPR & Verifikasi Pembayaran
- **Kwitansi & Verifikasi Kasir**: Pengunggahan bukti transfer konsumen dan verifikasi oleh tim Finance.
- **Manajemen KPR**: Pelacakan status pengajuan bank konsumen dari berkas masuk, SP3K, hingga akad kredit.
- **Pengaturan & Pencairan Komisi**:
  - Persentase komisi fleksibel yang diatur oleh Owner (Komisi Closing & Komisi Akad KPR).
  - Skema persetujuan berjenjang: Approval oleh Sales Manager → Pencairan oleh Finance.

### 7. 🚀 Kompresi Gambar Otomatis ke Format Modern WebP
- Layanan terpusat (`ImageOptimizerService`) yang secara otomatis mengompresi setiap gambar yang diunggah (JPEG/PNG/BMP) menjadi format **WebP**.
- Menghemat ruang penyimpanan server dan bandwidth pengguna hingga **70% - 85%** dengan tetap mempertahankan kualitas visual foto luxury.
- Auto-scaling cerdas dengan batas resolusi maksimal 1920px untuk gambar kamera resolusi raksasa.

### 8. 📱 Arsitektur Responsif Mobile-Friendly Penuh
- **Off-Canvas Drawer Navigation**: Sidebar desktop otomatis beralih menjadi drawer geser mewah di layar smartphone dengan backdrop overlay transparan.
- **Touch Targets Optimal**: Tombol aksi dan navigasi memiliki area sentuh minimal 44px ramah ibu jari.
- **Tabel Data Aman di Ponsel**: Tabel data tidak terjepit dan dapat digeser menyamping (*touch horizontal scroll*) dengan batas lebar minimum yang aman.

### 9. 🕒 Dukungan 3 Zona Waktu Indonesia Penuh (WIB, WITA, WIT)
- **WIB (UTC+7 / `Asia/Jakarta`)**: Sumatera, Jawa, Madura, Kalimantan Barat, Kalimantan Tengah.
- **WITA (UTC+8 / `Asia/Makassar`)**: Bali, Nusa Tenggara, Kalimantan Selatan, Kalimantan Timur, Kalimantan Utara, Sulawesi.
- **WIT (UTC+9 / `Asia/Jayapura`)**: Maluku, Maluku Utara, Papua.
- **Quick Switcher Topbar**: Pengalihan zona waktu secara instan dengan dropdown interaktif 1-klik di header navigasi utama.
- **Sinkronisasi Waktu Otomatis**: Middleware `SetUserTimezone` secara cerdas menyesuaikan waktu pencatatan jadwal survei lokasi, riwayat aktivitas prospek, dan audit log sesuai zona waktu pengguna aktif.

### 10. 🛡️ Keamanan Enterprise & Isolasi Multi-Tenant
- **Multi-Tenant Ready**: Setiap data terikat ke `company_id` yang divalidasi server-side (mencegah IDOR/BOLA).
- **Private Storage**: Dokumen sensitif konsumen (KTP, NPWP, KK, Slip Gaji) disimpan di *Private Storage*, hanya dapat diunduh oleh pengguna berwenang.
- **Role-Based Access Control (RBAC)**: Pembatasan akses berbasis peran di tingkat Middleware dan Policy.
- **Audit Trail**: Pencatatan log otomatis untuk setiap aksi kritis (persetujuan booking, revisi harga, verifikasi dana, aktivasi user).

---

## 👥 Peran Pengguna (User Roles)

| Peran | Tanggung Jawab & Hak Akses |
| :--- | :--- |
| **Super Admin** | Akses penuh lintas perusahaan dan pemeliharaan sistem global. |
| **Company Owner** | Pemilik perusahaan developer: memantau omset, mengatur persentase komisi, konfigurasi website branding, dan persetujuan diskon tingkat tinggi. |
| **Sales Manager** | Memimpin tim penjualan, membagikan leads, memonitor pipeline, menyetujui diskon & negosiasi, serta menyetujui komisi sales. |
| **Team Leader** | Mengkoordinasikan sub-tim sales, memantau jadwal survei lokasi, dan memonitor target penjualan tim. |
| **Sales Agent** | Mengelola prospek pribadi, membuat jadwal kunjungan lapangan, mengajukan diskon, dan menerbitkan pemesanan booking unit. |
| **Finance** | Memverifikasi pembayaran dan kwitansi konsumen, memonitor status akad KPR bank, dan memproses pencairan dana komisi. |
| **Admin Property** | Mengelola inventori master properti, kawasan proyek, penambahan cluster multi-foto, spesifikasi teknis unit, dan denah siteplan. |

---

## 🛠️ Teknologi yang Digunakan

- **Backend Framework**: [Laravel 12.x](https://laravel.com) (PHP 8.3+)
- **Database**: MySQL / MariaDB (Mendukung SQLite untuk pengujian cepat)
- **Frontend & Styling**: Blade Templating + [Tailwind CSS v4](https://tailwindcss.com) (Tanpa ketergantungan SPA berat)
- **Assets Bundler**: [Vite 8.x](https://vitejs.dev)
- **Ikon UI**: [Font Awesome 6 Free Solid & Brands](https://fontawesome.com)
- **Dialog & Notifikasi**: [SweetAlert2](https://sweetalert2.github.io)
- **Pengolah Gambar**: Native PHP GD dengan ekstensi `imagewebp`
- **Testing**: PHPUnit 12

---

## ⚙️ Persyaratan Sistem (Prerequisites)

- **PHP**: Versi `>= 8.3` dengan ekstensi: `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `gd` (dengan dukungan WebP).
- **Composer**: Versi `>= 2.2`
- **Node.js**: Versi `>= 18.x` & **NPM**
- **Database**: MySQL `>= 8.0` / MariaDB `>= 10.5`

---

## 🚀 Panduan Instalasi & Menjalankan

### 1. Kloning Repositori
```bash
git clone git@github.com:willyrahmaw/CRM-Property.git
cd CRM-Property
```

### 2. Pasang Dependensi Backend & Frontend
```bash
composer install
npm install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan kredensial database pada `.env`:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_property
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Tautkan Storage Simbolik
```bash
php artisan storage:link
```

### 5. Jalankan Migrasi & Seeder Data Realistis
```bash
php artisan migrate:fresh --seed
```
*Seeder akan membuat profil developer lengkap PT Grand Harmony Land, kawasan proyek, cluster, unit kaveling dengan spesifikasi arsitektural lengkap, leads, booking aktif, dan seluruh hierarki akun pengguna.*

### 6. Kompilasi Aset Frontend
```bash
# Untuk mode pengembangan (hot reload):
npm run dev

# Atau untuk kompilasi production:
npm run build
```

### 7. Jalankan Server Aplikasi
```bash
php artisan serve
```
Buka peramban Anda di: `http://127.0.0.1:8000`

---

## 🔑 Akun Demo Bawaan (Default Seeded Credentials)

Semua akun demo bawaan menggunakan kata sandi standar: **`CrmProperty123!`**

| Peran (Role) | Nama Pengguna | Alamat Email | Password |
| :--- | :--- | :--- | :--- |
| **Company Owner** | Bambang Wijaya | `owner@propflow.local` | `CrmProperty123!` |
| **Sales Manager** | Hendrik Pratama | `manager@propflow.local` | `CrmProperty123!` |
| **Finance** | Siti Rahmawati | `finance@propflow.local` | `CrmProperty123!` |
| **Admin Property** | Dedi Irawan | `admin@propflow.local` | `CrmProperty123!` |
| **Sales Agent 1** | Andi Setiawan | `andi@propflow.local` | `CrmProperty123!` |
| **Sales Agent 2** | Rina Melati | `rina@propflow.local` | `CrmProperty123!` |

---

## 🧪 Pengujian Otomatis (Automated Tests)

Aplikasi dilengkapi dengan 85 pengujian otomatis (*Feature & Unit tests*) yang mencakup integritas bisnis kritis:
- Pencegahan double-booking (*Concurrency Test* dengan transaksi database).
- Otorisasi menu dan rute berbasis peran (*Role & Permission Gate*).
- Multi-photo upload pada cluster properti dan penghapusan parsial.
- Layanan kompresi gambar otomatis ke format WebP.
- Responsivitas tata letak seluler dan off-canvas drawer navigasi.
- Dukungan 3 zona waktu Indonesia (WIB, WITA, WIT) dengan middleware dan quick switcher.

Untuk menjalankan seluruh test suite:
```bash
php artisan test
```

Hasil:
```text
Pass: 85 passed, 373 assertions
Duration: ~5s
```

---

## 📁 Struktur Direktori Proyek

```text
app/
├── Enums/                 # PHP Enums: State unit, status booking, suhu lead, role
├── Http/
│   ├── Controllers/       # Thin controllers per kapabilitas bisnis
│   ├── Middleware/        # Proteksi peran (EnsureUserHasRole)
│   └── Requests/          # Form request validations ketat
├── Models/                # Eloquent models dengan UUID & Multi-tenant scope
├── Services/              # Business logic terisolasi
│   ├── BookingService.php         # Transaksi pemesanan & locking
│   ├── CommissionService.php      # Hitungan & approval komisi
│   ├── ImageOptimizerService.php  # Kompresi native WebP otomatis
│   ├── LeadService.php            # Pengelolaan & scoring prospek
│   ├── LeadAssignmentService.php  # Strategi distribusi prospek
│   ├── PaymentService.php         # Verifikasi dana kasir
│   └── AuditLogService.php        # Pencatatan riwayat audit
database/
├── migrations/            # Skema tabel database dengan indexing tepat
└── seeders/               # Data awal realistis properti & pengguna
resources/
├── css/                   # Desain sistem Tailwind CSS solid (no gradient)
├── js/
│   ├── app.js             # Inisialisasi modul frontend
│   └── modules/           # Modul JS terisolasi (drawer, sweetalert, booking, tabs)
└── views/                 # Blade templates & Reusable Components
tests/
├── Feature/               # End-to-end business flow tests
└── Unit/                  # Isolated service tests
```

---

## 📜 Lisensi

Sistem ini dikembangkan secara eksklusif sebagai solusi proprietary CRM Properti Enterprise. Hak cipta dilindungi undang-undang.
