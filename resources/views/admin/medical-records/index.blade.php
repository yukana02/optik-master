@extends('layouts.admin')
@section('title', 'Rekam Medis')
@section('page-title', 'Rekam Medis')

@section('content')
<div class="card">
    <div class="card-header p-3">
        <form class="row g-2 mb-2" method="GET">
            <div class="col-12 col-sm-6 col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Nama / No. RM..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-sm-4 col-md-3">
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-6 col-sm-4 col-md-3">
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-auto d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                @if(request()->anyFilled(['search','from','to']))
                <a href="{{ route('medical-records.index') }}" class="btn btn-sm btn-light">Reset</a>
                @endif
            </div>
        </form>
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <div><i class="bi bi-clipboard2-pulse text-primary me-1"></i>Daftar Rekam Medis</div>
            @can('medical_record.create')
            <a href="{{ route('medical-records.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Rekam Medis Baru
            </a>
            @endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Tanggal</th>
                    <th class="d-none d-md-table-cell">No. RM</th>
                    <th>Pasien</th>
                    <th class="d-none d-lg-table-cell">OD (Kanan)</th>
                    <th class="d-none d-lg-table-cell">OS (Kiri)</th>
                    <th class="d-none d-md-table-cell">Dokter</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $rm)
                <tr>
                    <td class="ps-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:.75rem">
                            {{ $rm->tanggal_kunjungan->format('d M Y') }}
                        </span>
                    </td>
                    <td class="d-none d-md-table-cell">
                        <span class="badge bg-secondary">{{ $rm->patient->no_rm }}</span>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $rm->patient->nama }}</div>
                        <small class="text-muted d-md-none">{{ $rm->patient->no_rm }}</small>
                        <small class="text-muted d-lg-none d-md-block" style="font-size:.72rem">
                            OD: {{ $rm->formatResep($rm->od_sph) }} / OS: {{ $rm->formatResep($rm->os_sph) }}
                        </small>
                    </td>
                    <td class="d-none d-lg-table-cell" style="font-size:.82rem">
                        <span class="text-muted">SPH</span> {{ $rm->formatResep($rm->od_sph) }}
                        <span class="text-muted ms-1">CYL</span> {{ $rm->formatResep($rm->od_cyl) }}
                    </td>
                    <td class="d-none d-lg-table-cell" style="font-size:.82rem">
                        <span class="text-muted">SPH</span> {{ $rm->formatResep($rm->os_sph) }}
                        <span class="text-muted ms-1">CYL</span> {{ $rm->formatResep($rm->os_cyl) }}
                    </td>
                    <td class="d-none d-md-table-cell text-muted small">{{ $rm->dokter->name ?? '-' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('medical-records.show', $rm) }}" class="btn btn-xs btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('medical_record.edit')
                            <a href="{{ route('medical-records.edit', $rm) }}" class="btn btn-xs btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('medical_record.delete')
                            <form method="POST" action="{{ route('medical-records.destroy', $rm) }}"
                                  data-confirm="Hapus rekam medis ini?">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-clipboard2 fs-1 d-block mb-2 opacity-25"></i>
                        Belum ada rekam medis
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($records->hasPages())
    <div class="card-footer">{{ $records->links() }}</div>
    @endif
</div>
@endsection
