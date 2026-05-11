# Deployment Gratis: Laravel + Supabase PostgreSQL

Panduan ini menyiapkan aplikasi Form CBT agar bisa dijalankan gratis memakai:

- **Render Free Web Service** untuk aplikasi Laravel.
- **Supabase Free PostgreSQL** untuk database.

Konfigurasi ini juga bisa dipakai di VPS gratis seperti Oracle Cloud Always Free karena project sudah memiliki `Dockerfile`.

## 1. Siapkan Supabase

1. Buat project baru di Supabase.
2. Buka **Project Settings > Database**.
3. Ambil connection detail PostgreSQL.
4. Untuk platform serverless/container seperti Render, gunakan connection pooler jika tersedia.

Contoh env database:

```env
DB_CONNECTION=pgsql
DB_HOST=aws-xxx.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.xxxxx
DB_PASSWORD=password-supabase
```

Catatan:

- Port `5432` biasanya direct connection.
- Port `6543` biasanya pooler Supabase.
- Gunakan credential dari dashboard Supabase kamu, jangan contoh di atas.

## 2. Siapkan APP_KEY

Di komputer lokal, jalankan:

```bash
php artisan key:generate --show
```

Salin hasilnya, misalnya:

```env
APP_KEY=base64:xxxxxxxxxxxxxxxx
```

## 3. Deploy ke Render Free

1. Push project ke GitHub.
2. Buka Render.
3. Pilih **New > Blueprint**.
4. Hubungkan repository project.
5. Render akan membaca `render.yaml`.
6. Isi environment variable yang `sync: false`.

Environment yang wajib diisi:

```env
APP_KEY=base64:hasil-key-generate
APP_URL=https://nama-app.onrender.com
DB_HOST=host-supabase
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=user-supabase
DB_PASSWORD=password-supabase
```

Environment yang sudah disiapkan di `render.yaml`:

```env
APP_ENV=production
APP_DEBUG=false
LOG_CHANNEL=stderr
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
RUN_MIGRATIONS=true
```

## 4. Migration Otomatis

Container menjalankan migration otomatis saat start jika:

```env
RUN_MIGRATIONS=true
```

Setelah deploy stabil, boleh tetap `true` karena migration Laravel aman/idempotent. Jika ingin lebih konservatif, ubah ke `false` setelah semua tabel terbentuk.

## 5. Akses Aplikasi

Setelah deploy selesai:

```text
https://nama-app.onrender.com/login
```

## 6. Catatan Free Tier

Render Free dapat sleep saat tidak ada trafik. Akses pertama setelah idle bisa lambat. Untuk penggunaan sekolah serius, opsi lebih stabil adalah:

- Oracle Cloud Always Free + Docker.
- VPS murah + Supabase.
- Laravel Cloud jika nanti ingin layanan berbayar yang paling cocok untuk Laravel.

## 7. Deploy ke Oracle Cloud Always Free

Jika memakai VPS Oracle:

1. Install Docker dan Docker Compose.
2. Clone repository.
3. Buat file `.env` production.
4. Build dan jalankan container:

```bash
docker build -t form-cbt .
docker run -d \
  --name form-cbt \
  -p 8080:8080 \
  --env-file .env \
  -e RUN_MIGRATIONS=true \
  form-cbt
```

5. Pasang Nginx reverse proxy dan SSL Let's Encrypt jika memakai domain.

## 8. Hal Yang Tetap Butuh Internet

Fitur berikut tetap butuh internet:

- Google Form iframe.
- Sinkron otomatis hasil Google Sheets.
- Logo login jika masih memakai URL eksternal.

Jika ingin logo offline, simpan gambar ke `public/images` dan ubah Blade memakai `asset('images/logo-smp3.jpg')`.
