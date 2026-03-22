@extends('layouts.admin')
@section('title', 'Purchase Order')
@section('page-title', 'Purchase Order (Stok Masuk)')
@section('content')
<div class="card">
    <div class="card-header p-3">
        <form class="row g-2 mb-2" method="GET">
            <div class="col-12 col-sm-6 col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="No. PO / Supplier" value="{{ request('search') }}">
            </div>
            <div class="col-6 col-sm-4 col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach(['draft','dikirim','diterima','batal'] as $st)
                    <option value="{{ $st }}" {{ request('status')==$st?'selected':'' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search me-1"></i>Cari</button>
                @if(request()->anyFilled(['search','status']))
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-light">Reset</a>
                @endif
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center">
            <div><i class="bi bi-bag-check text-primary me-1"></i>Daftar Purchase Order</div>
            @can('purchase_order.create')
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Buat PO Baru
            </a>
            @endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">No. PO</th>
                    <th>Supplier</th>
                    <th class="d-none d-md-table-cell">Tgl. PO</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="d-none d-lg-table-cell">Petugas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseOrders as $po)
                <tr>
                    <td class="ps-3 fw-semibold" style="font-size:.82rem">{{ $po->no_po }}</td>
                    <td>
                        <div>{{ $po->supplier->nama ?? '-' }}</div>
                        <small class="text-muted d-md-none">{{ $po->tanggal_po->format('d M Y') }}</small>
                    </td>
                    <td class="d-none d-md-table-cell text-muted small">{{ $po->tanggal_po->format('d M Y') }}</td>
                    <td style="font-size:.85rem">Rp {{ number_format($po->total_harga,0,',','.') }}</td>
                    <td>
                        @php $sc=['draft'=>'secondary','dikirim'=>'info text-dark','diterima'=>'success','batal'=>'danger'] @endphp
                        <span class="badge bg-{{ $sc[$po->status] ?? 'secondary' }}">{{ ucfirst($po->status) }}</span>
                    </td>
                    <td class="d-none d-lg-table-cell text-muted small">{{ $po->user->name ?? '-' }}</td>
                    <td class="text-end pe-2">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-xs btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a>
                            @if(in_array($po->status,['draft','dikirim']))
                            @can('purchase_order.edit')
                            <form method="POST" action="{{ route('purchase-orders.receive', $po) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-xs btn-success" title="Terima"
                                        onclick="return confirm('Terima PO ini? Stok produk akan diperbarui.')">
                                    <i class="bi bi-check2-circle"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('purchase-orders.cancel', $po) }}" class="d-inline"
                                  data-confirm="Batalkan PO {{ $po->no_po }}?">
                                @csrf @method('PATCH')
                                <button class="btn btn-xs btn-outline-danger" title="Batal"><i class="bi bi-x-circle"></i></button>
                            </form>
                            @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada Purchase Order.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchaseOrders->hasPages())
    <div class="card-footer py-2 px-3">{{ $purchaseOrders->links() }}</div>
    @endif
</div>
@endsection
