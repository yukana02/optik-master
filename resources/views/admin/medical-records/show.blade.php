{{-- resources/views/admin/medical-records/show.blade.php --}}
@extends('layouts.admin')
@section('title','History Rekam Medis')
@section('page-title','History Rekam Medis')

@section('content')
<div class="row justify-content-center">
<div class="col-xl-9">

{{-- Info Header --}}
<div class="card mb-3">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-1">{{ $medicalRecord->patient->nama }}</h5>
                <span class="badge bg-secondary me-2">{{ $medicalRecord->patient->no_rm }}</span>
                <span class="badge bg-primary">{{ $medicalRecord->tanggal_kunjungan->format('d M Y') }}</span>
            </div>
            <div class="d-flex gap-2">
                @can('medical_record.create')
                <a href="{{ route('medical-records.create',$medicalRecord) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus me-1"></i>Rekam Medis Baru
                </a>
                @endcan
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-printer me-1"></i>Cetak
                </button>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted ps-0" style="width:120px">Dokter</td><td>: {{ $medicalRecord->nama_dokter ?? '-' }}</td></tr>
                    <tr><td class="text-muted ps-0">Keluhan</td><td>: {{ $medicalRecord->keluhan ?? '-' }}</td></tr>
                    <tr><td class="text-muted ps-0">Diagnosis</td><td>: {{ $medicalRecord->diagnosis ?? '-' }}</td></tr>
                    <tr><td class="text-muted ps-0">PD Total</td><td>: {{ $medicalRecord->pd_total ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Resep --}}
<div class="card mb-3">
    <div class="card-header p-3">
        <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Refraksi
    </div>

    <div class="card-body p-3">
        @forelse($histories as $history)
            <div class="card mb-3 border">
                
                {{-- Header --}}
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            {{ $history->tanggal_kunjungan->format('d M Y') }}
                        </span>
                        <span class="ms-2 text-muted">
                            oleh <strong>{{ $history->createdBy->name ?? '-' }}</strong>
                        </span>
                    </div>

                    <a href="{{ route('medical-records.detail', $history->id) }}" 
                       class="btn btn-sm btn-outline-primary">
                        Detail
                    </a>
                </div>
                
                {{-- Dokter --}}
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted ps-0" style="width:120px">Dokter</td><td>: {{ $history->nama_dokter ?? '-' }}</td></tr>
                            <tr><td class="text-muted ps-0">Keluhan</td><td>: {{ $history->keluhan ?? '-' }}</td></tr>
                            <tr><td class="text-muted ps-0">Jenis Lensa</td><td>: {{ $history->jenis_lensa ?? '-' }}</td></tr>
                        </table>
                    </div>
                </div>

                {{-- Body --}}
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:120px">Mata</th>
                                    <th>SPH</th>
                                    <th>CYL</th>
                                    <th>AXIS</th>
                                    <th>ADD</th>
                                    <th>PD</th>
                                    <th>Visus</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- OD --}}
                                <tr>
                                    <td>
                                        <span class="badge bg-danger">OD</span>
                                    </td>
                                    <td>{{ $history->formatResep($history->od_sph) }}</td>
                                    <td>{{ $history->formatResep($history->od_cyl) }}</td>
                                    <td>{{ $history->od_axis ? $history->od_axis.'°' : '-' }}</td>
                                    <td>{{ $history->od_add ? '+'.number_format($history->od_add,2) : '-' }}</td>
                                    <td>{{ $history->od_pd ?? '-' }}</td>
                                    <td>{{ $history->od_vis ?? '-' }}</td>
                                </tr>

                                {{-- OS --}}
                                <tr>
                                    <td>
                                        <span class="badge bg-info">OS</span>
                                    </td>
                                    <td>{{ $history->formatResep($history->os_sph) }}</td>
                                    <td>{{ $history->formatResep($history->os_cyl) }}</td>
                                    <td>{{ $history->os_axis ? $history->os_axis.'°' : '-' }}</td>
                                    <td>{{ $history->os_add ? '+'.number_format($history->os_add,2) : '-' }}</td>
                                    <td>{{ $history->os_pd ?? '-' }}</td>
                                    <td>{{ $history->os_vis ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Catatan --}}
                    @if($history->catatan)
                        <div class="alert alert-light border mt-3 mb-0">
                            <strong>Catatan:</strong>
                            <div class="mt-1">{{ $history->catatan }}</div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-3">
                Tidak ada riwayat sebelumnya
            </div>
        @endforelse
    </div>
</div>

{{-- Transaksi Terkait --}}
@if($medicalRecord->transaction)
<div class="card mb-3">
    <div class="card-header p-3"><i class="bi bi-receipt text-success me-2"></i>Transaksi Terkait</div>
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-bold">{{ $medicalRecord->transaction->no_transaksi }}</div>
                <div class="text-muted small">{{ $medicalRecord->transaction->created_at->format('d M Y H:i') }}</div>
            </div>
            <div class="text-end">
                <div class="fw-bold text-primary fs-5">
                    Rp {{ number_format($medicalRecord->transaction->total_bayar,0,',','.') }}
                </div>
                <span class="badge badge-{{ $medicalRecord->transaction->status }}">
                    {{ ucfirst($medicalRecord->transaction->status) }}
                </span>
            </div>
            <a href="{{ route('transactions.show',$medicalRecord->transaction) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-eye me-1"></i>Lihat Struk
            </a>
        </div>
    </div>
</div>
@endif

<div class="d-flex gap-2">
    <a href="{{ route('patients.show',$medicalRecord->patient) }}" class="btn btn-outline-secondary">
        <i class="bi bi-person me-1"></i>Profil Pasien
    </a>
    <a href="{{ route('medical-records.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

</div>
</div>
@endsection
