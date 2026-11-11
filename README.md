# POS App - Tugas PKL Astacode

Aplikasi Point of Sale (POS) berbasis web yang dibangun menggunakan Laravel 13, Bootstrap 5, dan MySQL. Proyek ini dibuat untuk memenuhi tugas Praktik Kerja Lapangan (PKL) di Astacode.

## 🚀 Fitur Utama (M1-M11)
- **Autentikasi & Hak Akses:** Login/Logout dengan role Admin & Kasir.
- **Dashboard:** Ringkasan penjualan hari ini, transaksi, stok menipis, dan produk aktif.
- **Master Data:** CRUD Kategori, Produk (dengan upload gambar), Pelanggan, dan Supplier.
- **Stok Masuk:** Pencatatan barang dari supplier dengan `DB::transaction`.
- **Transaksi Kasir:** Keranjang dinamis, validasi stok, hitung kembalian otomatis.
- **Riwayat & Struk:** Filter transaksi, cetak struk thermal (58mm/80mm).
- **Laporan:** Omzet, laba kotor, dan 10 produk terlaris.
- **Pengaturan:** Profil toko dan footer struk.
- **Manajemen User:** Kelola akun Admin dan Kasir.

## 🌟 Fitur Bonus (B1, B2, B3)
- **B1 - Export Excel:** Laporan penjualan bisa diunduh dalam format Excel (`.xlsx`).
- **B2 - Void Transaksi:** Admin bisa membatalkan transaksi dan stok produk otomatis dikembalikan.
- **B3 - Log Aktivitas:** Mencatat setiap aktivitas create, update, dan delete beserta data lama dan barunya.

## 🔑 Akun Demo
- **Admin:** `admin@pos.test` / password: `password`
- **Kasir:** `kasir@pos.test` / password: `password`

## ⚙️ Cara Instalasi
1. Clone repo ini:
   ```bash
   git clone https://github.com/Aryahidayat02/pos-app-pkl.git
