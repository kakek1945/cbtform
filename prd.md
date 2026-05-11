Kamu adalah senior full-stack Laravel developer. Saya ingin membuat aplikasi CBT ujian siswa berbasis web yang terhubung dengan Google Form.

Buatkan aplikasi menggunakan:
- Laravel 11 atau versi terbaru yang stabil
- Blade Template atau Laravel Breeze
- Tailwind CSS
- PostgreSQL
- Laravel Migration, Model, Controller, Middleware, Route, dan Seeder
- Authentication berbasis session
- Role-based access untuk siswa dan admin

Aplikasi harus memiliki fitur berikut:

1. Authentication
- Login siswa menggunakan NIS/username dan password/token.
- Login admin menggunakan email/username dan password.
- Role user terdiri dari admin dan siswa.
- Siswa tidak bisa mengakses halaman ujian tanpa login.
- Admin tidak bisa mengakses halaman siswa jika tidak diperlukan.
- Middleware untuk membatasi akses berdasarkan role.

2. Dashboard Siswa
- Setelah login, siswa masuk ke dashboard.
- Tampilkan nama siswa, NIS, kelas, dan daftar ujian yang tersedia.
- Daftar ujian hanya menampilkan ujian sesuai kelas siswa.
- Tampilkan status ujian:
  - Belum mulai
  - Tersedia
  - Sedang berlangsung
  - Selesai
  - Waktu habis
- Tombol “Mulai Ujian” hanya aktif jika jadwal ujian sedang berlangsung.
- Jika siswa sudah selesai ujian, tombol ujian dinonaktifkan.

3. Halaman Instruksi Ujian
- Tampilkan detail ujian:
  - Nama ujian
  - Mata pelajaran
  - Kelas
  - Durasi
  - Jadwal mulai dan selesai
- Tampilkan instruksi ujian sebelum siswa mulai.
- Tambahkan checkbox “Saya telah membaca dan memahami aturan ujian”.
- Tombol “Mulai Ujian” aktif setelah checkbox dicentang.
- Saat tombol diklik, sistem membuat atau melanjutkan exam session.
- Timer dimulai saat exam session dibuat.

4. Halaman Ujian
- Tampilkan header ujian berisi:
  - Nama siswa
  - NIS
  - Kelas
  - Nama ujian
  - Mata pelajaran
- Tampilkan timer countdown yang jelas di bagian atas.
- Tampilkan Google Form menggunakan iframe.
- Link Google Form diambil dari data ujian yang dibuat admin.
- Buat fungsi untuk menghasilkan prefilled Google Form URL berdasarkan data siswa, seperti nama, NIS, kelas, dan nama ujian.
- Timer harus tetap berjalan walaupun halaman direfresh.
- Timer dihitung berdasarkan started_at dan duration_minutes dari exam session.
- Jika waktu habis:
  - Google Form disembunyikan
  - Status session berubah menjadi waktu_habis
  - Siswa diarahkan ke halaman selesai
- Tambahkan tombol “Selesai Ujian”.
- Saat tombol diklik, status session berubah menjadi selesai.
- Catat waktu selesai di database.

5. Proteksi dan Monitoring Ujian
- Tambahkan deteksi pindah tab menggunakan JavaScript visibilitychange.
- Setiap siswa pindah tab, kirim request AJAX ke Laravel untuk mencatat pelanggaran.
- Simpan jumlah pindah tab di exam_sessions.
- Simpan detail aktivitas di activity_logs.
- Tambahkan fitur fullscreen mode saat ujian dimulai.
- Tambahkan peringatan jika siswa keluar dari fullscreen.
- Disable klik kanan dan shortcut umum seperti Ctrl+C, Ctrl+V, Ctrl+U, dan F12 sebagai proteksi dasar.
- Catat:
  - waktu login
  - waktu mulai ujian
  - waktu selesai
  - jumlah pindah tab
  - IP address
  - user agent

6. Halaman Selesai Ujian
- Tampilkan pesan bahwa ujian telah selesai.
- Tampilkan:
  - nama siswa
  - nama ujian
  - waktu mulai
  - waktu selesai
  - status ujian
  - jumlah pelanggaran pindah tab
- Sediakan tombol kembali ke dashboard.

7. Dashboard Admin
- Admin dapat melihat ringkasan:
  - jumlah siswa
  - jumlah ujian
  - jumlah siswa sedang ujian
  - jumlah siswa selesai ujian
  - jumlah siswa waktu habis
- Admin dapat mengelola data siswa.
- Admin dapat mengelola data ujian.
- Admin dapat melihat monitoring ujian secara real-time atau semi real-time.
- Admin dapat melihat log aktivitas siswa.

8. Manajemen Siswa
Admin dapat:
- Tambah siswa
- Edit siswa
- Hapus siswa
- Import siswa dari CSV/Excel jika memungkinkan
- Reset password/token siswa

Data siswa:
- nama
- NIS
- kelas
- username
- password/token
- role

9. Manajemen Ujian
Admin dapat membuat, mengedit, menghapus, dan mengaktifkan/nonaktifkan ujian.

Data ujian:
- nama ujian
- mata pelajaran
- kelas
- link Google Form
- field prefilled Google Form untuk nama siswa
- field prefilled Google Form untuk NIS
- field prefilled Google Form untuk kelas
- field prefilled Google Form untuk nama ujian
- tanggal dan jam mulai
- tanggal dan jam selesai
- durasi ujian dalam menit
- status aktif/nonaktif
- opsi apakah siswa boleh mengulang ujian atau tidak

10. Monitoring Admin
Admin dapat melihat tabel monitoring berisi:
- nama siswa
- NIS
- kelas
- nama ujian
- status ujian
- waktu mulai
- waktu selesai
- sisa waktu
- jumlah pindah tab
- IP address
- user agent

Tambahkan fitur filter berdasarkan:
- ujian
- kelas
- status
- tanggal

11. Log Aktivitas
Buat halaman log aktivitas yang mencatat:
- siswa login
- siswa mulai ujian
- siswa membuka halaman ujian
- siswa pindah tab
- siswa keluar fullscreen
- siswa klik selesai ujian
- waktu habis
- admin membuat atau mengubah ujian

12. Struktur Database PostgreSQL
Buat migration Laravel yang kompatibel dengan PostgreSQL untuk tabel berikut:

users:
- id bigserial primary key
- name varchar
- nis varchar nullable unique
- class varchar nullable
- username varchar unique
- email varchar nullable unique
- password varchar
- role varchar check value: admin atau siswa
- created_at timestamp
- updated_at timestamp

exams:
- id bigserial primary key
- title varchar
- subject varchar
- class varchar
- google_form_url text
- prefill_name_field varchar nullable
- prefill_nis_field varchar nullable
- prefill_class_field varchar nullable
- prefill_exam_field varchar nullable
- start_time timestamp
- end_time timestamp
- duration_minutes integer
- is_active boolean default true
- allow_retake boolean default false
- created_at timestamp
- updated_at timestamp

exam_sessions:
- id bigserial primary key
- user_id foreign key references users(id) on delete cascade
- exam_id foreign key references exams(id) on delete cascade
- started_at timestamp nullable
- finished_at timestamp nullable
- status varchar check value: belum_mulai, berlangsung, selesai, waktu_habis
- tab_switch_count integer default 0
- fullscreen_exit_count integer default 0
- ip_address varchar nullable
- user_agent text nullable
- created_at timestamp
- updated_at timestamp

activity_logs:
- id bigserial primary key
- user_id foreign key nullable references users(id) on delete set null
- exam_id foreign key nullable references exams(id) on delete set null
- exam_session_id foreign key nullable references exam_sessions(id) on delete set null
- activity_type varchar
- description text nullable
- ip_address varchar nullable
- user_agent text nullable
- created_at timestamp
- updated_at timestamp

Catatan khusus PostgreSQL:
- Hindari enum database native jika tidak diperlukan; gunakan string/varchar dengan validasi Laravel agar mudah migrasi.
- Gunakan constrained() dan cascadeOnDelete() pada migration Laravel.
- Gunakan nullable()->unique() dengan hati-hati untuk kolom email dan NIS.
- Pastikan konfigurasi .env menggunakan PostgreSQL:
  DB_CONNECTION=pgsql
  DB_HOST=127.0.0.1
  DB_PORT=5432
  DB_DATABASE=cbt_exam
  DB_USERNAME=postgres
  DB_PASSWORD=password_database

13. Model dan Relasi
Buat model Laravel:
- User
- Exam
- ExamSession
- ActivityLog

Relasi:
- User hasMany ExamSession
- Exam hasMany ExamSession
- ExamSession belongsTo User
- ExamSession belongsTo Exam
- ExamSession hasMany ActivityLog
- ActivityLog belongsTo User
- ActivityLog belongsTo Exam
- ActivityLog belongsTo ExamSession

14. Controller
Buat controller:
- AuthController
- StudentDashboardController
- ExamController
- ExamSessionController
- AdminDashboardController
- AdminStudentController
- AdminExamController
- AdminMonitoringController
- ActivityLogController

15. Route
Buat route Laravel dengan pembagian:
- route guest untuk login
- route siswa dengan middleware auth dan role:siswa
- route admin dengan middleware auth dan role:admin

Contoh route:
- GET /login
- POST /login
- POST /logout

Siswa:
- GET /dashboard
- GET /exam/{exam}/instruction
- POST /exam/{exam}/start
- GET /exam/{exam}/session/{session}
- POST /exam/session/{session}/finish
- POST /exam/session/{session}/tab-switch
- POST /exam/session/{session}/fullscreen-exit

Admin:
- GET /admin/dashboard
- CRUD /admin/students
- CRUD /admin/exams
- GET /admin/monitoring
- GET /admin/activity-logs

16. UI/UX
Gunakan Blade dan Tailwind CSS dengan tampilan:
- modern
- sederhana
- responsif
- cocok untuk sekolah
- mudah digunakan oleh siswa dan guru

Halaman yang harus dibuat:
- login
- dashboard siswa
- instruksi ujian
- halaman ujian
- halaman selesai
- dashboard admin
- data siswa
- data ujian
- monitoring
- log aktivitas

17. JavaScript Ujian
Buat JavaScript untuk:
- countdown timer
- deteksi pindah tab
- deteksi keluar fullscreen
- disable klik kanan
- disable shortcut tertentu
- redirect otomatis saat waktu habis
- AJAX request ke endpoint Laravel untuk mencatat pelanggaran

18. Seeder
Buat seeder untuk:
- admin default
- beberapa siswa dummy
- beberapa ujian dummy dengan link Google Form dummy

19. Security
Pastikan:
- password menggunakan hash Laravel
- route terlindungi middleware auth
- CSRF token aktif
- validasi form lengkap
- siswa tidak bisa membuka ujian kelas lain
- siswa tidak bisa membuka ujian di luar jadwal
- siswa tidak bisa mengulang ujian jika allow_retake false
- admin hanya bisa mengakses halaman admin

20. Output yang saya inginkan:
- Struktur folder project
- Konfigurasi .env PostgreSQL
- Migration lengkap yang kompatibel dengan PostgreSQL
- Model lengkap
- Controller lengkap
- Middleware role
- Route web.php lengkap
- Blade view lengkap
- JavaScript ujian
- Seeder dummy
- Instruksi instalasi dari awal sampai aplikasi berjalan
- Contoh command artisan yang harus dijalankan
- Penjelasan cara menghubungkan Google Form
- Penjelasan cara membuat prefilled Google Form URL
- Catatan deployment Laravel dengan PostgreSQL

Pastikan kode yang diberikan rapi, scalable, mudah dipahami, dan siap dikembangkan untuk kebutuhan ujian sekolah berbasis CBT.
