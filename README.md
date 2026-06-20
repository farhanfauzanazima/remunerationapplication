# 🍽️ Remuneration Application
### Sistem Remunerasi Restoran — Frontend Web (Laravel Blade)

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

---

## 📋 Deskripsi Proyek

**Remuneration Application** adalah aplikasi web frontend berbasis Laravel Blade yang berfungsi sebagai antarmuka pengguna (UI) untuk sistem remunerasi restoran. Aplikasi ini berkomunikasi dengan backend REST API secara penuh melalui HTTP Client Laravel — tidak memerlukan koneksi database langsung.

Aplikasi ini dirancang untuk memudahkan proses penggajian restoran yang sebelumnya dilakukan manual menggunakan Microsoft Excel, menjadi sistem yang terotomatisasi, efisien, dan mudah digunakan oleh seluruh tim.

### Repositori Terkait

| Komponen | Repository | Port Default |
|---|---|---|
| **Frontend (ini)** | remunerationapplication | 8080 |
| **Backend API** | apiremunerationapplication | 8000 |

---

## ✨ Fitur Utama

| Fitur | Deskripsi | Role |
|---|---|---|
| 🔐 **Login & Logout** | Autentikasi berbasis session & token API | Semua |
| 📊 **Dashboard** | Ringkasan statistik berbeda per role | Semua |
| 🏷️ **Kategori Gaji** | Kelola kategori & komponen gaji | Owner |
| 👥 **Manajemen Karyawan** | CRUD karyawan + riwayat gaji | Owner, Head |
| 📅 **Periode Penggajian** | Kelola periode open/close | Owner, Head |
| 🧮 **Input Slip Gaji** | Single & massal dengan kalkulasi realtime | Semua |
| 📄 **Generate PDF** | Preview, download, bulk generate PDF | Semua |
| 📧 **Distribusi Email** | Kirim slip via email single & massal | Semua |
| 📈 **Laporan & Statistik** | Rekap per periode, tren, per karyawan | Owner, Head |
| 📋 **Activity Log** | Catatan aktivitas seluruh pengguna | Owner |
| 👤 **Profil & Password** | Update profil & ganti password | Semua |

---

## 🛠️ Teknologi yang Digunakan

| Teknologi | Versi | Kegunaan |
|---|---|---|
| **PHP** | 8.3 | Bahasa pemrograman |
| **Laravel** | 12.x | Framework web |
| **Laravel Blade** | - | Template engine |
| **Bootstrap** | 5.3 | CSS framework via CDN |
| **Bootstrap Icons** | 1.11 | Ikon via CDN |
| **Vanilla JavaScript** | ES6+ | Interaktivitas UI |
| **Laravel HTTP Client** | - | Komunikasi ke backend API |

---

## 👤 Role & Hak Akses

| Role | Deskripsi | Menu yang Bisa Diakses |
|---|---|---|
| **Owner** | Pemilik restoran | Semua menu termasuk kategori gaji & activity log |
| **Head** | Kepala Toko / HR | Karyawan, periode, slip gaji, email, laporan |
| **Admin** | Admin Toko | Slip gaji, generate PDF, distribusi email |

---

## 🚀 Cara Instalasi

### Prasyarat
- PHP >= 8.3
- Composer
- Backend API (`apiremunerationapplication`) sudah berjalan di port 8000
- Git

### Langkah Instalasi

**1. Clone Repository**
```bash
git clone https://github.com/farhanfauzanazima/remunerationapplication.git
cd remunerationapplication
```

**2. Install Dependencies**
```bash
composer install
```

**3. Salin File Environment**
```bash
cp .env.example .env
```

**4. Generate Application Key**
```bash
php artisan key:generate
```

**5. Konfigurasi `.env`**
```env
APP_NAME="Sistem Remunerasi Restoran"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8080

# URL Backend API
API_BASE_URL=http://127.0.0.1:8000/api

# Tidak perlu database — gunakan file
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

SESSION_DRIVER=file
CACHE_STORE=file
```

**6. Jalankan Server**
```bash
php artisan serve --port=8080
```

Akses di browser: `http://127.0.0.1:8080`

---

## ⚙️ Menjalankan Keduanya (Backend + Frontend)

Jalankan dua terminal secara bersamaan:

**Terminal 1 — Backend API:**
```bash
cd C:\laragon\www\apiselfserviceapplication
php artisan serve --host=0.0.0.0 --port=8000
```

**Terminal 2 — Frontend Web:**
```bash
cd C:\laragon\www\remunerationapplication
php artisan serve --host=0.0.0.0 --port=8080
```

> **Catatan Port:** Jika terjadi bentrok port, ubah `APP_URL` di `.env` frontend dan sesuaikan `API_BASE_URL`. Konfigurasi port backend ada di `config/api.php`.

---

## 🔑 Default Akun (dari Seeder Backend)

| Role | Email | Password |
|---|---|---|
| Owner | owner@resto.com | password123 |
| Kepala Toko | head@resto.com | password123 |
| Admin Toko | admin@resto.com | password123 |

> ⚠️ Ganti password default setelah instalasi pertama!

---

## 📁 Struktur Folder
app/
├── Helpers/
│   └── helpers.php                  ← rupiah(), statusBadge(), roleLabel()
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php       ← Login, logout, profil, ganti password
│   │   ├── DashboardController.php  ← Dashboard per role
│   │   ├── CategoryController.php   ← Kategori gaji (Owner)
│   │   ├── EmployeeController.php   ← Manajemen karyawan
│   │   ├── PeriodController.php     ← Periode penggajian
│   │   ├── SalarySlipController.php ← Slip gaji single & bulk
│   │   ├── PdfController.php        ← Generate, preview, download PDF
│   │   ├── EmailController.php      ← Distribusi email
│   │   ├── ReportController.php     ← Laporan & statistik
│   │   └── ActivityLogController.php← Activity log
│   └── Middleware/
│       ├── AuthMiddleware.php        ← Cek session token
│       └── RoleMiddleware.php        ← Proteksi per role
└── Services/
└── ApiService.php                    ← HTTP Client ke backend API
config/
└── api.php                           ← Konfigurasi base URL & timeout API
public/
├── css/
│   └── app.css                       ← Custom stylesheet
└── js/
└── app.js                            ← Custom JavaScript
resources/views/
├── layouts/
│   ├── app.blade.php                 ← Layout utama (sidebar + topbar)
│   └── auth.blade.php                ← Layout halaman login
├── auth/
│   └── login.blade.php
├── dashboard/
│   ├── owner.blade.php
│   ├── head.blade.php
│   └── admin.blade.php
├── categories/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── employees/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── salary-history.blade.php
├── periods/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── salary-slips/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── show.blade.php
│   ├── bulk-create.blade.php
│   └── bulk-pdf.blade.php
├── emails/
│   ├── index.blade.php
│   ├── send.blade.php
│   ├── send-bulk.blade.php
│   └── slip-history.blade.php
├── reports/
│   ├── index.blade.php
│   ├── salary-summary.blade.php
│   ├── statistics.blade.php
│   └── employee.blade.php
├── activity-logs/
│   ├── index.blade.php
│   └── show.blade.php
├── profile/
│   └── index.blade.php
├── errors/
│   └── 403.blade.php
└── coming-soon.blade.php
routes/
└── web.php                           ← Semua route web

---

## 🗺️ Daftar Route

### Auth
| Method | URL | Deskripsi |
|---|---|---|
| GET | `/login` | Halaman login |
| POST | `/login` | Proses login |
| POST | `/logout` | Logout |

### Dashboard & Profil
| Method | URL | Deskripsi | Role |
|---|---|---|---|
| GET | `/dashboard` | Dashboard (redirect per role) | Semua |
| GET | `/profile` | Halaman profil | Semua |
| PUT | `/profile` | Update profil | Semua |
| POST | `/change-password` | Ganti password | Semua |

### Kategori Gaji
| Method | URL | Deskripsi | Role |
|---|---|---|---|
| GET | `/categories` | Daftar kategori | Owner |
| GET | `/categories/create` | Form tambah | Owner |
| POST | `/categories` | Simpan kategori | Owner |
| GET | `/categories/{id}/edit` | Form edit | Owner |
| PUT | `/categories/{id}` | Update kategori | Owner |
| DELETE | `/categories/{id}` | Hapus kategori | Owner |

### Karyawan
| Method | URL | Deskripsi | Role |
|---|---|---|---|
| GET | `/employees` | Daftar karyawan | Owner, Head |
| GET | `/employees/create` | Form tambah | Owner, Head |
| POST | `/employees` | Simpan karyawan | Owner, Head |
| GET | `/employees/{id}/edit` | Form edit | Owner, Head |
| PUT | `/employees/{id}` | Update karyawan | Owner, Head |
| DELETE | `/employees/{id}` | Hapus karyawan | Owner, Head |
| GET | `/employees/{id}/salary-history` | Riwayat gaji | Owner, Head |

### Periode Penggajian
| Method | URL | Deskripsi | Role |
|---|---|---|---|
| GET | `/periods` | Daftar periode | Owner, Head |
| GET | `/periods/create` | Form tambah | Owner, Head |
| POST | `/periods` | Simpan periode | Owner, Head |
| GET | `/periods/{id}/edit` | Form edit | Owner, Head |
| PUT | `/periods/{id}` | Update periode | Owner, Head |
| DELETE | `/periods/{id}` | Hapus periode | Owner, Head |
| PUT | `/periods/{id}/close` | Tutup periode | Owner, Head |
| PUT | `/periods/{id}/reopen` | Buka kembali | Owner, Head |

### Slip Gaji
| Method | URL | Deskripsi | Role |
|---|---|---|---|
| GET | `/salary-slips` | Daftar slip | Semua |
| GET | `/salary-slips/create` | Input single | Semua |
| POST | `/salary-slips` | Simpan slip | Semua |
| GET | `/salary-slips/bulk-create` | Input massal | Semua |
| POST | `/salary-slips/bulk-generate` | Generate bulk | Semua |
| GET | `/salary-slips/{id}` | Detail slip | Semua |
| GET | `/salary-slips/{id}/edit` | Form edit | Semua |
| PUT | `/salary-slips/{id}` | Update slip | Semua |
| DELETE | `/salary-slips/{id}` | Hapus slip | Semua |

### PDF
| Method | URL | Deskripsi | Role |
|---|---|---|---|
| GET | `/salary-slips/{id}/preview-pdf` | Preview PDF | Semua |
| GET | `/salary-slips/{id}/download-pdf` | Download PDF | Semua |
| POST | `/salary-slips/{id}/generate-pdf` | Generate PDF | Semua |
| POST | `/pdf/bulk-generate` | Bulk generate PDF | Semua |

### Email
| Method | URL | Deskripsi | Role |
|---|---|---|---|
| GET | `/emails` | Riwayat email | Semua |
| GET | `/emails/send/{slipId}` | Konfirmasi kirim | Semua |
| POST | `/emails/send/{slipId}` | Kirim email | Semua |
| GET | `/emails/send-bulk` | Form kirim massal | Semua |
| POST | `/emails/send-bulk` | Kirim massal | Semua |
| POST | `/emails/resend/{slipId}` | Kirim ulang | Semua |
| GET | `/emails/history/{slipId}` | Riwayat per slip | Semua |

### Laporan
| Method | URL | Deskripsi | Role |
|---|---|---|---|
| GET | `/reports` | Halaman laporan | Owner, Head |
| GET | `/reports/salary-summary` | Rekap per periode | Owner, Head |
| GET | `/reports/export-pdf` | Export PDF | Owner, Head |
| GET | `/reports/statistics` | Statistik tren | Owner, Head |
| GET | `/reports/employee/{id}` | Laporan karyawan | Owner, Head |

### Activity Log
| Method | URL | Deskripsi | Role |
|---|---|---|---|
| GET | `/activity-logs` | Daftar log | Owner |
| GET | `/activity-logs/{id}` | Detail log | Owner |

---

## 🎨 Panduan UI/UX

### Palet Warna
| Variabel | Nilai | Kegunaan |
|---|---|---|
| `--primary` | `#FFC107` | Warna utama (kuning) |
| `--primary-dark` | `#E6A800` | Hover state |
| `--primary-light` | `#FFF3CD` | Background highlight |
| `--sidebar-bg` | `#1A1A2E` | Background sidebar |
| `--white` | `#FFFFFF` | Background utama |

### Komponen Utama
- **Sidebar** — navigasi tetap di sisi kiri, menyesuaikan menu per role
- **Topbar** — judul halaman, badge role, tombol logout
- **Stat Cards** — kartu statistik dengan ikon berwarna
- **Table Custom** — tabel dengan hover effect dan stripe
- **Status Badge** — badge berwarna untuk status slip, periode, karyawan
- **Alert Custom** — notifikasi flash dengan auto-hide 4 detik

---

## 🌿 Git Branch Strategy

| Branch | Deskripsi |
|---|---|
| `main` | Branch utama, production-ready |
| `feature/setup-layout` | Setup project, layout, ApiService |
| `feature/authentication` | Login, logout, session management |
| `feature/dashboard` | Dashboard per role |
| `feature/salary-category` | Master kategori gaji |
| `feature/employee-management` | Manajemen karyawan |
| `feature/payroll-period` | Periode penggajian |
| `feature/salary-slip` | Input slip gaji single & bulk |
| `feature/pdf-generation` | Generate, preview, download PDF |
| `feature/email-distribution` | Distribusi email |
| `feature/reports` | Laporan & statistik |
| `feature/profile-activitylog` | Profil & activity log |
| `fix/salary-slip-form-improvements` | Perbaikan form slip gaji |

---

## 📝 Konvensi Commit
feat:    Fitur baru
fix:     Perbaikan bug
config:  Perubahan konfigurasi
refactor: Refactoring kode
docs:    Perubahan dokumentasi
style:   Perubahan tampilan/CSS

---

## 🔧 Perintah yang Sering Digunakan

```bash
# Jalankan server development
php artisan serve --port=8080

# Clear semua cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Lihat semua route
php artisan route:list

# Autoload ulang helpers
composer dump-autoload
```

---

## ⚠️ Catatan Penting

- File `.env` **tidak di-commit** ke GitHub, gunakan `.env.example` sebagai template
- Aplikasi ini **tidak memerlukan database** — semua data dari backend API
- Pastikan backend API sudah berjalan sebelum menjalankan frontend
- Gunakan **App Password** Gmail (bukan password utama) untuk konfigurasi SMTP di backend
- Untuk pengiriman email massal skala besar, gunakan **Resend** di backend (switch di `.env` backend)

---

## 👨‍💻 Developer

**Farhan Fauzan Azima**
- GitHub: [@farhanfauzanazima](https://github.com/farhanfauzanazima)
- Repository Frontend Website: [remunerationapplication](https://github.com/farhanfauzanazima/remunerationapplication)
- Repository Frontend Mobile: [remunerationapplication](https://github.com/farhanfauzanazima/mobileremunerationapplication)
- Repository Backend: [apiremunerationapplication](https://github.com/farhanfauzanazima/apiremunerationapplication)

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan pengembangan sistem remunerasi restoran.

---

*Dibuat dengan ❤️ menggunakan Laravel 12 & Bootstrap 5*