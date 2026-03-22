@extends('layouts.admin')
@section('title','Riwayat Transaksi')
@section('page-title','Riwayat Transaksi')
@section('content')
<div class="card">
    <div class="card-header p-3">
        <form class="row g-2 mb-2" method="GET">
            <div class="col-12 col-sm-6 col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="No. transaksi / pasien" value="{{ request('search') }}">
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="lunas"   {{ request('status')=='lunas'  ?'selected':'' }}>Lunas</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                    <option value="batal"   {{ request('status')=='batal'  ?'selected':'' }}>Batal</option>
                </select>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-auto d-flex gap-2">
                <button class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
                @if(request()->anyFilled(['search','status','from','to']))
                <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-light">Reset</a>
                @endif
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center">
            <div><i class="bi bi-receipt-cutoff text-primary me-1"></i>Semua Transaksi</div>
            @can('transaction.create')
            <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Transaksi Baru
            </a>
            @endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">No. Transaksi</th>
                    <th>Pasien</th>
                    <th class="d-none d-md-table-cell">Kasir</th>
                    <th>Total</th>
                    <th class="d-none d-lg-table-cell">Metode</th>
                    <th>Status</th>
                    <th class="d-none d-md-table-cell">Tanggal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td class="ps-3 fw-semibold" style="font-size:.82rem">{{ $trx->no_transaksi }}</td>
                    <td>
                        <div style="font-size:.85rem">{!! $trx->patient->nama ?? '<span class="text-muted">Umum</span>' !!}</div>
                        <small class="text-muted d-md-none">{{ $trx->created_at->format('d M H:i') }}</small>
                    </td>
                    <td class="d-none d-md-table-cell text-muted small">{{ $trx->kasir->name ?? '-' }}</td>
                    <td class="fw-semibold" style="font-size:.85rem">Rp {{ number_format($trx->total_bayar,0,',','.') }}</td>
                    <td class="d-none d-lg-table-cell">
                        <span class="badge bg-light text-dark border">{{ ucfirst($trx->metode_bayar) }}</span>
                    </td>
                    <td><span class="badge badge-{{ $trx->status }}">{{ ucfirst($trx->status) }}</span></td>
                    <td class="d-none d-md-table-cell text-muted small">{{ $trx->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <a href="{{ route('transactions.show',$trx) }}" class="btn btn-xs btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-5">
                    <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
                    Belum ada transaksi
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer">{{ $transactions->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
