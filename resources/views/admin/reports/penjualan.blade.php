@extends('layouts.admin')
@section('title','Laporan Penjualan')
@section('page-title','Laporan Penjualan')

@section('content')
{{-- Filter Card --}}
<div class="card mb-3">
    <div class="card-body p-3">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-12 col-sm-6 col-md-auto">
                <label class="form-label small mb-1">Dari Tanggal</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-12 col-sm-6 col-md-auto">
                <label class="form-label small mb-1">Sampai Tanggal</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            {{-- [5] Filter Tipe Pasien --}}
            <div class="col-12 col-sm-6 col-md-auto">
                <label class="form-label small mb-1">Tipe Pasien</label>
                <select name="tipe_pasien" class="form-select form-select-sm" style="min-width:120px">
                    <option value="" {{ $tipePasien=='' ? 'selected':'' }}>Semua</option>
                    <option value="umum" {{ $tipePasien=='umum' ? 'selected':'' }}>
                        <i class="bi bi-person"></i> Umum
                    </option>
                    <option value="bpjs" {{ $tipePasien=='bpjs' ? 'selected':'' }}>
                        <i class="bi bi-shield-check"></i> BPJS
                    </option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
            @if(request()->anyFilled(['from','to','tipe_pasien']))
            <div class="col-auto">
                <a href="{{ route('reports.penjualan') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
            @endif
            <div class="col-12 col-md-auto ms-md-auto d-flex gap-2">
                <a href="{{ request()->fullUrlWithQuery(['export'=>'excel']) }}"
                   class="btn btn-success btn-sm flex-fill flex-md-grow-0">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                </a>
                <a href="{{ route('reports.penjualan.print') }}?{{ http_build_query(request()->except('export')) }}"
                   target="_blank" class="btn btn-outline-secondary btn-sm flex-fill flex-md-grow-0">
                    <i class="bi bi-printer me-1"></i>Cetak
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Total Transaksi</div>
            <div class="fs-3 fw-bold text-primary">{{ number_format($summary['total_transaksi']) }}</div>
            @if($tipePasien)
            <div class="text-muted" style="font-size:.72rem">
                <span class="badge {{ $tipePasien=='bpjs' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($tipePasien) }}</span>
            </div>
            @endif
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Total Omzet</div>
            <div class="fw-bold text-success" style="font-size:1.05rem">
                Rp {{ number_format($summary['total_omzet'],0,',','.') }}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Total Diskon + BPJS</div>
            <div class="fw-bold text-danger" style="font-size:1.05rem">
                Rp {{ number_format($summary['total_diskon'] + $summary['total_subsidi'],0,',','.') }}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Rata-rata/Transaksi</div>
            <div class="fw-bold text-info" style="font-size:1.05rem">
                Rp {{ number_format($summary['rata_per_trx'],0,',','.') }}
            </div>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header p-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div>
            Detail Penjualan {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
            @if($tipePasien)
                <span class="badge {{ $tipePasien=='bpjs' ? 'bg-success' : 'bg-secondary' }} ms-2">
                    {{ ucfirst($tipePasien) }}
                </span>
            @endif
        </div>
        <small class="text-muted">{{ $transactions->count() }} transaksi</small>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">No. Transaksi</th>
                    <th>Pasien</th>
                    <th>Tipe</th>
                    <th>Items</th>
                    <th>Diskon</th>
                    <th>Subsidi BPJS</th>
                    <th>Total Bayar</th>
                    <th>Metode</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td class="ps-3">
                        <a href="{{ route('transactions.show', $trx) }}" class="text-decoration-none fw-semibold">
                            {{ $trx->no_transaksi }}
                        </a>
                    </td>
                    <td>{{ $trx->patient->nama ?? 'Umum' }}</td>
                    <td>
                        @if(($trx->tipe_pasien ?? 'umum') === 'bpjs')
                            <span class="badge bg-success">BPJS</span>
                        @else
                            <span class="badge bg-secondary">Umum</span>
                        @endif
                    </td>
                    <td>{{ $trx->items->sum('qty') }} item</td>
                    <td>{{ $trx->diskon_nominal > 0 ? 'Rp '.number_format($trx->diskon_nominal,0,',','.') : '-' }}</td>
                    <td class="text-success">{{ ($trx->subsidi_bpjs??0) > 0 ? 'Rp '.number_format($trx->subsidi_bpjs,0,',','.') : '-' }}</td>
                    <td class="fw-semibold">Rp {{ number_format($trx->total_bayar,0,',','.') }}</td>
                    <td>{{ ucfirst($trx->metode_bayar) }}</td>
                    <td class="text-muted small">{{ $trx->created_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                        Tidak ada transaksi pada periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($transactions->count())
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="6" class="text-end ps-3">Total Omzet:</td>
                    <td>Rp {{ number_format($summary['total_omzet'],0,',','.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
