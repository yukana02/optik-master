@extends('layouts.admin')
@section('title', 'Produk')
@section('page-title', 'Manajemen Produk')

@section('content')
<div class="card">
    <div class="card-header p-3">
        {{-- Filter row --}}
        <form class="row g-2 mb-2" method="GET">
            <div class="col-12 col-sm-6 col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Nama / kode / merek..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-sm-4 col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-sm-4 col-md-3">
                <select name="stok" class="form-select form-select-sm">
                    <option value="">Semua Stok</option>
                    <option value="menipis" {{ request('stok')=='menipis' ? 'selected' : '' }}>Stok Menipis</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                @if(request()->anyFilled(['search','category_id','stok']))
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-light">Reset</a>
                @endif
            </div>
        </form>
        {{-- Actions row --}}
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <div><i class="bi bi-box-seam text-primary me-1"></i>Daftar Produk</div>
            <div class="d-flex flex-wrap gap-2">

            {{-- Import / Export --}}
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-arrow-down-up me-1"></i>Import/Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('import.index') }}">
                            <i class="bi bi-upload text-primary me-2"></i>Import Produk (Excel/CSV)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('export.produk') }}{{ request()->getQueryString() ? '?'.request()->getQueryString() : '' }}">
                            <i class="bi bi-file-earmark-excel text-success me-2"></i>Export Produk (.xlsx)
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('import.template', 'produk') }}">
                            <i class="bi bi-file-earmark-spreadsheet text-secondary me-2"></i>Download Template
                        </a>
                    </li>
                </ul>
            </div>

            @can('product.create')
            <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Produk
            </a>
            @endcan
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3 d-none d-lg-table-cell">#</th>
                    <th class="d-none d-md-table-cell">Gambar</th>
                    <th class="d-none d-md-table-cell">Kode</th>
                    <th>Nama Produk</th>
                    <th class="d-none d-lg-table-cell">Kategori</th>
                    <th>Harga Jual</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center d-none d-sm-table-cell">Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $i => $p)
                <tr>
                    <td class="ps-3 text-muted d-none d-lg-table-cell">{{ $products->firstItem() + $i }}</td>
                    <td class="d-none d-md-table-cell">
                        <img src="{{ $p->gambar_url }}" alt="{{ $p->nama }}"
                             style="width:44px;height:44px;object-fit:cover;border-radius:8px;border:1px solid #eee">
                    </td>
                    <td class="d-none d-md-table-cell">
                        <span class="badge bg-secondary">{{ $p->kode_produk }}</span>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $p->nama }}</div>
                        @if($p->merek)
                        <small class="text-muted">{{ $p->merek }}</small>
                        @endif
                        <small class="text-muted d-md-none">{{ $p->kode_produk }}</small>
                    </td>
                    <td class="d-none d-lg-table-cell text-muted small">{{ $p->category->nama ?? '-' }}</td>
                    <td class="fw-semibold" style="font-size:.85rem">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $p->stok == 0 ? 'bg-danger' : ($p->stok_menipis ? 'bg-warning text-dark' : 'bg-success') }}">
                            {{ $p->stok }} {{ $p->satuan }}
                        </span>
                    </td>
                    <td class="text-center d-none d-sm-table-cell">
                        <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('products.show', $p) }}" class="btn btn-xs btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('product.edit')
                            <a href="{{ route('products.edit', $p) }}" class="btn btn-xs btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('product.delete')
                            <form method="POST" action="{{ route('products.destroy', $p) }}"
                                  data-confirm="Hapus produk {{ $p->nama }}?">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-25"></i>
                        Belum ada produk
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="card-footer">{{ $products->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

