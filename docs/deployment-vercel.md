# Deployment Vercel + Neon/Vercel Postgres

> Catatan: Laravel di Vercel memakai runtime PHP komunitas (`vercel-php`). Ini cocok untuk demo/prototype. Untuk production Laravel penuh, server PHP biasa tetap lebih ideal.

## 1. Hubungkan GitHub ke Vercel

1. Push repository ini ke GitHub.
2. Buka Vercel.
3. Pilih **Add New Project**.
4. Import repository GitHub `cbtform`.
5. Vercel akan membaca `vercel.json`.

## 2. Buat Database Neon Lewat Vercel

1. Di project Vercel, buka tab **Storage**.
2. Pilih **Neon**.
3. Buat database baru.
4. Vercel akan menambahkan env database seperti `POSTGRES_URL`.

Laravel project ini sudah mendukung:

```env
DB_URL
DATABASE_URL
POSTGRES_URL
```

Jadi jika Vercel/Neon memberi `POSTGRES_URL`, Laravel bisa langsung membacanya.

## 3. Environment Variables Wajib

Tambahkan di Vercel Project Settings > Environment Variables:

```env
APP_NAME=Form CBT
APP_ENV=production
APP_DEBUG=false
APP_URL=https://nama-project.vercel.app
APP_TIMEZONE=Asia/Jakarta
LOG_CHANNEL=stderr

APP_KEY=base64:ISI_DARI_php_artisan_key_generate_show

DB_CONNECTION=pgsql
DB_SSLMODE=require

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

APP_STORAGE_PATH=/tmp/form-cbt/storage
VIEW_COMPILED_PATH=/tmp/form-cbt/storage/framework/views
```

Jika Vercel Neon belum otomatis memberi `POSTGRES_URL`, isi salah satu:

```env
DB_URL=postgresql://user:password@host/database?sslmode=require
DATABASE_URL=postgresql://user:password@host/database?sslmode=require
POSTGRES_URL=postgresql://user:password@host/database?sslmode=require
```

## 4. Generate APP_KEY

Di lokal:

```bash
php artisan key:generate --show
```

Salin hasilnya ke env `APP_KEY` di Vercel.

## 5. Jalankan Migration

Karena Vercel serverless tidak ideal untuk menjalankan migration otomatis saat request, jalankan migration dari lokal ke database Neon:

```powershell
$env:DB_CONNECTION="pgsql"
$env:DB_URL="postgresql://user:password@host/database?sslmode=require"
php artisan migrate --force
```

Atau isi env database lengkap:

```powershell
$env:DB_CONNECTION="pgsql"
$env:DB_HOST="host"
$env:DB_PORT="5432"
$env:DB_DATABASE="database"
$env:DB_USERNAME="user"
$env:DB_PASSWORD="password"
$env:DB_SSLMODE="require"
php artisan migrate --force
```

## 6. Deploy

Setelah env lengkap:

```bash
vercel --prod
```

Atau deploy otomatis dari GitHub saat push ke branch `main`.

## 7. Batasan Vercel Untuk Laravel Ini

- Upload CSV tetap bisa, tetapi file hanya diproses saat request dan tidak disimpan permanen.
- Jangan mengandalkan storage lokal Vercel untuk file permanen.
- Queue worker dan scheduler tidak natural di Vercel.
- Google Form dan Google Sheets sync tetap butuh internet.
- Untuk production sekolah yang serius, Render/Oracle/VPS lebih stabil.
