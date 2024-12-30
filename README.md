<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Tentang Laravel

Laravel adalah framework aplikasi web dengan sintaks yang ekspresif dan elegan. Framework ini dirancang untuk membuat proses pengembangan menjadi lebih menyenangkan dan produktif. Laravel mempermudah tugas-tugas umum yang sering digunakan dalam banyak proyek web, seperti:

- [Routing sederhana dan cepat](https://laravel.com/docs/routing).
- [Container dependency injection yang kuat](https://laravel.com/docs/container).
- Penyimpanan untuk [sesi](https://laravel.com/docs/session) dan [cache](https://laravel.com/docs/cache) dengan berbagai back-end.
- [ORM basis data](https://laravel.com/docs/eloquent) yang ekspresif dan intuitif.
- [Migrasi skema](https://laravel.com/docs/migrations) yang tidak tergantung pada jenis basis data.
- [Proses pekerjaan latar belakang](https://laravel.com/docs/queues) yang andal.
- [Broadcasting acara secara real-time](https://laravel.com/docs/broadcasting).

Laravel menyediakan alat-alat yang diperlukan untuk membangun aplikasi besar dan kuat.

## Cara Menggunakan Proyek Ini

### Prasyarat
Pastikan Anda sudah menginstal:
- **PHP versi 8.1 atau lebih tinggi**
- **Composer**
- **Basis Data** (contoh: MySQL, MariaDB)

### Langkah-Langkah

1. **Clone repository ini**
   ```bash
   git clone https://github.com/liebert193323/kokakos.git
   cd kokakos
2. **Install dependencies**
   ```bash
   composer install
3. **Buat file lingkungan**
   ```bash
   cp .env.example .env
4. **Atur konfigurasi file .env:
   Atur kredensial basis data Anda**
   ```bash
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=username_anda
   DB_PASSWORD=password_anda
5. **Generate aplikasi key**
   ```bash
   php artisan key:generate
6. **Jalankan migrasi basis data**
   ```bash
   php artisan key:generate
6. **Jalankan Aplikasi **
   ```bash
   php artisan serve
   

