@extends('layouts.admin')
@section('title', 'Detail PO')
@section('page-title', 'Detail Purchase Order')
@section('content')
@php $sc=['draft'=>'secondary','dikirim'=>'info text-dark','diterima'=>'success','batal'=>'danger'] @endphp
<div class="row g-3">
<div class="col-12 col-lg-4">
    <div class="card">
        <div class="card-header p-3"><i class="bi bi-info-circle text-primary me-2"></i>Info PO</div>
        <div class="card-body p-4">
            <div class="mb-2">
                <span class="badge bg-{{ $sc[$purchaseOrder->status] ?? 'secondary' }} fs-6">{{ ucfirst($purchaseOrder->status) }}</span>
            </div>
            <h5 class="fw-bold">{{ $purchaseOrder->no_po }}</h5>
            <table class="table table-sm table-borderless mt-2">
                <tr><td class="text-muted ps-0">Supplier</td><td class="fw-semibold">{{ $purchaseOrder->supplier->nama }}</td></tr>
                <tr><td class="text-muted ps-0">Tanggal PO</td><td>{{ $purchaseOrder->tanggal_po->format('d M Y') }}</td></tr>
                <tr><td class="text-muted ps-0">Dibuat oleh</td><td>{{ $purchaseOrder->user->name }}</td></tr>
                @if($purchaseOrder->tanggal_terima)
                <tr><td class="text-muted ps-0">Tgl. Terima</td><td>{{ $purchaseOrder->tanggal_terima->format('d M Y') }}</td></tr>
                @endif
                @if($purchaseOrder->catatan)
                <tr><td class="text-muted ps-0">Catatan</td><td>{{ $purchaseOrder->catatan }}</td></tr>
                @endif
            </table>
            <div class="p-3 bg-primary bg-opacity-10 rounded-3 mt-2">
                <div class="text-muted small">Total Nilai PO</div>
                <div class="fw-bold fs-5 text-primary">Rp {{ number_format($purchaseOrder->total_harga,0,',','.') }}</div>
            </div>
            @if(in_array($purchaseOrder->status,['draft','dikirim']))
            @can('purchase_order.edit')
            <div class="d-flex gap-2 mt-3">
                <form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-success btn-sm" onclick="return confirm('Terima PO ini? Stok akan diperbarui.')">
                        <i class="bi bi-check2-circle me-1"></i>Terima
                    </button>
                </form>
                <form method="POST" action="{{ route('purchase-orders.cancel', $purchaseOrder) }}"
                      data-confirm="Batalkan PO {{ $purchaseOrder->no_po }}?">
                    @csrf @method('PATCH')
                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle me-1"></i>Batal</button>
                </form>
            </div>
            @endcan
            @endif
        </div>
    </div>
</div>
<div class="col-12 col-lg-8">
    <div class="card">
        <div class="card-header p-3"><i class="bi bi-box-seam text-primary me-2"></i>Item Produk</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr><th class="ps-3">#</th><th>Produk</th><th class="text-end">Qty</th><th class="text-end">Harga Beli</th><th class="text-end pe-3">Subtotal</th></tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $i => $item)
                    <tr>
                        <td class="ps-3 text-muted">{{ $i+1 }}</td>
                        <td>
                            <div class="fw-semibold">{{ $item->nama_produk }}</div>
                            @if($item->product)
                            <small class="text-muted">{{ $item->product->kode_produk }}</small>
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($item->qty) }}</td>
                        <td class="text-end">Rp {{ number_format($item->harga_beli,0,',','.') }}</td>
                        <td class="text-end pe-3 fw-semibold">Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <td colspan="4" class="text-end fw-bold ps-3">Total</td>
                        <td class="text-end pe-3 fw-bold text-primary">Rp {{ number_format($purchaseOrder->total_harga,0,',','.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</div>
@endsection
