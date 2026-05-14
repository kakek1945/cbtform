# Product Requirements Document (PRD)

## Ringkasan

Aplikasi CBT berbasis Laravel untuk pelaksanaan ujian sekolah menggunakan Google Form sebagai media pengerjaan soal. Sistem memiliki dua role utama, yaitu admin dan siswa. Admin membuat data ujian terlebih dahulu, kemudian mengimport data siswa menggunakan template CSV yang berisi kode ujian agar siswa otomatis terdaftar ke ujian yang sesuai.

## Tujuan

- Menyediakan platform CBT sederhana untuk sekolah dengan integrasi Google Form.
- Memastikan hanya siswa yang terdaftar sebagai peserta ujian yang dapat melihat dan mengerjakan ujian.
- Memudahkan admin mengelola ujian, siswa, peserta ujian, monitoring, log aktivitas, dan hasil ujian.
- Mengurangi proses manual pendaftaran peserta ujian melalui penggunaan `kode_ujian` pada template import siswa.

## Pengguna

- Admin sekolah atau operator CBT.
- Siswa peserta ujian.

## Alur Utama

1. Admin login ke dashboard admin.
2. Admin membuat data ujian terlebih dahulu melalui menu `Ujian`.
3. Admin mengisi `Kode Ujian` pada setiap ujian.
4. Admin masuk ke menu `Siswa`.
5. Admin download template CSV siswa.
6. Admin mengisi data siswa dan kolom `kode_ujian`.
7. Jika siswa mengikuti lebih dari satu ujian, kode ditulis dalam satu kolom dan dipisahkan dengan titik koma, contoh `MTK-XII;BINDO-XII`.
8. Admin import CSV siswa.
9. Sistem membuat atau memperbarui akun siswa berdasarkan `username`.
10. Sistem otomatis mendaftarkan siswa ke semua ujian yang kode ujiannya cocok.
11. Siswa login dan hanya melihat ujian yang sudah didaftarkan untuk dirinya.

## Scope

### In Scope

- Authentication berbasis session.
- Role `admin` dan `siswa`.
- Dashboard admin.
- Manajemen ujian.
- Manajemen siswa.
- Import siswa dari CSV dengan `kode_ujian`.
- Pendaftaran peserta ujian otomatis berdasarkan `kode_ujian`.
- Dashboard siswa berisi daftar ujian yang dapat diakses.
- Halaman instruksi ujian.
- Halaman ujian dengan iframe Google Form.
- Timer ujian berbasis sesi.
- Monitoring sesi ujian.
- Reset sesi ujian oleh admin.
- Log aktivitas.
- Sinkronisasi hasil ujian dari Google Sheets.

### Out of Scope

- Pembuatan soal langsung di aplikasi.
- Koreksi jawaban langsung di aplikasi selain sinkron hasil Google Sheets.
- Proctoring kamera atau screen recording.
- Integrasi SSO eksternal.
- Import peserta ujian dari halaman edit ujian.

## Kebutuhan Fungsional

### Authentication

- Admin dapat login menggunakan username atau email dan password.
- Siswa dapat login menggunakan username, NISN, atau email dan password/token.
- Sistem membatasi akses berdasarkan role.
- Admin tidak dapat mengakses halaman siswa.
- Siswa tidak dapat mengakses halaman admin.

### Dashboard Admin

- Menampilkan ringkasan jumlah ujian, siswa, sesi aktif, selesai, dan waktu habis.
- Menampilkan sesi ujian terbaru.
- Header navigasi admin berurutan: `Dashboard`, `Ujian`, `Siswa`, `Monitoring`, `Hasil Ujian`, `Log`.

### Manajemen Ujian

- Admin dapat membuat, mengedit, dan menghapus ujian.
- Data ujian minimal berisi nama ujian, kode ujian, mata pelajaran, kelas, Google Form URL, waktu mulai, waktu selesai, durasi, status aktif, izin mengulang, dan instruksi.
- `Kode Ujian` wajib diisi dan unik.
- Kode ujian digunakan pada template import siswa untuk mendaftarkan peserta.
- Halaman edit ujian tidak lagi memiliki fitur import peserta ujian.

### Manajemen Siswa

- Admin dapat menambah, mengedit, menghapus, dan reset password/token siswa.
- Data siswa menggunakan NISN, bukan NIS.
- Template CSV siswa menggunakan header `nisn`.
- Template CSV siswa minimal berisi `name`, `nisn`, `class`, `username`, `email`, `password`, dan `kode_ujian`.
- Import siswa wajib memiliki kolom `username` dan `kode_ujian`.
- Sistem tetap dapat membaca kolom lama `nis` sebagai fallback saat import, tetapi template resmi memakai `nisn`.
- Kolom `kode_ujian` dapat berisi lebih dari satu kode ujian.
- Pemisah kode ujian yang didukung: titik koma `;`, koma `,`, pipe `|`, atau baris baru.
- Jika kode ujian tidak ditemukan, baris tersebut dilewati dan sistem menampilkan jumlah baris yang dilewati.
- Tampilan master data siswa hanya menampilkan nama, NISN, username, dan daftar kode ujian yang diikuti.
- Daftar ujian yang diikuti pada master siswa cukup menampilkan kode ujian saja.

### Dashboard Siswa

- Siswa melihat nama, NISN, kelas, dan daftar ujian yang didaftarkan untuk dirinya.
- Ujian yang tampil bukan berdasarkan kelas saja, tetapi berdasarkan relasi peserta ujian.
- Status ujian yang ditampilkan meliputi belum mulai, tersedia, berlangsung, selesai, dan waktu habis.
- Tombol mulai ujian hanya aktif ketika ujian tersedia sesuai jadwal.

### Instruksi Ujian

- Menampilkan detail ujian, durasi, jadwal, dan instruksi.
- Siswa wajib mencentang persetujuan instruksi sebelum mulai.
- Saat mulai, sistem membuat atau melanjutkan `exam_session`.

### Halaman Ujian

- Menampilkan Google Form melalui iframe.
- Google Form URL diambil dari data ujian.
- Sistem mendukung prefilled URL berdasarkan data siswa seperti nama, NISN, kelas, dan nama ujian.
- Timer dihitung dari `started_at + duration_minutes`.
- Timer tetap akurat setelah halaman direfresh.
- Jika waktu habis, status sesi berubah menjadi `waktu_habis`.
- Siswa dapat klik tombol selesai untuk mengubah status sesi menjadi `selesai`.

### Proteksi dan Monitoring

- Sistem mencatat perpindahan tab.
- Sistem mencatat keluar fullscreen.
- Sistem mencatat login, mulai ujian, buka ujian, selesai ujian, waktu habis, reset sesi, dan aktivitas penting lain.
- Admin dapat memantau sesi ujian berdasarkan status, ujian, atau tanggal.
- Admin dapat reset sesi ujian siswa dari halaman monitoring.

### Hasil Ujian

- Admin dapat memilih ujian dan melakukan sinkronisasi hasil dari Google Sheets.
- Sistem membaca spreadsheet publik atau spreadsheet yang dapat diakses melalui link.
- Sistem mencocokkan hasil dengan siswa berdasarkan username, NISN, email, atau profil siswa.
- Sistem menyimpan hasil ke tabel hasil ujian.
- Admin dapat melihat dan menghapus hasil ujian.

## Kebutuhan Data

### Users

- `name`
- `nisn`
- `class`
- `username`
- `email`
- `password`
- `role`

### Exams

- `title`
- `code`
- `subject`
- `class`
- `google_form_url`
- `result_spreadsheet_id`
- `result_sheet_name`
- `prefill_name_field`
- `prefill_nisn_field`
- `prefill_class_field`
- `prefill_exam_field`
- `start_time`
- `end_time`
- `duration_minutes`
- `is_active`
- `allow_retake`
- `instructions`

### Exam Participants

- `exam_id`
- `user_id`

### Exam Sessions

- `exam_id`
- `user_id`
- `started_at`
- `finished_at`
- `status`
- `tab_switch_count`
- `fullscreen_exit_count`
- `ip_address`
- `user_agent`

### Exam Results

- `exam_id`
- `user_id`
- `identifier`
- `student_name`
- `nisn`
- `class`
- `score`
- `max_score`
- `percentage`
- `submitted_at`
- `imported_at`
- `raw_payload`

## Kebutuhan Non-Fungsional

- Aplikasi harus berjalan di Laravel dengan Blade dan Tailwind CSS.
- Database menggunakan PostgreSQL pada produksi dan dapat menggunakan konfigurasi test sesuai Laravel.
- Import CSV harus mendukung delimiter koma, titik koma, dan tab.
- UI harus tetap responsif pada desktop dan mobile.
- Sistem harus menggunakan middleware role untuk membatasi akses.
- Password harus disimpan menggunakan hashing Laravel.
- Aktivitas penting harus tercatat untuk audit.

## Kriteria Sukses

- Admin dapat membuat ujian dengan kode unik.
- Admin dapat mengimport siswa melalui template CSV dengan NISN dan kode ujian.
- Siswa otomatis terdaftar ke semua ujian yang kodenya ditulis pada CSV.
- Master data siswa hanya menampilkan nama, NISN, username, dan kode ujian yang diikuti.
- Import peserta ujian tidak tersedia lagi di halaman edit ujian.
- Siswa hanya melihat ujian yang didaftarkan untuk dirinya.
- Timer ujian berjalan akurat walaupun halaman direfresh.
- Admin dapat memonitor, reset sesi, dan sinkron hasil ujian.

## Risiko

- Kesalahan penulisan `kode_ujian` pada CSV menyebabkan siswa tidak terdaftar ke ujian.
- Google Form atau Google Sheets yang tidak publik dapat gagal ditampilkan atau gagal disinkronkan.
- Proteksi browser seperti fullscreen dan disable shortcut hanya bersifat dasar dan tidak setara proctoring penuh.
- Data hasil Google Sheets perlu format kolom yang konsisten agar pencocokan siswa akurat.

## Catatan Implementasi Terakhir

- Istilah NIS sudah diganti menjadi NISN pada database dan tampilan utama.
- Template import siswa memakai `nisn`.
- Import siswa menjadi satu-satunya alur pendaftaran peserta ujian.
- Halaman edit ujian hanya digunakan untuk mengelola data ujian, bukan import peserta.
- Ikon aksi sudah disesuaikan agar tidak memakai gear untuk aksi yang bukan setting.
