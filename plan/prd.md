# Product Requirements Document (PRD)

## Ringkasan

Aplikasi CBT sekolah berbasis Laravel, Blade, Tailwind CSS, dan Google Form. Aplikasi menyediakan portal admin untuk mengelola ujian, siswa, peserta, monitoring sesi, log aktivitas, dan hasil ujian dari Google Sheets. Siswa mengerjakan soal melalui Google Form yang ditampilkan dalam iframe, sementara aplikasi mengatur akses, timer, sesi ujian, peringatan submit, dan sinkronisasi nilai.

## Tujuan Produk

- Menyediakan platform CBT ringan untuk sekolah tanpa membangun editor soal sendiri.
- Memastikan siswa hanya melihat ujian yang memang didaftarkan sebagai peserta.
- Mengurangi kesalahan identitas siswa dengan prefilled username pada Google Form.
- Memberi admin kontrol terhadap data ujian, data siswa, sesi ujian, log aktivitas, dan hasil nilai.
- Membantu siswa mengingat bahwa nilai hanya masuk jika Google Form sudah dikirim.

## Pengguna

- Admin sekolah atau operator CBT.
- Siswa peserta ujian.

## Role dan Hak Akses

### Admin

- Login ke dashboard admin.
- Mengelola ujian.
- Mengelola siswa.
- Import siswa dan pendaftaran peserta ujian via CSV.
- Melihat monitoring sesi ujian.
- Reset sesi ujian.
- Melihat log aktivitas.
- Sinkronisasi dan download hasil ujian.
- Menghapus hasil nilai.

### Siswa

- Login ke dashboard siswa.
- Melihat ujian yang didaftarkan untuk dirinya.
- Membaca instruksi ujian.
- Memulai atau melanjutkan sesi ujian.
- Mengerjakan Google Form dalam iframe.
- Kembali ke dashboard setelah jawaban Google Form terdeteksi terkirim.

## Alur Utama Admin

1. Admin login.
2. Admin membuat ujian melalui menu `Ujian`.
3. Admin mengisi data ujian, termasuk `Kode Ujian`, Google Form URL, jadwal, durasi, instruksi, dan konfigurasi hasil.
4. Jika ingin username otomatis terisi di Google Form, admin mengisi `Field Username Google Form` dengan parameter `entry.xxxxx` dari pre-filled link Google Form.
5. Admin dapat memakai short URL `forms.gle`; aplikasi akan mencoba resolve ke URL asli Google Form saat membuat prefilled URL.
6. Admin mengelola siswa melalui menu `Siswa`.
7. Admin dapat import CSV siswa dengan kolom `kode_ujian`.
8. Sistem membuat atau memperbarui akun siswa berdasarkan `username`.
9. Sistem mendaftarkan siswa ke ujian yang kode ujiannya cocok.
10. Admin memantau sesi ujian melalui menu `Monitoring`.
11. Admin menyinkronkan hasil dari Google Sheets melalui menu `Hasil Ujian`.

## Alur Utama Siswa

1. Siswa login menggunakan username, NISN, atau email dan password/token.
2. Siswa melihat daftar ujian yang didaftarkan untuk dirinya.
3. Siswa membuka instruksi ujian.
4. Siswa mencentang persetujuan instruksi.
5. Sistem membuat atau melanjutkan sesi ujian.
6. Siswa mengerjakan soal pada Google Form di iframe.
7. Aplikasi mengisi username ke Google Form jika parameter prefill tersedia atau bisa dideteksi.
8. Saat sisa waktu 3 menit, aplikasi menampilkan popup peringatan besar agar siswa segera klik `Kirim` di Google Form.
9. Setelah Google Form terkirim, aplikasi menampilkan popup `Kembali ke Dashboard Siswa`.
10. Jika waktu habis sebelum siswa klik `Kirim` di Google Form, sesi berubah menjadi `waktu_habis`, tetapi nilai tidak masuk karena Google Form belum menyimpan jawaban.

## Scope

### In Scope

- Authentication berbasis session.
- Role `admin` dan `siswa`.
- Dashboard admin.
- Dashboard siswa.
- CRUD ujian.
- CRUD siswa.
- Reset password/token siswa.
- Import siswa dari CSV.
- Pendaftaran peserta ujian otomatis berdasarkan `kode_ujian`.
- Halaman instruksi ujian.
- Halaman ujian dengan iframe Google Form.
- Prefilled Google Form untuk username dan field opsional lain.
- Timer ujian berbasis sesi.
- Popup peringatan submit saat sisa waktu 3 menit.
- Popup kembali dashboard setelah submit Google Form terdeteksi.
- Pencatatan pindah tab dan aktivitas penting.
- Monitoring sesi ujian.
- Reset sesi oleh admin.
- Log aktivitas.
- Sinkronisasi hasil dari Google Sheets.
- Download hasil CSV.
- Hapus nilai pada daftar hasil ujian.

### Out of Scope

- Pembuatan soal langsung di aplikasi.
- Penyimpanan jawaban sementara dari Google Form.
- Auto-submit Google Form saat waktu habis.
- Koreksi jawaban selain hasil yang tersedia di Google Sheets.
- Proctoring kamera, screen recording, atau lockdown browser penuh.
- SSO eksternal.

## Kebutuhan Fungsional

### Authentication

- Admin dapat login menggunakan username atau email dan password.
- Siswa dapat login menggunakan username, NISN, atau email dan password/token.
- Sistem membatasi halaman berdasarkan role.
- Admin tidak diarahkan ke halaman siswa.
- Siswa tidak dapat mengakses halaman admin.
- Logout tersedia dari topbar aplikasi.

### Dashboard Admin

- Menampilkan ringkasan data ujian, siswa, sesi aktif, selesai, dan waktu habis.
- Menampilkan sesi ujian terbaru.
- Navigasi admin meliputi `Dashboard`, `Ujian`, `Siswa`, `Monitoring`, `Hasil Ujian`, dan `Log`.

### Manajemen Ujian

- Admin dapat membuat, mengedit, dan menghapus ujian.
- Data ujian mencakup nama ujian, kode ujian, mata pelajaran, kelas, Google Form URL, spreadsheet hasil, nama sheet hasil, field prefill, waktu mulai, waktu selesai, durasi, status aktif, izin mengulang, tampilkan nilai, dan instruksi.
- `Kode Ujian` wajib unik.
- Google Form URL dapat berupa URL asli Google Form atau short URL `forms.gle`.
- `Field Username Google Form` menyimpan parameter `entry.xxxxx` untuk pertanyaan username di Google Form.
- Jika `Field Username Google Form` kosong, aplikasi mencoba mendeteksi field username dari HTML Google Form publik.
- Daftar ujian ditampilkan sebagai kartu compact agar lebih banyak data terlihat dalam satu layar.

### Manajemen Siswa

- Admin dapat menambah, mengedit, menghapus, dan reset password/token siswa.
- Data siswa menggunakan NISN.
- Template CSV siswa menggunakan header `nisn`.
- Import CSV siswa mendukung data minimal `name`, `nisn`, `class`, `username`, `email`, `password`, dan `kode_ujian`.
- Import memakai `username` sebagai kunci update.
- Kolom `kode_ujian` dapat berisi lebih dari satu kode.
- Pemisah kode yang didukung: titik koma `;`, koma `,`, pipe `|`, atau baris baru.
- Jika kode ujian tidak ditemukan, baris terkait dilewati dan jumlahnya dilaporkan.
- Master data siswa menampilkan identitas utama dan daftar kode ujian yang diikuti.

### Dashboard Siswa

- Siswa melihat nama, NISN, kelas, dan daftar ujian yang didaftarkan untuk dirinya.
- Ujian yang tampil berdasarkan relasi peserta, bukan hanya berdasarkan kelas.
- Status ujian meliputi `belum_mulai`, `tersedia`, `berlangsung`, `selesai`, dan `waktu_habis`.
- Tombol mulai ujian hanya aktif ketika ujian tersedia sesuai jadwal.

### Instruksi Ujian

- Menampilkan detail ujian, mapel, kelas, durasi, jadwal, dan instruksi.
- Siswa wajib menyetujui instruksi sebelum mulai.
- Sistem membuat sesi baru atau melanjutkan sesi yang belum selesai.
- Jika ujian sudah selesai dan tidak boleh mengulang, siswa diarahkan ke halaman selesai.

### Halaman Ujian

- Menampilkan Google Form dalam iframe layar penuh.
- Timer dihitung dari `started_at + duration_minutes`.
- Timer tetap akurat setelah refresh.
- Sistem menampilkan peringatan kecil untuk aktivitas penting.
- Saat sisa waktu 3 menit, sistem menampilkan popup besar yang mengingatkan siswa untuk klik `Kirim` di Google Form.
- Saat waktu habis, sesi ditandai `waktu_habis` dan siswa diarahkan ke halaman selesai.
- Saat Google Form submit terdeteksi, sesi ditandai `selesai` dan popup kembali dashboard ditampilkan.
- Deteksi submit memakai sinkronisasi hasil Google Sheets dan fallback perubahan iframe Google Form.

### Prefilled Google Form

- Aplikasi dapat membuat URL prefilled dengan data siswa.
- Field yang didukung: nama, username, NISN, kelas, dan nama ujian.
- Username menjadi field prioritas untuk mencegah kesalahan penulisan identitas siswa.
- Jika Google Form URL memakai `forms.gle`, aplikasi mencoba resolve ke URL asli sebelum menambahkan parameter `entry.xxxxx`.
- Jika URL sudah memiliki parameter prefill lama, aplikasi mengganti nilai parameter tersebut dengan data siswa login agar tidak terjadi duplikasi nilai.

### Proteksi dan Monitoring

- Sistem mencatat siswa membuka halaman ujian.
- Sistem mencatat siswa mulai ujian.
- Sistem mencatat pindah tab atau aplikasi.
- Sistem mencatat keluar fullscreen jika event dikirim.
- Sistem mencatat selesai ujian, waktu habis, submit Google Form terdeteksi, reset sesi, sinkron hasil, download hasil, dan hapus nilai.
- Admin dapat memfilter monitoring berdasarkan status, ujian, atau tanggal.
- Admin dapat reset sesi ujian siswa.

### Hasil Ujian

- Admin dapat memilih ujian dan sinkronisasi hasil dari Google Sheets.
- Spreadsheet hasil harus dapat dibaca oleh aplikasi, misalnya `Anyone with the link can view` atau sheet dipublish.
- Sistem membaca sheet sebagai CSV dari endpoint Google Sheets.
- Sistem mencocokkan hasil dengan siswa berdasarkan username, NISN, email, atau profil nama dan kelas.
- Sistem menyimpan nilai, max score, persentase, waktu submit, dan payload mentah.
- Admin dapat menghapus nilai apa pun dari daftar hasil.
- Admin dapat download hasil ke CSV.

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
- `prefill_username_field`
- `prefill_nisn_field`
- `prefill_class_field`
- `prefill_exam_field`
- `start_time`
- `end_time`
- `duration_minutes`
- `is_active`
- `allow_retake`
- `show_score`
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

### Activity Logs

- `user_id`
- `exam_id`
- `exam_session_id`
- `action`
- `description`
- `ip_address`
- `user_agent`
- `created_at`

## Kebutuhan Non-Fungsional

- Aplikasi berjalan di Laravel dengan Blade dan Tailwind CSS.
- Database produksi menggunakan PostgreSQL.
- Import CSV mendukung delimiter koma, titik koma, dan tab.
- UI responsif pada desktop dan mobile.
- Middleware role wajib membatasi akses admin dan siswa.
- Password disimpan dengan hashing Laravel.
- Aktivitas penting tercatat untuk audit.
- Tampilan tidak memakai mode gelap/terang; aplikasi memakai satu tema terang lembut.
- Background utama memakai warna lembut agar tidak menyilaukan saat ujian.

## Kriteria Sukses

- Admin dapat membuat ujian dengan kode unik.
- Admin dapat mengimport siswa melalui CSV dan mendaftarkan peserta berdasarkan kode ujian.
- Siswa hanya melihat ujian yang didaftarkan untuk dirinya.
- Username siswa otomatis terisi di Google Form saat parameter username tersedia atau terdeteksi.
- Popup peringatan 3 menit muncul sebelum waktu habis.
- Popup kembali dashboard muncul setelah Google Form terkirim.
- Timer tetap akurat setelah refresh.
- Admin dapat memonitor dan reset sesi.
- Admin dapat sinkron, download, dan hapus nilai ujian.

## Risiko dan Batasan

- Google Form tidak mengizinkan aplikasi mengambil jawaban yang belum dikirim.
- Jika siswa tidak klik `Kirim` di Google Form sebelum waktu habis, nilai tidak masuk ke Google Sheets dan tidak bisa disinkronkan.
- Deteksi otomatis field username bergantung pada struktur HTML Google Form dan akses publik form.
- Google Form atau Google Sheets yang tidak dapat diakses dapat menggagalkan prefill otomatis atau sinkronisasi hasil.
- Proteksi tab/fullscreen hanya proteksi dasar browser, bukan proctoring penuh.
- Format header Google Sheets harus konsisten agar pencocokan siswa akurat.

## Catatan Implementasi Terbaru

- Mode terang/gelap sudah dihapus; aplikasi memakai satu tema terang lembut.
- Background utama dilembutkan ke warna abu-biru muda.
- Kartu daftar ujian pada manajemen ujian dibuat lebih compact dan mendukung 3 kolom pada layar lebar.
- Prefilled username Google Form diperkuat dengan resolve short URL `forms.gle` dan fallback deteksi field username.
- URL prefill sekarang mengganti parameter lama agar tidak terjadi duplikasi `entry.xxxxx`.
- Popup submit Google Form dan popup peringatan sisa 3 menit sudah tersedia pada halaman ujian.
- Tombol hapus nilai tersedia pada semua baris daftar hasil ujian.
