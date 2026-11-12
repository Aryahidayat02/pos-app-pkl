# POS App - Tugas PKL Astacode

Aplikasi Point of Sale (POS) berbasis web yang dibangun menggunakan Laravel 13, Bootstrap 5, dan MySQL. Proyek ini dibuat untuk memenuhi tugas Praktik Kerja Lapangan (PKL) di Astacode. Aplikasi ini mempermudah proses transaksi jual-beli di toko, mulai dari manajemen stok, transaksi kasir, hingga laporan penjualan.

## 🚀 Fitur yang Selesai (M1 - M11 + Bonus)

- **M1 - Autentikasi:** Login/Logout dengan pembatasan hak akses (Admin & Kasir).
- **M2 - Dashboard:** Ringkasan penjualan hari ini, total transaksi, dan peringatan stok menipis.
- **M3 - Kategori Produk:** CRUD Kategori dengan proteksi data yang terikat pada produk (Restrict on Delete).
- **M4 - Produk:** CRUD Produk lengkap dengan upload gambar, filter kategori/status, dan validasi harga.
- **M5 - Pelanggan:** CRUD Pelanggan dengan validasi format nomor HP.
- **M6 - Supplier & Stok Masuk:** Pencatatan stok masuk (Purchases) dengan form dinamis dan `DB::transaction()`.
- **M7 - Transaksi Kasir:** Halaman kasir interaktif dengan keranjang belanja (JavaScript), integrasi QRIS, dan perhitungan kembalian.
- **M8 - Riwayat & Struk:** Daftar riwayat transaksi (filter tanggal/invoice) dan cetak struk (CSS Print 80mm).
- **M9 - Laporan:** Laporan omzet, laba kotor, produk terlaris, dan filter tanggal.
- **M10 - Manajemen User:** CRUD User dengan validasi password dan pembatasan role.
- **M11 - Pengaturan Toko:** Pengaturan profil toko (Nama, Alamat, Telp, Footer Struk) yang diaplikasikan ke sidebar & struk.
- **Bonus B1 - Export Excel:** Unduh laporan penjualan ke format `.xlsx` menggunakan `maatwebsite/excel`.
- **Bonus B2 - Void Transaksi:** Pembatalan transaksi oleh Admin (mengembalikan stok otomatis).
- **Bonus B3 - Log Aktivitas:** Pencatatan otomatis aktivitas Create, Update, Delete (Audit Trail).

## ⚙️ Persyaratan Sistem (Requirements)
- PHP 8.3 atau lebih baru
- Composer
- MySQL 8 (atau MariaDB 10.6+)
- Node.js & NPM (Opsional, untuk build aset Vite)

## 🔑 Akun Demo
- **Admin:** `admin@pos.test` / password: `password`
- **Kasir:** `kasir@pos.test` / password: `password`

## 🛠️ Cara Instalasi
1. Clone repo ini:
   ```bash
   git clone https://github.com/Aryahidayat02/pos-app-pkl.git
