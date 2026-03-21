@extends('layouts.admin')
@section('title', 'Data Supplier')
@section('page-title', 'Data Supplier')

@section('content')
<div class="card">
    <div class="card-header p-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div><i class="bi bi-truck text-primary me-2"></i>Daftar Supplier</div>
        <div class="d-flex gap-2 flex-wrap">
            <form class="d-flex gap-2" method="GET">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / kode..." value="{{ request('search') }}" style="width:200px">
                <select name="status" class="form-select form-select-sm" style="width:110px">
                    <option value="">Semua</option>
                    <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status')=='nonaktif'?'selected':'' }}>Nonaktif</option>
                </select>
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-filter"></i></button>
                @if(request()->anyFilled(['search','status']))
                    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-light">Reset</a>
                @endif
            </form>
            @can('supplier.create')
            <a href="{{ route('suppliers.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Supplier</a>
            @endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Kode</th>
                    <th>Nama Supplier</th>
                    <th>Kontak</th>
                    <th>Telepon</th>
                    <th>Status</th>
                    <th>Total PO</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $s)
                <tr>
                    <td class="ps-3"><span class="badge bg-light text-dark border fw-semibold">{{ $s->kode_supplier }}</span></td>
                    <td class="fw-semibold">{{ $s->nama }}</td>
                    <td class="text-muted small">{{ $s->kontak_person ?? '-' }}</td>
                    <td class="text-muted small">{{ $s->telepon ?? '-' }}</td>
                    <td>
                        @if($s->is_active)
                            <span class="badge badge-lunas">Aktif</span>
                        @else
                            <span class="badge badge-batal">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $s->purchase_orders_count ?? $s->purchaseOrders()->count() }} PO</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('suppliers.show', $s) }}" class="btn btn-sm btn-light" title="Detail"><i class="bi bi-eye"></i></a>
                        @can('supplier.edit')
                        <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                        @endcan
                        @can('supplier.delete')
                        <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="d-inline"
                              data-confirm="Hapus supplier {{ $s->nama }}?">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
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
