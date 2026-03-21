@extends('layouts.admin')
@section('title', 'Purchase Order')
@section('page-title', 'Purchase Order (Stok Masuk)')
@section('content')
<div class="card">
    <div class="card-header p-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div><i class="bi bi-bag-check text-primary me-2"></i>Daftar Purchase Order</div>
        <div class="d-flex gap-2 flex-wrap">
            <form class="d-flex gap-2" method="GET">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="No. PO / Supplier" value="{{ request('search') }}" style="width:190px">
                <select name="status" class="form-select form-select-sm" style="width:120px">
                    <option value="">Semua Status</option>
                    @foreach(['draft','dikirim','diterima','batal'] as $st)
                    <option value="{{ $st }}" {{ request('status')==$st?'selected':'' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-filter"></i></button>
                @if(request()->anyFilled(['search','status']))
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-light">Reset</a>
                @endif
            </form>
            @can('purchase_order.create')
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>Buat PO Baru</a>
            @endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">No. PO</th><th>Supplier</th><th>Tgl. PO</th>
                    <th>Total</th><th>Status</th><th>Petugas</th><th>Tgl. Terima</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseOrders as $po)
                <tr>
                    <td class="ps-3 fw-semibold">{{ $po->no_po }}</td>
                    <td>{{ $po->supplier->nama ?? '-' }}</td>
                    <td class="text-muted small">{{ $po->tanggal_po->format('d M Y') }}</td>
                    <td>Rp {{ number_format($po->total_harga,0,',','.') }}</td>
                    <td>
                        @php $sc=['draft'=>'secondary','dikirim'=>'info text-dark','diterima'=>'success','batal'=>'danger'] @endphp
                        <span class="badge bg-{{ $sc[$po->status] ?? 'secondary' }}">{{ ucfirst($po->status) }}</span>
                    </td>
                    <td class="text-muted small">{{ $po->user->name ?? '-' }}</td>
                    <td class="text-muted small">{{ $po->tanggal_terima ? $po->tanggal_terima->format('d M Y') : '-' }}</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-light" title="Detail"><i class="bi bi-eye"></i></a>
                        @if(in_array($po->status,['draft','dikirim']))
                        @can('purchase_order.edit')
                        <form method="POST" action="{{ route('purchase-orders.receive', $po) }}" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-success" title="Terima & update stok"
                                    onclick="return confirm('Terima PO ini? Stok produk akan diperbarui.')">
                                <i class="bi bi-check2-circle"></i> Terima
                            </button>
                        </form>
                        <form method="POST" action="{{ route('purchase-orders.cancel', $po) }}" class="d-inline"
                              data-confirm="Batalkan PO {{ $po->no_po }}?">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-light text-danger" title="Batalkan"><i class="bi bi-x-circle"></i></button>
                        </form>
                        @endcan
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada Purchase Order.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchaseOrders->hasPages())
    <div class="card-footer py-2 px-3">{{ $purchaseOrders->links() }}</div>
    @endif
</div>
@endsection
