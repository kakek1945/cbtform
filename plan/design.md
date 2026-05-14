# GitHub-Style UI Design System

## Overview

Design ini mengikuti nuansa frontend GitHub: clean, minimalis, rapi, developer-friendly, banyak whitespace, border halus, card sederhana, tabel mudah dibaca, dan warna netral profesional.

## Design Tokens

```yaml
version: beta
name: GitHub Style
description: Clean developer dashboard with neutral surfaces, soft borders, compact controls, and readable data tables.

colors:
  background: "#f6f8fa"
  surface: "#ffffff"
  surfaceSubtle: "#f6f8fa"
  border: "#d0d7de"
  borderMuted: "#d8dee4"
  text: "#24292f"
  muted: "#57606a"
  mutedLight: "#6e7781"
  primary: "#0969da"
  primaryHover: "#0757b5"
  success: "#1f883d"
  successHover: "#1a7f37"
  danger: "#cf222e"
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
  label:
    fontSize: "14px"
    fontWeight: 600
  tableHeader:
    fontSize: "12px"
    fontWeight: 600
    textTransform: uppercase

radius:
  sm: "6px"
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

## Layout

- Gunakan background utama `#f6f8fa`.
- Gunakan topbar gelap `#24292f` untuk navigasi utama.
- Konten utama memakai container maksimal `1180px` dengan padding `32px 24px`.
- Halaman dimulai dengan `page-header` berisi kicker, title, deskripsi singkat, dan aksi utama.
- Gunakan whitespace secukupnya agar halaman terlihat ringan dan mudah dipindai.

## Topbar

- Background `#24292f`.
- Brand berwarna putih, font weight `600`, dengan ikon sederhana.
- User chip memakai teks muted `#d0d7de`.
- Tombol logout/login di topbar menggunakan border transparan lembut dan background transparan.

## Cards

```css
.card {
  background: #ffffff;
  border: 1px solid #d0d7de;
  border-radius: 6px;
  box-shadow: 0 1px 0 rgba(27, 31, 36, .04);
}

.card-header {
  background: #f6f8fa;
  border-bottom: 1px solid #d0d7de;
  padding: 16px;
}

.card-body {
  padding: 16px;
}
```

## Buttons

Gunakan tombol compact seperti GitHub.

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
  background: #1f883d;
  color: #ffffff;
}

.btn-secondary {
  background: #0969da;
  color: #ffffff;
}

.btn-danger {
  background: #ffffff;
  border-color: rgba(207, 34, 46, .35);
  color: #cf222e;
}
```

## Forms

- Label singkat dan jelas.
- Input tinggi minimal `36px`.
- Border `#d0d7de`.
- Radius `6px`.
- Focus state memakai border biru dan ring lembut.

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

- Gunakan tabel untuk daftar data utama.
- Header tabel memakai background subtle `#f6f8fa`.
- Header uppercase kecil agar mudah dipindai.
- Border row halus.
- Pada mobile, tabel dibungkus container horizontal scroll.

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
  background: #f6f8fa;
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

- Gunakan badge kecil untuk status dan prioritas.
- Success untuk selesai atau prioritas rendah.
- Warning untuk prioritas sedang.
- Danger untuk prioritas tinggi atau deadline terlambat.

```css
.badge {
  border: 1px solid #d0d7de;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  padding: 2px 8px;
}

.badge-low {
  background: #dafbe1;
  border-color: #aceebb;
  color: #1f883d;
}

.badge-medium {
  background: #fff8c5;
  border-color: #eac54f;
  color: #9a6700;
}

.badge-high {
  background: #ffebe9;
  border-color: #ffcecb;
  color: #cf222e;
}
```

## Alerts

- Alert memakai card putih dengan border halus.
- Gunakan border kiri untuk menandai status.
- Success memakai hijau.
- Error memakai merah.

```css
.flash,
.errors {
  background: #ffffff;
  border: 1px solid #d0d7de;
  border-radius: 6px;
  display: flex;
  gap: 10px;
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

## Icons

- Gunakan ikon inline SVG agar ringan dan tidak menambah dependency.
- Ukuran standar `16px`.
- Ukuran besar `24px` untuk brand atau empty state.
- Ikon digunakan hanya pada aksi penting: tambah, simpan, edit, hapus, login, logout, status selesai, user.

```css
.icon {
  height: 16px;
  width: 16px;
}

.icon-lg {
  height: 24px;
  width: 24px;
}
```

## Responsive Rules

- Pada layar kecil, topbar, page header, dan card header berubah menjadi kolom.
- Tombol menjadi full width agar mudah ditekan.
- Form action menjadi full width.
- Tabel tetap dipertahankan dengan horizontal scroll.

```css
@media (max-width: 700px) {
  .topbar-inner,
  .page-header,
  .card-header {
    align-items: stretch;
    flex-direction: column;
  }

  .btn {
    width: 100%;
  }

  .table-wrap {
    overflow-x: auto;
  }
}
```

## Copywriting Rules

- Gunakan heading pendek.
- Gunakan label langsung ke tujuan.
- Hindari paragraf panjang.
- Gunakan teks aksi jelas seperti `Tambah Todo`, `Simpan Todo`, `Edit`, `Hapus`, `Login`, dan `Logout`.

## Do's

- Gunakan warna netral sebagai dasar.
- Gunakan border halus untuk pemisah utama.
- Gunakan tabel untuk data yang perlu dibandingkan.
- Gunakan ikon seperlunya untuk memperjelas aksi.
- Jaga controller dan route tetap tidak berubah saat refactor UI.

## Don'ts

- Jangan gunakan gradient.
- Jangan gunakan shadow besar.
- Jangan gunakan terlalu banyak warna aksen.
- Jangan membuat tombol terlalu besar pada desktop.
- Jangan mengubah business logic hanya untuk styling.
