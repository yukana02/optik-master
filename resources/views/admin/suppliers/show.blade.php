@extends('layouts.admin')
@section('title', 'Detail Supplier')
@section('page-title', 'Detail Supplier')
@section('content')
<div class="row g-3">
<div class="col-lg-4">
<div class="card h-auto">
    <div class="card-header p-3">
        <i class="bi bi-truck text-primary me-2"></i>Profil Supplier
    </div>
    <div class="card-body p-4">
        <div class="mb-2"><span class="badge bg-light text-dark border fw-semibold fs-6">{{ $supplier->kode_supplier }}</span></div>
        <h5 class="fw-bold mb-1">{{ $supplier->nama }}</h5>
        <div class="mb-3">
            @if($supplier->is_active)
                <span class="badge badge-lunas">Aktif</span>
            @else
                <span class="badge badge-batal">Nonaktif</span>
            @endif
        </div>
        <table class="table table-sm table-borderless">
            <tr><td class="text-muted ps-0" style="width:110px">Kontak Person</td><td class="fw-semibold">{{ $supplier->kontak_person ?? '-' }}</td></tr>
            <tr><td class="text-muted ps-0">Telepon</td><td>{{ $supplier->telepon ?? '-' }}</td></tr>
            <tr><td class="text-muted ps-0">Email</td><td>{{ $supplier->email ?? '-' }}</td></tr>
            <tr><td class="text-muted ps-0">Alamat</td><td>{{ $supplier->alamat ?? '-' }}</td></tr>
            <tr><td class="text-muted ps-0">Bergabung</td><td>{{ $supplier->created_at->format('d M Y') }}</td></tr>
        </table>
        <div class="d-flex gap-2 mt-2">
            @can('supplier.edit')
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
            @endcan
        </div>
    </div>
</div>
</div>
<div class="col-lg-8">
<div class="card">
    <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-file-earmark-text text-info me-2"></i>Riwayat Purchase Order</span>
        @can('purchase_order.create')
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>Buat PO</a>
        @endcan
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">No. PO</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplier->purchaseOrders as $po)
                <tr>
                    <td class="ps-3 fw-semibold">{{ $po->no_po }}</td>
                    <td class="text-muted small">{{ $po->tanggal_po->format('d M Y') }}</td>
                    <td>Rp {{ number_format($po->total_harga,0,',','.') }}</td>
                    <td>
                        @php $sc=['draft'=>'secondary','dikirim'=>'info','diterima'=>'success','batal'=>'danger'] @endphp
                        <span class="badge bg-{{ $sc[$po->status] ?? 'secondary' }}">{{ ucfirst($po->status) }}</span>
                    </td>
                    <td class="text-muted small">{{ $po->user->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada PO dari supplier ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
@endsection
