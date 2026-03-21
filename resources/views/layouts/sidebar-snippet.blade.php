{{--
    Tambahkan snippet ini di dalam blok @canany(['product.view']) pada layouts/admin.blade.php
    SETELAH baris menu "Kategori", SEBELUM @endcanany

    Atau copy seluruh section "Toko" yang sudah diupdate berikut ini:
--}}

    {{-- ── GANTI SELURUH SECTION "Toko" DI admin.blade.php DENGAN INI ── --}}

    <div class="nav-section">Toko</div>
    <a href="{{ route('transactions.create') }}" class="nav-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
        <i class="bi bi-cart-plus"></i> POS / Kasir
    </a>
    <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.index','transactions.show') ? 'active' : '' }}">
        <i class="bi bi-receipt-cutoff"></i> Riwayat Transaksi
    </a>
    @canany(['product.view'])
    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i> Produk
    </a>
    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
        <i class="bi bi-tags"></i> Kategori
    </a>
    <a href="{{ route('import.index') }}" class="nav-link {{ request()->routeIs('import.*') ? 'active' : '' }}">
        <i class="bi bi-arrow-down-up"></i> Import / Export
    </a>
    @endcanany
