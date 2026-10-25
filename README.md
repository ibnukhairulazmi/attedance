# PresensiApp

Sistem presensi akademik berbasis **QR Code dinamis** dan **validasi lokasi GPS** untuk mencegah kecurangan titip absen di lingkungan perkuliahan.

![Flutter](https://img.shields.io/badge/Flutter-02569B?logo=flutter&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel_12-FF2D20?logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-06B6D4?logo=tailwindcss&logoColor=white)

> Project ini dikembangkan sebagai bagian dari skripsi Teknologi Informasi, Universitas Amikom Purwokerto.

## Latar Belakang

Presensi manual dan presensi digital sederhana masih rentan terhadap praktik titip absen. PresensiApp menerapkan **autentikasi dua lapis** sehingga presensi hanya sah jika mahasiswa:

1. memindai **QR Code dinamis** yang valid dan belum kedaluwarsa, dan
2. berada **dalam radius lokasi perkuliahan** (geofencing 10 meter).

## Fitur

### Mahasiswa (Aplikasi Mobile Flutter)

- Pemindaian QR Code untuk presensi
- Validasi lokasi otomatis menggunakan GPS
- Riwayat presensi per mata kuliah

### Dosen (Dashboard Web Laravel)

- Pembuatan dan pengelolaan sesi presensi
- QR Code dinamis yang berganti secara berkala
- Pemantauan kehadiran secara real-time dengan penghitung status
- Pengubahan status kehadiran mahasiswa secara manual
- Penutupan sesi otomatis melalui Laravel Scheduler

### Keamanan

- Token QR ditandatangani dengan **HMAC-SHA256**
- Validasi jarak dengan **formula Haversine**
- Enkripsi data dengan **AES-256-CBC**
- Hashing password dengan **bcrypt**
- **Rate limiting** pada endpoint API

## Arsitektur

```
┌─────────────────┐      REST API      ┌──────────────────┐      ┌─────────┐
│  Flutter App    │ ◄────────────────► │  Laravel 12      │ ◄──► │  MySQL  │
│  (Mahasiswa)    │                    │  Backend + Web   │      └─────────┘
└─────────────────┘                    │  (Dosen)         │
                                       └──────────────────┘
```

## Teknologi

| Komponen  | Teknologi                        |
| --------- | -------------------------------- |
| Mobile    | Flutter                          |
| Backend   | Laravel 12, REST API             |
| Dashboard | Blade, Tailwind CSS              |
| Database  | MySQL                            |
| Keamanan  | HMAC-SHA256, AES-256-CBC, bcrypt |
| Geolokasi | GPS, Haversine Formula           |

## Cara Kerja Presensi

1. Dosen membuka sesi presensi, lalu sistem menghasilkan QR Code dinamis bertanda tangan HMAC-SHA256.
2. Mahasiswa memindai QR Code melalui aplikasi mobile.
3. Aplikasi mengirim token QR beserta koordinat GPS ke server.
4. Server memverifikasi tanda tangan dan masa berlaku token, lalu menghitung jarak mahasiswa ke lokasi kelas dengan formula Haversine.
5. Presensi tercatat jika token valid dan jarak berada dalam radius yang diizinkan.

## Instalasi

### Prasyarat

- PHP [versi] dan Composer
- Node.js dan npm
- MySQL / MariaDB (contoh: XAMPP)
- Flutter SDK [versi]

### Backend (Laravel)

```bash
git clone https://github.com/ibnuazmi05/attendance.git
cd [folder-backend]

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
```

Atur koneksi database di file `.env`:

```env
DB_DATABASE=[nama_database]
DB_USERNAME=[username]
DB_PASSWORD=[password]
```

Jalankan migrasi dan server:

```bash
php artisan migrate --seed
php artisan serve
```

Untuk penutupan sesi otomatis, jalankan scheduler:

```bash
php artisan schedule:work
```

### Aplikasi Mobile (Flutter)

```bash
cd [folder-mobile]
flutter pub get
```

Atur alamat API di `[lokasi file konfigurasi]`, lalu jalankan:

```bash
flutter run
```

## Keterbatasan

- Akurasi GPS di dalam ruangan dapat menurun.
- Sistem belum sepenuhnya menangani GPS spoofing (mock location).

## Pengembangan Selanjutnya

- Deteksi mock location dan manipulasi GPS
- Dukungan verifikasi tambahan untuk area dengan sinyal GPS lemah

## Pengembang

**Ibnu Khairul Azmi**
