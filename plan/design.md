# Design System

## Ringkasan

Desain aplikasi memakai gaya dashboard sekolah yang bersih, compact, dan mudah dibaca. Aplikasi tidak lagi menyediakan mode terang/gelap. Seluruh halaman menggunakan satu tema terang lembut dengan latar abu-biru muda agar tidak menyilaukan saat dipakai lama, terutama pada sesi ujian.

## Prinsip Desain

- Prioritaskan keterbacaan teks dan data.
- Gunakan layout compact untuk halaman admin agar lebih banyak informasi terlihat.
- Gunakan warna aksen seperlunya untuk aksi dan status.
- Hindari visual yang terlalu terang, terlalu ramai, gradient, dan shadow besar.
- Jaga konsistensi bentuk: border halus, radius kecil-menengah, dan kontrol sederhana.
- Pastikan tampilan tetap nyaman di desktop dan mobile.

## Design Tokens

```yaml
version: 2
name: Soft GitHub-Inspired CBT
description: Compact school CBT dashboard with soft light background, neutral cards, readable tables, and clear exam warnings.

colors:
  background: "#eef2f6"
  surface: "#ffffff"
  surfaceSubtle: "#eef2f6"
  border: "#d0d7de"
  borderMuted: "#d8dee4"
  text: "#24292f"
  muted: "#57606a"
  mutedLight: "#8c959f"
  primary: "#0969da"
  primaryHover: "#0757b5"
  success: "#1f883d"
  successHover: "#1a7f37"
  danger: "#cf222e"
  dangerHover: "#a40e26"
  dangerSurface: "#ffebe9"
  warning: "#9a6700"
  warningSurface: "#fff8c5"
  infoSurface: "#ddf4ff"
  topbar: "#24292f"
  topbarText: "#ffffff"
  topbarMuted: "#d0d7de"

typography:
  fontFamily: "-apple-system, BlinkMacSystemFont, Segoe UI, Helvetica, Arial, sans-serif"
  body:
    fontSize: "14px"
    lineHeight: 1.5
    fontWeight: 400
  pageTitle:
    fontSize: "24px"
    lineHeight: 1.25
    fontWeight: 600
    letterSpacing: "-0.01em"
  cardTitle:
    fontSize: "16px"
    lineHeight: 1.25
    fontWeight: 600
  compactCardTitle:
    fontSize: "16px"
    lineHeight: 1.25
    fontWeight: 700
  label:
    fontSize: "14px"
    fontWeight: 600
  tableHeader:
    fontSize: "12px"
    fontWeight: 600
    textTransform: uppercase

radius:
  sm: "6px"
  md: "8px"
  pill: "999px"

spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"

shadow:
  card: "0 1px 0 rgba(27, 31, 36, .04)"
```

## Theme Policy

- Tidak ada toggle mode terang/gelap.
- Tidak ada script `localStorage` untuk theme.
- Tidak ada class `dark:` pada source aplikasi.
- Semua halaman menggunakan theme terang lembut.
- Background utama: `#eef2f6`.
- Card dan form tetap putih untuk menjaga kontras bacaan.

## Layout Utama

- Body memakai background `#eef2f6` dan teks `#24292f`.
- Konten utama memakai container maksimal `1180px`.
- Halaman ujian memakai mode wide content hingga `1600px` karena iframe Google Form perlu area luas.
- Padding utama desktop sekitar `32px` vertikal.
- Pada mobile, spacing dikurangi dan tombol dapat menjadi full width.

## Topbar

- Topbar memakai background gelap `#24292f`.
- Logo sekolah ditampilkan dalam kotak putih kecil agar tetap terlihat.
- Nama sekolah memakai teks muted `#d0d7de`.
- Judul halaman aktif berwarna putih.
- Navigasi aktif memakai background putih dan teks gelap.
- Navigasi nonaktif memakai teks muted dan hover putih transparan.
- Tombol logout menggunakan border putih transparan.

## Cards

Card standar digunakan pada dashboard, form, tabel ringkas, dan panel informasi.

```css
.card {
  background: #ffffff;
  border: 1px solid #d0d7de;
  border-radius: 6px;
  box-shadow: 0 1px 0 rgba(27, 31, 36, .04);
}

.card-body {
  padding: 16px;
}
```

## Compact Exam Cards

Kartu daftar ujian pada halaman `Manajemen Ujian` sengaja dibuat lebih kecil agar admin dapat melihat lebih banyak ujian sekaligus.

- Grid default gap `12px`.
- `md`: 2 kolom.
- `xl`: 3 kolom.
- Padding kartu: `16px`.
- Judul ujian: `16px`, truncate satu baris.
- Mata pelajaran dan kelas: `12px`, truncate satu baris.
- Badge kode dan status: `11px`.
- Tombol aksi: `12px`, padding compact.

## Buttons

Gunakan tombol compact, jelas, dan tidak terlalu besar pada desktop.

```css
.btn {
  min-height: 32px;
  padding: 5px 12px;
  border-radius: 6px;
  border: 1px solid rgba(27, 31, 36, .15);
  font-size: 14px;
  font-weight: 600;
}

.btn-primary {
  background: #0969da;
  color: #ffffff;
}

.btn-success {
  background: #1f883d;
  color: #ffffff;
}

.btn-danger {
  background: #cf222e;
  color: #ffffff;
}
```

## Forms

- Label harus pendek dan jelas.
- Input minimal tinggi `36px`.
- Background input putih.
- Border `#d0d7de`.
- Focus memakai border biru dan ring lembut.
- Helper text kecil dapat digunakan untuk menjelaskan field teknis seperti `Field Username Google Form`.

```css
input,
select,
textarea {
  background: #ffffff;
  border: 1px solid #d0d7de;
  border-radius: 6px;
  color: #24292f;
  min-height: 36px;
  padding: 7px 10px;
}

input:focus,
select:focus,
textarea:focus {
  border-color: #0969da;
  box-shadow: 0 0 0 3px rgba(9, 105, 218, .15);
  outline: none;
}
```

## Tables

- Tabel digunakan untuk data yang perlu dibandingkan: siswa, monitoring, hasil, log.
- Header tabel memakai background `#eef2f6`.
- Header uppercase kecil.
- Row memakai border bawah halus.
- Pada mobile, tabel dibungkus horizontal scroll.

```css
.table-wrap {
  border: 1px solid #d0d7de;
  border-radius: 6px;
  overflow-x: auto;
}

.data-table {
  border-collapse: collapse;
  min-width: 760px;
  width: 100%;
}

.data-table th {
  background: #eef2f6;
  border-bottom: 1px solid #d0d7de;
  color: #57606a;
  font-size: 12px;
  font-weight: 600;
  padding: 10px 12px;
  text-align: left;
  text-transform: uppercase;
}

.data-table td {
  border-bottom: 1px solid #d8dee4;
  padding: 12px;
  vertical-align: top;
}
```

## Badges

- Badge digunakan untuk status ujian, status sesi, kode ujian, dan metadata ringkas.
- Badge harus kecil dan tidak mendominasi card.
- Status aktif/sukses memakai hijau.
- Status nonaktif/netral memakai abu.
- Warning memakai amber.
- Error/danger memakai merah.

```css
.badge {
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  padding: 2px 8px;
}

.badge-compact {
  font-size: 11px;
  padding: 2px 10px;
}
```

## Alerts dan Flash Messages

- Flash success dan error memakai card putih dengan border halus.
- Gunakan border kiri untuk memperjelas status.

```css
.flash,
.errors {
  background: #ffffff;
  border: 1px solid #d0d7de;
  border-radius: 6px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 12px 14px;
}

.flash {
  border-left: 4px solid #1f883d;
}

.errors {
  border-left: 4px solid #cf222e;
  color: #cf222e;
}
```

## Exam Page UI

- Halaman ujian memakai layout layar penuh agar Google Form punya ruang maksimal.
- Timer berada fixed di kanan atas.
- Warning kecil berada di atas tengah.
- Iframe Google Form memenuhi layar.
- Popup submit berhasil memakai overlay gelap transparan dan card putih.
- Popup sisa waktu 3 menit memakai overlay merah gelap agar sangat terlihat.

### Popup Peringatan 3 Menit

- Muncul sekali saat sisa waktu `<= 180` detik.
- Pesan utama: siswa harus klik `Kirim` di Google Form.
- Copy harus eksplisit bahwa nilai tidak masuk jika waktu habis sebelum Google Form dikirim.
- Tombol: `Saya Mengerti, Lanjut Kirim Jawaban`.

### Popup Jawaban Terkirim

- Muncul setelah submit Google Form terdeteksi.
- Pesan memberi tahu jawaban sudah terkirim.
- Tombol utama: `Kembali ke Dashboard Siswa`.

## Login dan Welcome

- Login memakai card putih di atas background `#eef2f6`.
- Tidak ada theme toggle.
- Logo sekolah berada di bagian atas card.
- Form dibuat singkat: username, password, tombol masuk.
- Halaman welcome disederhanakan dan langsung mengarahkan pengguna ke login.

## Icons

- Gunakan ikon inline SVG lewat komponen `x-icon`.
- Ukuran standar: `16px`.
- Ukuran compact: `14px`.
- Ukuran besar untuk heading atau modal: `24px` sampai `32px`.
- Ikon dipakai pada aksi penting: tambah, edit, hapus, login, logout, sinkron, download, warning, selesai.

## Responsive Rules

- Pada layar kecil, topbar dan header halaman dapat berubah menjadi kolom.
- Tombol di konten utama dapat menjadi full width agar mudah ditekan.
- Tabel tetap horizontal scroll, bukan dipaksa terlalu sempit.
- Kartu daftar ujian tetap satu kolom di mobile.
- Modal memakai `max-width` dan padding horizontal agar aman di layar kecil.

```css
@media (max-width: 700px) {
  .topbar-inner {
    align-items: stretch;
    flex-direction: column;
  }

  main button,
  main a[class*="bg-"],
  main a[class*="border"] {
    justify-content: center;
    width: 100%;
  }

  .table-wrap {
    overflow-x: auto;
  }
}
```

## Copywriting Rules

- Gunakan bahasa Indonesia yang langsung dan instruksional.
- Heading harus pendek.
- Label form harus jelas.
- Untuk risiko penting, gunakan kalimat eksplisit.
- Contoh copy penting: `Tekan tombol Kirim di Google Form sekarang. Jika waktu habis sebelum jawaban dikirim, nilai tidak akan masuk ke aplikasi.`
- Tombol harus menjelaskan aksi: `Tambah Ujian`, `Simpan`, `Edit`, `Hapus`, `Sinkronkan`, `Download CSV`, `Kembali ke Dashboard Siswa`.

## Do's

- Gunakan background lembut `#eef2f6`.
- Gunakan card putih untuk area baca utama.
- Gunakan border halus untuk struktur.
- Gunakan layout compact untuk daftar admin.
- Gunakan popup besar hanya untuk peringatan kritis.
- Jaga kontras teks tetap jelas.

## Don'ts

- Jangan menambahkan mode gelap atau toggle tema.
- Jangan memakai background putih penuh pada seluruh layar kecuali iframe Google Form atau area khusus.
- Jangan gunakan gradient.
- Jangan gunakan shadow besar.
- Jangan membuat tombol desktop terlalu besar.
- Jangan memakai terlalu banyak warna aksen dalam satu card.
- Jangan menyembunyikan risiko submit Google Form dengan copy yang ambigu.
