@extends('layouts.admin')
@section('title', 'Data Supplier')
@section('page-title', 'Data Supplier')
@section('content')
<div class="card">
    <div class="card-header p-3">
        <form class="row g-2 mb-2" method="GET">
            <div class="col-12 col-sm-7 col-md-5">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari nama / kode..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-sm-4 col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="aktif"    {{ request('status')=='aktif'    ?'selected':'' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status')=='nonaktif' ?'selected':'' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search me-1"></i>Cari</button>
                @if(request()->anyFilled(['search','status']))
                <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-light">Reset</a>
                @endif
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center">
            <div><i class="bi bi-truck text-primary me-1"></i>Daftar Supplier</div>
            @can('supplier.create')
            <a href="{{ route('suppliers.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Supplier
            </a>
            @endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Kode</th>
                    <th>Nama Supplier</th>
                    <th class="d-none d-md-table-cell">Kontak</th>
                    <th class="d-none d-md-table-cell">Telepon</th>
                    <th>Status</th>
                    <th class="d-none d-lg-table-cell">Total PO</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $s)
                <tr>
                    <td class="ps-3"><span class="badge bg-light text-dark border fw-semibold">{{ $s->kode_supplier }}</span></td>
                    <td>
                        <div class="fw-semibold">{{ $s->nama }}</div>
                        <small class="text-muted d-md-none">{{ $s->telepon ?? '-' }}</small>
                    </td>
                    <td class="d-none d-md-table-cell text-muted small">{{ $s->kontak_person ?? '-' }}</td>
                    <td class="d-none d-md-table-cell text-muted small">{{ $s->telepon ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $s->is_active ? 'badge-lunas' : 'badge-batal' }}">
                            {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="d-none d-lg-table-cell text-muted small">{{ $s->purchaseOrders()->count() }} PO</td>
                    <td class="text-end pe-3">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('suppliers.show', $s) }}" class="btn btn-xs btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a>
                            @can('supplier.edit')
                            <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-xs btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('supplier.delete')
                            <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="d-inline"
                                  data-confirm="Hapus supplier {{ $s->nama }}?">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data supplier.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="card-footer py-2 px-3">{{ $suppliers->links() }}</div>
    @endif
</div>
@endsection
