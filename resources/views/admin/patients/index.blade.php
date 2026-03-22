@extends('layouts.admin')
@section('title', 'Data Pasien')
@section('page-title', 'Data Pasien')

@section('content')
<div class="card">
    <div class="card-header p-3">
        {{-- Baris filter + aksi --}}
        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
            <form class="d-flex gap-2 flex-grow-1" method="GET" style="max-width:380px">
                <input type="text" name="search" class="form-control form-control-sm flex-grow-1"
                       placeholder="Cari nama / No. RM..." value="{{ request('search') }}">
                <button class="btn btn-sm btn-outline-secondary flex-shrink-0">
                    <i class="bi bi-search"></i>
                </button>
                @if(request()->filled('search'))
                <a href="{{ route('patients.index') }}" class="btn btn-sm btn-light flex-shrink-0">Reset</a>
                @endif
            </form>
            <div class="d-flex gap-2 flex-wrap ms-auto">
                {{-- Import / Export --}}
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-arrow-down-up me-1"></i>
                        <span class="d-none d-sm-inline">Import/Export</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('import.index') }}">
                                <i class="bi bi-upload text-primary me-2"></i>Import Pasien (Excel/CSV)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('export.pasien') }}{{ request('search') ? '?search='.request('search') : '' }}">
                                <i class="bi bi-file-earmark-excel text-success me-2"></i>Export Pasien (.xlsx)
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('import.template', 'pasien') }}">
                                <i class="bi bi-file-earmark-spreadsheet text-secondary me-2"></i>Download Template
                            </a>
                        </li>
                    </ul>
                </div>
                @can('patient.create')
                <a href="{{ route('patients.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    <span class="d-none d-sm-inline">Tambah Pasien</span>
                    <span class="d-sm-none">Tambah</span>
                </a>
                @endcan
            </div>
        </div>
        <div class="text-muted small"><i class="bi bi-people text-primary me-1"></i>Daftar Pasien</div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">No. RM</th>
                    <th>Nama</th>
                    <th class="d-none d-md-table-cell">No. HP</th>
                    <th class="d-none d-lg-table-cell">Kunjungan Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $i => $p)
                <tr>
                    <td class="ps-3">
                        <span class="badge bg-secondary">{{ $p->no_rm }}</span>
                    </td>
                    <td>
                        <a href="{{ route('patients.show', $p) }}" class="fw-semibold text-decoration-none">
                            {{ $p->nama }}
                        </a>
                        @if($p->tanggal_lahir)
                        <div class="text-muted" style="font-size:.75rem">
                            {{ $p->umur }} th · {{ $p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </div>
                        @endif
                        {{-- Info mobile - tampil di bawah nama --}}
                        <small class="text-muted d-md-none">{{ $p->no_hp ?? '-' }}</small>
                    </td>
                    <td class="d-none d-md-table-cell text-muted">{{ $p->no_hp ?? '-' }}</td>
                    <td class="d-none d-lg-table-cell text-muted small">
                        @if($p->latestRecord)
                            {{ $p->latestRecord->tanggal_kunjungan->format('d M Y') }}
                        @else
                            <span class="text-muted">Belum ada</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('patients.show', $p) }}" class="btn btn-xs btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('patient.edit')
                            <a href="{{ route('patients.edit', $p) }}" class="btn btn-xs btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('patient.delete')
                            <form method="POST" action="{{ route('patients.destroy', $p) }}"
                                  data-confirm="Hapus pasien {{ $p->nama }}?">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                        Belum ada data pasien
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($patients->hasPages())
    <div class="card-footer">{{ $patients->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
