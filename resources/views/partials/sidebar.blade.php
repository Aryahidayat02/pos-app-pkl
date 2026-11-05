<aside class="pos-sidebar">
    <div class="brand">
        <i class="bi bi-shop"></i>
        {{ \App\Models\Setting::get('store_name', 'POS App') }}
    </div>

    <div class="pos-nav-group">Utama</div>
    <ul class="nav flex-column">
        <li>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('kasir') }}" class="nav-link {{ request()->routeIs('kasir') ? 'active' : '' }}">
                <i class="bi bi-cart3"></i>Kasir
            </a>
        </li>
        <li>
            <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>Riwayat Transaksi
            </a>
        </li>
    </ul>

    <div class="pos-nav-group">Master Data</div>
    <ul class="nav flex-column">
        {{-- Menu Produk: Bisa dilihat Admin & Kasir --}}
        <li>
            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>Produk
            </a>
        </li>

        {{-- Menu di bawah ini HANYA untuk Admin --}}
        @if(auth()->user()->role === 'admin')
            <li><a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"><i class="bi bi-tags"></i>Kategori</a></li>
            <li><a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"><i class="bi bi-people"></i>Pelanggan</a></li>
            <li><a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"><i class="bi bi-truck"></i>Supplier</a></li>
            <li><a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}"><i class="bi bi-box-arrow-in-down"></i>Stok Masuk</a></li>
        @endif
    </ul>

    @if(auth()->user()->role === 'admin')
    <div class="pos-nav-group">Laporan & Setting</div>
    <ul class="nav flex-column">
        <li><a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i>Laporan</a></li>
        <li><a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="bi bi-person-gear"></i>User</a></li>
        <li><a href="{{ route('settings.index') }}" class="nav-link {{ route('settings.index') == url()->current() ? 'active' : '' }}"><i class="bi bi-gear"></i>Pengaturan</a></li>
    </ul>
    @endif
</aside>