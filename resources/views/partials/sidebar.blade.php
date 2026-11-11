<aside class="pos-sidebar">
    <div class="brand">
        <i class="bi bi-shop"></i>
        {{ \App\Models\Setting::get('store_name', 'POS App') }}
    </div>

    <div class="pos-nav-group">Utama</div>
    <ul class="nav flex-column">
        <li><a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a></li>
        <li><a href="{{ route('sales.create') }}" class="nav-link">Kasir</a></li>
        <li><a href="{{ route('sales.index') }}" class="nav-link">Riwayat Transaksi</a></li>
    </ul>

    <div class="pos-nav-group">Master Data</div>
    <ul class="nav flex-column">
        <li><a href="{{ route('products.index') }}" class="nav-link">Produk</a></li>
        <li><a href="{{ route('categories.index') }}" class="nav-link">Kategori</a></li>
        <li><a href="{{ route('customers.index') }}" class="nav-link">Pelanggan</a></li>
        <li><a href="{{ route('suppliers.index') }}" class="nav-link">Supplier</a></li>
        <li><a href="{{ route('purchases.index') }}" class="nav-link">Stok Masuk</a></li>
    </ul>

    <div class="pos-nav-group">Laporan & Setting</div>
    <ul class="nav flex-column">
        <li><a href="{{ route('reports.index') }}" class="nav-link">Laporan</a></li>
        <li><a href="{{ route('users.index') }}" class="nav-link">Manajemen User</a></li>
        <li><a href="{{ route('settings.index') }}" class="nav-link">Pengaturan</a></li>
        
        {{-- KITA HAPUS SEMUA IF NYA --}}
        <li><a href="{{ route('logs.index') }}" class="nav-link" style="color: yellow; font-weight: bold;">LOG AKTIVITAS (TANPA IF)</a></li>
    </ul>
</aside>