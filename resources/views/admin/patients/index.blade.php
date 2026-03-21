@extends('layouts.admin')
@section('title', 'Data Pasien')
@section('page-title', 'Data Pasien')

@section('content')
<div class="card">
    <div class="card-header p-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div class="fw-semibold"><i class="bi bi-people text-primary me-2"></i>Daftar Pasien</div>
        <div class="d-flex gap-2 flex-wrap">
            <form class="d-flex gap-2" method="GET">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari nama / No. RM..." value="{{ request('search') }}" style="width:220px">
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
            </form>

            {{-- Import / Export --}}
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-arrow-down-up me-1"></i>Import/Export
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
                <i class="bi bi-plus-lg me-1"></i>Tambah Pasien
            </a>
            @endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    <th>No. RM</th>
                    <th>Nama</th>
                    <th>J/K</th>
                    <th>No. HP</th>
                    <th>Kunjungan Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $i => $p)
                <tr>
                    <td class="ps-3 text-muted">{{ $patients->firstItem() + $i }}</td>
                    <td><span class="badge bg-secondary">{{ $p->no_rm }}</span></td>
                    <td>
                        <a href="{{ route('patients.show', $p) }}" class="fw-semibold text-decoration-none">
                            {{ $p->nama }}
                        </a>
                        @if($p->tanggal_lahir)
                        <div class="text-muted" style="font-size:.75rem">{{ $p->umur }} tahun</div>
                        @endif
                    </td>
                    <td>{{ $p->jenis_kelamin == 'L' ? 'L' : ($p->jenis_kelamin == 'P' ? 'P' : '-') }}</td>
                    <td>{{ $p->no_hp ?? '-' }}</td>
                    <td>
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
                    <td colspan="7" class="text-center text-muted py-5">
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

@push('styles')
<style>.btn-xs { padding: 3px 8px; font-size: .75rem; }</style>
@endpush
