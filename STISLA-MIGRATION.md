# Panduan Migrasi ke Template Stisla

## Langkah 1: Download dan Setup Stisla

1. Download Stisla dari GitHub:
```bash
cd public/assets
git clone https://github.com/stisla/stisla.git
cd stisla
cp -r dist/* ../stisla/
cd ..
rm -rf stisla/.git
```

2. Struktur folder yang benar:
```
public/assets/stisla/
├── css/
├── js/
├── modules/
└── img/
```

## Langkah 2: Mengganti Layout Utama

1. Ganti file layout utama:
- Rename: `resources/views/admin/layouts/app.blade.php` → `app-adminlte.blade.php`
- Gunakan: `resources/views/admin/layouts/stisla.blade.php` sebagai layout utama

2. Update referensi layout di semua view:
```php
@extends('admin.layouts.app')
// menjadi
@extends('admin.layouts.stisla')
```

## Langkah 3: Update Halaman Login

1. Ganti file login:
- Rename: `resources/views/admin/login.blade.php` → `login-adminlte.blade.php`
- Gunakan: `resources/views/admin/auth/login-stisla.blade.php`

2. Update route di `AuthController`:
```php
return view('admin.auth.login-stisla');
```

## Langkah 4: Konversi Halaman Fitur

1. Contoh konversi halaman produk:
- Rename: `resources/views/admin/products/index.blade.php` → `index-adminlte.blade.php`
- Gunakan: `resources/views/admin/products/index-stisla.blade.php`

2. Perbedaan utama komponen:
- Card: `.card` (AdminLTE) → `.card` (Stisla)
- Button: `.btn-*` (AdminLTE) → `.btn-*` (Stisla)
- Table: `.table` (AdminLTE) → `.table` (Stisla)
- Alert: `.alert` (AdminLTE) → `.alert` (Stisla)
- Form: Sama-sama menggunakan class Bootstrap

## Langkah 5: Penyesuaian CSS/JS

1. Hapus referensi AdminLTE:
```html
<!-- Hapus -->
<link rel="stylesheet" href="{{ asset('assets/adminlte/css/adminlte.min.css') }}">
<script src="{{ asset('assets/adminlte/js/adminlte.min.js') }}"></script>
```

2. Tambahkan Stisla CSS/JS:
```html
<!-- CSS -->
<link rel="stylesheet" href="{{ asset('assets/stisla/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/stisla/css/components.css') }}">

<!-- JS -->
<script src="{{ asset('assets/stisla/js/stisla.js') }}"></script>
<script src="{{ asset('assets/stisla/js/scripts.js') }}"></script>
<script src="{{ asset('assets/stisla/js/custom.js') }}"></script>
```

## Langkah 6: Migrasi Fitur Lain

1. Dashboard:
- Gunakan card statistics Stisla
- Implementasi grafik dengan Chart.js
- Gunakan komponen timeline Stisla

2. Tabel:
- Gunakan DataTables dengan styling Stisla
- Implementasi fitur export/import
- Tambahkan filter dan pencarian

3. Form:
- Gunakan komponen form Stisla
- Implementasi validasi dengan styling Stisla
- Tambahkan upload file dengan preview

4. Notifikasi:
- Gunakan toast Stisla untuk flash message
- Implementasi dropdown notifikasi
- Tambahkan badge untuk counter

## Tips Penting

1. Responsivitas:
- Gunakan class responsive Stisla
- Test di berbagai ukuran layar
- Pastikan sidebar berfungsi dengan baik

2. Performa:
- Minify assets CSS/JS
- Optimize gambar
- Gunakan lazy loading

3. Konsistensi:
- Gunakan komponen yang sama untuk fungsi serupa
- Pertahankan skema warna
- Ikuti panduan style Stisla

4. Debugging:
- Periksa console untuk error JS
- Validasi HTML
- Test semua interaksi user

## Rollback Plan

Jika terjadi masalah:
1. Backup semua file yang dimodifikasi
2. Simpan versi AdminLTE sebagai fallback
3. Test perubahan di staging sebelum ke production
4. Siapkan rollback script jika diperlukan
