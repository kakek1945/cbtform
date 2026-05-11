# Form CBT Laravel + Google Form

Aplikasi CBT sekolah berbasis Laravel 13, Blade, Tailwind CSS, session authentication, role admin/siswa, PostgreSQL, dan Google Form iframe.

## Fitur Utama

- Login admin memakai username/email dan password.
- Login siswa memakai NIS/username dan password/token.
- Role middleware `role:admin` dan `role:siswa`.
- Dashboard siswa menampilkan ujian sesuai kelas.
- Halaman instruksi dengan checkbox persetujuan.
- Halaman ujian berisi iframe Google Form dengan prefilled URL.
- Timer berdasarkan `started_at + duration_minutes`, tetap akurat saat refresh.
- Auto-finish saat waktu habis.
- Logging pindah tab, keluar fullscreen, login, mulai ujian, buka ujian, selesai, waktu habis.
- Admin dashboard, CRUD siswa, CRUD ujian, monitoring, dan log aktivitas.

## Struktur Penting

- `app/Models/User.php`
- `app/Models/Exam.php`
- `app/Models/ExamSession.php`
- `app/Models/ActivityLog.php`
- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/StudentDashboardController.php`
- `app/Http/Controllers/ExamController.php`
- `app/Http/Controllers/ExamSessionController.php`
- `app/Http/Controllers/Admin*Controller.php`
- `app/Http/Middleware/EnsureUserHasRole.php`
- `database/migrations/*`
- `database/seeders/DatabaseSeeder.php`
- `resources/views/student/*`
- `resources/views/admin/*`
- `public/js/exam-security.js`

## Instalasi Dari Awal

1. Install dependency PHP.

```bash
composer install
```

2. Install dependency frontend dan build Tailwind.

```bash
npm install
npm run build
```

Untuk development, jalankan `npm run dev` bersamaan dengan `php artisan serve`.

3. Siapkan env.

```bash
copy .env.example .env
php artisan key:generate
```

4. Buat database PostgreSQL.

```sql
CREATE DATABASE cbt_exam;
```

5. Sesuaikan `.env`.

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=cbt_exam
DB_USERNAME=postgres
DB_PASSWORD=password_database
```

6. Jalankan migration dan seeder.

```bash
php artisan migrate --seed
```

7. Jalankan aplikasi.

```bash
php artisan serve
```

## Akun Demo

- Admin: `admin` / `password`
- Siswa XII IPA 1: `siswa001` / `password`
- Siswa XII IPA 1: `siswa002` / `password`
- Siswa XII IPS 1: `siswa003` / `password`

## Cara Menghubungkan Google Form

1. Buat Google Form.
2. Klik `Send`.
3. Pilih ikon link atau embed.
4. Untuk iframe, gunakan URL form yang bisa dibuka publik atau terbatas sesuai akun sekolah.
5. Masukkan URL itu ke field `Google Form URL` saat admin membuat ujian.

Jika ingin iframe langsung embedded, URL biasanya berbentuk:

```text
https://docs.google.com/forms/d/e/FORM_ID/viewform?embedded=true
```

## Prefilled Google Form URL Opsional

Secara default, akses ujian cukup dikontrol dari import peserta ujian. Siswa yang tidak masuk daftar peserta tidak dapat melihat atau mengerjakan ujian.

Prefilled Google Form hanya diperlukan jika sekolah ingin kolom Google Form seperti nama, NIS, kelas, dan nama ujian terisi otomatis dari data login siswa. Jika tidak diperlukan, biarkan field prefill kosong dan gunakan template upload peserta saja.

Jika suatu saat ingin mengaktifkan prefill:

1. Buka Google Form.
2. Klik menu titik tiga.
3. Pilih `Get pre-filled link`.
4. Isi contoh jawaban untuk field nama, NIS, kelas, dan nama ujian.
5. Klik `Get link`.
6. Dari URL hasil, ambil parameter seperti `entry.111111`, `entry.222222`, dan seterusnya.
7. Simpan parameter tersebut di kolom prefill ujian melalui database atau form admin lanjutan.

```text
prefill_name_field  = entry.111111
prefill_nis_field   = entry.222222
prefill_class_field = entry.333333
prefill_exam_field  = entry.444444
```

Aplikasi akan membuat URL final otomatis dari data siswa:

```text
google_form_url?entry.111111=Nama&entry.222222=NIS&entry.333333=Kelas&entry.444444=Nama%20Ujian
```

## Import Peserta Ujian Per Mapel/Ujian

Setiap ujian punya daftar peserta sendiri. Siswa hanya dapat melihat dan mengerjakan ujian jika username siswa itu masuk ke daftar peserta ujian tersebut.

Cara import:

1. Login sebagai admin.
2. Buka `Data Ujian`.
3. Klik `Edit` pada ujian/mapel yang dituju.
4. Pada bagian `Import Peserta Ujian`, upload file CSV.
5. Sistem akan membuat/memperbarui akun siswa dan menautkan siswa itu ke ujian tersebut.

Format CSV:

```csv
name,nis,class,username,email,password
Rani Permata,3001,XII IPA 1,rani3001,rani3001@example.com,password123
Yoga Saputra,3002,XII IPA 1,yoga3002,yoga3002@example.com,password123
```

Contoh file tersedia di:

```text
docs/sample-exam-participants.csv
```

Catatan:

- Kolom `username` wajib unik.
- Kolom `password` adalah token/password siswa untuk login.
- Jika `class` kosong, sistem memakai kelas dari ujian yang sedang diimport.
- Import memakai mode tambah/update, jadi tidak menghapus peserta lama dari ujian.

## Validasi Lokal Tanpa PostgreSQL Aktif

Jika PostgreSQL lokal belum berjalan, migrasi bisa divalidasi sementara memakai SQLite:

```powershell
$env:DB_CONNECTION='sqlite'
$env:DB_DATABASE='D:\LABOR-KOMPUTER\form-cbt\database\database.sqlite'
php artisan migrate:fresh --seed
```

## Testing dan Formatting

```bash
php artisan test
vendor/bin/pint
```

## Catatan Deployment Laravel + PostgreSQL

- Set `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL` sesuai domain.
- Pastikan ekstensi PHP `pdo_pgsql` aktif.
- Gunakan database user PostgreSQL khusus aplikasi, bukan superuser.
- Jalankan `php artisan migrate --force`.
- Jalankan `php artisan config:cache`, `route:cache`, dan `view:cache`.
- Jalankan `npm run build` sebelum deploy atau di pipeline CI/CD.
- Pastikan `storage` dan `bootstrap/cache` writable oleh web server.

Panduan gratis dengan Render/Oracle Cloud + Supabase tersedia di:

```text
docs/deployment-free.md
```
