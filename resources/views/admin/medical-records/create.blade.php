@extends('layouts.admin')
@section('title', 'Rekam Medis Baru')
@section('page-title', 'Input Rekam Medis')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <form method="POST" action="{{ route('medical-records.store') }}">
                @csrf

                {{-- Pilih Pasien --}}
                <div class="card mb-3">
                    <div class="card-header p-3"><i class="bi bi-person text-primary me-2"></i>Data Pasien & Kunjungan</div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    Pasien <span class="text-danger">*</span>
                                </label>

                                {{-- Hidden ID --}}
                                <input type="hidden" name="patient_id" id="patient_id"
                                    value="{{ old('patient_id', $selectedPatient?->id) }}">

                                {{-- Input tampil --}}
                                <input type="text"
                                    id="patient_search"
                                    class="form-control @error('patient_id') is-invalid @enderror"
                                    placeholder="Cari No RM / Nama pasien..."
                                    value="{{ old('patient_id') ? $selectedPatient?->no_rm.' — '.$selectedPatient?->nama : '' }}"
                                    autocomplete="off"
                                    required>

                                {{-- Dropdown hasil --}}
                                <div id="patient_result" class="list-group position-absolute w-100 shadow"
                                    style="z-index:1000;"></div>

                                @error('patient_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Dokter / Pemeriksa <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                    name="nama_dokter"
                                    class="form-control @error('nama_dokter') is-invalid @enderror"
                                    value="{{ old('nama_dokter') }}"
                                    placeholder="Masukkan nama dokter"
                                    required>

                                @error('nama_dokter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tanggal Kunjungan <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_kunjungan"
                                    class="form-control @error('tanggal_kunjungan') is-invalid @enderror"
                                    value="{{ old('tanggal_kunjungan', today()->format('Y-m-d')) }}" required>
                                @error('tanggal_kunjungan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Keluhan Utama</label>
                                <input type="text" name="keluhan" class="form-control" value="{{ old('keluhan') }}"
                                    placeholder="Penglihatan buram, mata lelah, sakit kepala...">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Resep Kacamata --}}
                <div class="card mb-3">
                    <div class="card-header p-3">
                        <i class="bi bi-eyeglasses text-primary me-2"></i>Resep Kacamata
                        <small class="text-muted ms-2">SPH = Spheris | CYL = Silinder | AXIS (0–180°) | ADD = Addisi | PD =
                            Pupil Distance</small>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width:120px">Mata</th>
                                        <th>SPH</th>
                                        <th>CYL</th>
                                        <th>AXIS</th>
                                        <th>ADD</th>
                                        <th>MPD</th>
                                        <th>Visus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-danger fs-6">OD</span>
                                            <div class="text-muted" style="font-size:.72rem">Kanan</div>
                                        </td>
                                        <td><input type="number" name="od_sph" class="form-control text-center" step="0.25"
                                                min="-30" max="30" value="{{ old('od_sph') }}" placeholder="0.00"></td>
                                        <td><input type="number" name="od_cyl" class="form-control text-center" step="0.25"
                                                min="-10" max="10" value="{{ old('od_cyl') }}" placeholder="0.00"></td>
                                        <td><input type="number" name="od_axis" class="form-control text-center" min="0"
                                                max="180" value="{{ old('od_axis') }}" placeholder="0"></td>
                                        <td><input type="number" name="od_add" class="form-control text-center" step="0.25"
                                                min="0" max="5" value="{{ old('od_add') }}" placeholder="0.00"></td>
                                        <td><input type="number" name="od_pd" class="form-control text-center" step="0.5"
                                                min="20" max="40" value="{{ old('od_pd') }}" placeholder="0.0"></td>
                                        <td><input type="text" name="od_vis" class="form-control text-center" step="0.1"
                                                min="0" max="2" value="{{ old('od_vis') }}" placeholder="6/6"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-info fs-6">OS</span>
                                            <div class="text-muted" style="font-size:.72rem">Kiri</div>
                                        </td>
                                        <td><input type="number" name="os_sph" class="form-control text-center" step="0.25"
                                                min="-30" max="30" value="{{ old('os_sph') }}" placeholder="0.00"></td>
                                        <td><input type="number" name="os_cyl" class="form-control text-center" step="0.25"
                                                min="-10" max="10" value="{{ old('os_cyl') }}" placeholder="0.00"></td>
                                        <td><input type="number" name="os_axis" class="form-control text-center" min="0"
                                                max="180" value="{{ old('os_axis') }}" placeholder="0"></td>
                                        <td><input type="number" name="os_add" class="form-control text-center" step="0.25"
                                                min="0" max="5" value="{{ old('os_add') }}" placeholder="0.00"></td>
                                        <td><input type="number" name="os_pd" class="form-control text-center" step="0.5"
                                                min="20" max="40" value="{{ old('os_pd') }}" placeholder="0.0"></td>
                                        <td><input type="text" name="os_vis" class="form-control text-center" step="0.1"
                                                min="0" max="2" value="{{ old('os_vis') }}" placeholder="6/6"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">PD Total / Binokular</label>
                                <input type="number" name="pd_total" class="form-control" min="40" max="80"
                                    value="{{ old('pd_total') }}" placeholder="60">
                            </div>

                            {{-- diagnosis --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Diagnosis</label>
                                <select name="diagnosis" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="Myopia Astigmatism" {{ old('diagnosis') == 'Myopia Astigmatism' ? 'selected' : '' }}>
                                        Myopia Astigmatism</option>
                                    <option value="Myopia + Presbyopia" {{ old('diagnosis') == 'Myopia + Presbyopia' ? 'selected' : '' }}>Myopia + Presbyopia
                                    </option>
                                    <option value="Myopia Astigmatism + Presbyopia" {{ old('diagnosis') == 'Myopia Astigmatism + Presbyopia' ? 'selected' : '' }}>
                                        Myopia Astigmatism + Presbyopia</option>
                                    <option value="Hypermetropia + Astigmatism" {{ old('diagnosis') == 'Hypermetropia + Astigmatism' ? 'selected' : '' }}>
                                        Hypermetropia + Astigmatism</option>
                                    <option value="Hypermetropia + Presbyopia" {{ old('diagnosis') == 'Hypermetropia + Presbyopia' ? 'selected' : '' }}>
                                        Hypermetropia + Presbyopia</option>
                                    <option value="Hypermetropia Astigmatism + Presbyopia" {{ old('diagnosis') == 'Hypermetropia Astigmatism + Presbyopia' ? 'selected' : '' }}>
                                        Hypermetropia Astigmatism + Presbyopia</option>
                                    <option value="Astigmatism + Presbyopia" {{ old('diagnosis') == 'Astigmatism + Presbyopia' ? 'selected' : '' }}>
                                        Astigmatism + Presbyopia</option>
                                </select>
                            </div>

                            {{-- catatan --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan Dokter</label>
                                <textarea name="catatan" class="form-control" rows="3"
                                    placeholder="Catatan tambahan, anjuran pemakaian, kontrol ulang...">{{ old('catatan') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i>Simpan Rekam Medis
                    </button>
                    <a href="{{ route('medical-records.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>

            </form>
        </div>
    </div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('patient_search');
    const resultBox = document.getElementById('patient_result');
    const hidden = document.getElementById('patient_id');

    let timeout = null;

    input.addEventListener('keyup', function () {
        const query = this.value;

        clearTimeout(timeout);

        if (query.length < 2) {
            resultBox.innerHTML = '';
            return;
        }

        timeout = setTimeout(() => {
            fetch(`{{ route('patients.autocomplete') }}?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    resultBox.innerHTML = '';

                    data.forEach(item => {
                        const el = document.createElement('a');
                        el.href = "#";
                        el.classList.add('list-group-item', 'list-group-item-action');
                        el.textContent = `${item.no_rm} — ${item.nama}`;

                        el.addEventListener('click', function (e) {
                            e.preventDefault();
                            input.value = `${item.no_rm} — ${item.nama}`;
                            hidden.value = item.id;
                            resultBox.innerHTML = '';
                        });

                        resultBox.appendChild(el);
                    });
                });
        }, 300);
    });

    // klik luar -> close dropdown
    document.addEventListener('click', function (e) {
        if (!input.contains(e.target)) {
            resultBox.innerHTML = '';
        }
    });
});
</script>