@extends('layouts.admin')
@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@push('styles')
<style>
@media print {
    .no-print, .topbar, .sidebar, .sidebar-overlay { display: none !important; }
    .main-wrapper { margin-left: 0 !important; }
    .content { padding: 0 !important; }
    .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
    #struk { max-width: 100% !important; }
}
</style>
@endpush

@section('content')
<div class="row justify-content-center">
<div class="col-md-8 col-lg-6">

<div class="card mb-3" id="struk">
    <div class="card-body p-4">
        {{-- Header --}}
        <div class="text-center mb-4">
            <h5 class="fw-bold mb-0">OPTIK STORE</h5>
            <div class="text-muted small">Jl. Contoh No. 1, Jakarta</div>
            <div class="text-muted small">Telp: 021-xxxxxxx</div>
            <hr>
            <div class="fw-bold">STRUK PEMBAYARAN</div>
            <span class="badge {{ $transaction->status === 'lunas' ? 'badge-lunas' : ($transaction->status === 'batal' ? 'badge-batal' : 'badge-pending') }} px-3 py-1 mt-1">
                {{ strtoupper($transaction->status) }}
            </span>
        </div>

        {{-- Info Transaksi --}}
        <div class="row mb-3 g-2">
            <div class="col-sm-6">
                <table class="table table-sm table-borderless mb-0" style="font-size:.85rem">
                    <tr>
                        <td class="text-muted ps-0 pe-2">No. Transaksi</td>
                        <td><strong>{{ $transaction->no_transaksi }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0 pe-2">Tanggal</td>
                        <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0 pe-2">Kasir</td>
                        <td>{{ $transaction->kasir->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-6">
                <table class="table table-sm table-borderless mb-0" style="font-size:.85rem">
                    <tr>
                        <td class="text-muted ps-0 pe-2">Pasien</td>
                        <td>{{ $transaction->patient->nama ?? 'Umum' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0 pe-2">No. RM</td>
                        <td>{{ $transaction->patient->no_rm ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0 pe-2">Tipe</td>
                        <td>
                            @if(($transaction->tipe_pasien ?? 'umum') === 'bpjs')
                            <span class="badge bg-success">BPJS</span>
                            @else
                            <span class="badge bg-secondary">Umum</span>
                            @endif
                        </td>
                    </tr>
                    @if(($transaction->tipe_pasien ?? 'umum') === 'bpjs')
                    <tr>
                        <td class="text-muted ps-0 pe-2">No. BPJS</td>
                        <td>{{ $transaction->no_bpjs }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted ps-0 pe-2">Metode</td>
                        <td>{{ ucfirst($transaction->metode_bayar) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Items --}}
        <table class="table table-sm table-bordered mb-3">
            <thead class="table-light">
                <tr>
                    <th>Produk</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                <tr>
                    <td>{{ $item->nama_produk }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-end">Rp {{ number_format($item->harga_satuan,0,',','.') }}</td>
                    <td class="text-end">Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <table class="table table-sm table-borderless ms-auto" style="max-width:320px;font-size:.9rem">
            <tr>
                <td class="text-muted">Subtotal</td>
                <td class="text-end">Rp {{ number_format($transaction->total_harga,0,',','.') }}</td>
            </tr>
            @if($transaction->diskon_nominal > 0)
            <tr>
                <td class="text-muted">Diskon{{ $transaction->diskon_persen > 0 ? ' ('.$transaction->diskon_persen.'%)' : '' }}</td>
                <td class="text-end text-danger">- Rp {{ number_format($transaction->diskon_nominal,0,',','.') }}</td>
            </tr>
            @endif
            @if(($transaction->subsidi_bpjs ?? 0) > 0)
            <tr>
                <td class="text-muted"><i class="bi bi-shield-check text-success me-1"></i>Subsidi BPJS</td>
                <td class="text-end text-success">- Rp {{ number_format($transaction->subsidi_bpjs,0,',','.') }}</td>
            </tr>
            @endif
            <tr class="fw-bold border-top">
                <td>Total Bayar</td>
                <td class="text-end fs-5">Rp {{ number_format($transaction->total_bayar,0,',','.') }}</td>
            </tr>
            <tr>
                <td class="text-muted">Bayar</td>
                <td class="text-end">Rp {{ number_format($transaction->bayar,0,',','.') }}</td>
            </tr>
            <tr>
                <td class="text-muted">Kembalian</td>
                <td class="text-end">Rp {{ number_format($transaction->kembalian,0,',','.') }}</td>
            </tr>
        </table>

        @if($transaction->catatan)
        <div class="alert alert-light mb-3 py-2">
            <small class="text-muted">Catatan: {{ $transaction->catatan }}</small>
        </div>
        @endif

        <div class="text-center mt-3 text-muted small">
            <i class="bi bi-heart-fill text-danger"></i> Terima kasih telah berbelanja di Optik Store
        </div>
    </div>
</div>

{{-- Action buttons --}}
<div class="d-flex flex-wrap gap-2 justify-content-center no-print">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="bi bi-printer me-1"></i>Cetak Struk
    </button>
    <a href="{{ route('transactions.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle me-1"></i>Transaksi Baru
    </a>
    @if(auth()->user()->hasAnyRole(['super_admin','admin']))
    @if($transaction->status !== 'batal')
    <form method="POST" action="{{ route('transactions.cancel', $transaction) }}"
          data-confirm="Batalkan transaksi ini? Stok akan dikembalikan.">
        @csrf @method('PATCH')
        <button class="btn btn-outline-danger">
            <i class="bi bi-x-circle me-1"></i>Batalkan
        </button>
    </form>
    @endif
    @endif
    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>
</div>
</div>
@push('scripts')
<script>
// Invoice button injection - add to action area
document.addEventListener('DOMContentLoaded', function() {
    var printBtns = document.querySelectorAll('.no-print');
    if (printBtns.length > 0) {
        var btn = document.createElement('a');
        btn.href = "{{ route('transactions.invoice', $transaction) }}";
        btn.target = "_blank";
        btn.className = "btn btn-primary";
        btn.innerHTML = '<i class="bi bi-printer"></i> Cetak Invoice';
        printBtns[0].prepend(btn);
    }
});
</script>
@endpush
@endsection
