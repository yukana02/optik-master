@extends('layouts.admin')
@section('title', 'Mutasi Stok')
@section('page-title', 'Riwayat Mutasi Stok')

@section('content')
{{-- [2] Filter Card — tombol Cetak ada di dalam, sejajar Filter dan Reset --}}
<div class="card mb-3">
    <div class="card-body p-3">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small mb-1">Produk</label>
                <select name="product_id" class="form-select form-select-sm">
                    <option value="">Semua Produk</option>
                    @foreach($productList as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->kode_produk }} — {{ $p->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <label class="form-label small mb-1">Tipe</label>
                <select name="tipe" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="masuk"      {{ request('tipe')=='masuk'      ? 'selected':'' }}>Masuk</option>
                    <option value="keluar"     {{ request('tipe')=='keluar'     ? 'selected':'' }}>Keluar</option>
                    <option value="retur"      {{ request('tipe')=='retur'      ? 'selected':'' }}>Retur</option>
                    <option value="adjustment" {{ request('tipe')=='adjustment' ? 'selected':'' }}>Adjustment</option>
                </select>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <label class="form-label small mb-1">Dari</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <label class="form-label small mb-1">Sampai</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            {{-- [2] Tombol Filter, Reset, Export, Cetak sejajar dalam satu baris --}}
            <div class="col-auto d-flex gap-2 flex-wrap">
                <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('reports.mutasi-stok') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                <a href="{{ request()->fullUrlWithQuery(['export'=>'excel']) }}"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel me-1"></i>Excel
                </a>
                <a href="{{ route('reports.mutasi-stok.print') }}?{{ http_build_query(request()->except('export')) }}"
                   target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-printer me-1"></i>Cetak
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <div><i class="bi bi-arrow-left-right text-primary me-2"></i>Riwayat Mutasi Stok</div>
        <small class="text-muted">{{ $movements->total() }} record</small>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Tanggal</th>
                    <th>Produk</th>
                    <th class="text-center">Tipe</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Stok Sebelum</th>
                    <th class="text-center">Stok Sesudah</th>
                    <th>Keterangan</th>
                    <th>Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $m)
                <tr>
                    <td class="ps-3 text-muted small">{{ $m->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <div class="fw-semibold" style="font-size:.85rem">{{ $m->product->nama ?? '-' }}</div>
                        <small class="text-muted">{{ $m->product->kode_produk ?? '' }}</small>
                    </td>
                    <td class="text-center">
                        @php $tipeMap=['masuk'=>['bg-success','Masuk'],'keluar'=>['bg-danger','Keluar'],'retur'=>['bg-info','Retur'],'adjustment'=>['bg-warning text-dark','Adjust']]; [$cls,$lbl]=$tipeMap[$m->tipe]??['bg-secondary','?']; @endphp
                        <span class="badge {{ $cls }}">{{ $lbl }}</span>
                    </td>
                    <td class="text-center fw-semibold {{ in_array($m->tipe,['keluar']) ? 'text-danger' : 'text-success' }}">
                        {{ in_array($m->tipe,['keluar']) ? '-' : '+' }}{{ $m->qty }}
                    </td>
                    <td class="text-center">{{ $m->stok_sebelum }}</td>
                    <td class="text-center fw-bold">{{ $m->stok_sesudah }}</td>
                    <td class="text-muted small">{{ $m->keterangan ?: '-' }}</td>
                    <td class="text-muted small">{{ $m->user->name ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                        Belum ada data mutasi stok
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($movements->hasPages())
    <div class="p-3">{{ $movements->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
