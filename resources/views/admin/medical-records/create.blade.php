<style>
.input-compact {
    min-width: 75px;
    text-align: center;
}
</style>

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

                {{-- Ukuran Kacamata Lama --}}
                <div class="card mb-3">
                    <div class="card-header fw-semibold">Ukuran Kacamata Lama</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>SPH</th>
                                        <th>CYL</th>
                                        <th>AXIS</th>
                                        <th>PRISM</th>
                                        <th>ADD</th>
                                        <th>MPD</th>
                                        <th>CC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>
                                            <span class="badge bg-danger fs-6">OD</span>
                                            <div class="text-muted" style="font-size:.72rem">Kanan</div>
                                        </th>
                                        <td><input type="number" name="old_od_sph" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="old_od_cyl" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="old_od_axis" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="old_od_prism" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="old_od_add" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="old_od_mpd" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="text" name="old_od_cc" class="form-control input-compact" placeholder="3/60"></td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <span class="badge bg-info fs-6">OS</span>
                                            <div class="text-muted style="font-size:.72rem">Kiri</div>
                                        </th>
                                        <td><input type="number" name="old_os_sph" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="old_os_cyl" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="old_os_axis" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="old_os_prism" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="old_os_add" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="old_os_mpd" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="text" name="old_os_cc" class="form-control input-compact" placeholder="3/60"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Hasil Refraksi --}}
                <div class="card mb-3">
                    <div class="card-header fw-semibold">Hasil Refraksi</div>
                    <div class="card-body">

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle text-center">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>SC</th>
                                        <th>SPH</th>
                                        <th>CYL</th>
                                        <th>AXIS</th>
                                        <th>PRISM</th>
                                        <th>ADD</th>
                                        <th>MPD</th>
                                        <th>CC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>
                                            <span class="badge bg-danger fs-6">OD</span>
                                            <div class="text-muted" style="font-size:.72rem">Kanan</div>
                                        </th>
                                        <td><input type="text" name="ref_od_sc" class="form-control input-compact" placeholder="3/60"></td>
                                        <td><input type="number" name="ref_od_sph" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="ref_od_cyl" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="ref_od_axis" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="ref_od_prism" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="ref_od_add" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="ref_od_mpd" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="text" name="ref_od_cc" class="form-control input-compact" placeholder="3/60"></td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <span class="badge bg-info fs-6">OS</span>
                                            <div class="text-muted style="font-size:.72rem">Kiri</div>
                                        </th>
                                        <td><input type="text" name="ref_os_sc" class="form-control input-compact" placeholder="3/60"></td>
                                        <td><input type="number" name="ref_os_sph" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="ref_os_cyl" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="ref_os_axis" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="ref_os_prism" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="ref_os_add" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="ref_os_mpd" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="text" name="ref_os_cc" class="form-control input-compact" placeholder="3/60"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            {{-- Diagnosis --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Diagnosis <span class="text-danger">*</span>
                                </label>

                                {{-- Input tampil --}}
                                <input type="text"
                                    id="ref_diagnosis_search"
                                    name="ref_diagnosis"
                                    class="form-control @error('ref_diagnosis') is-invalid @enderror"
                                    placeholder="Cari atau ketik Diagnosis..."
                                    value="{{ old('ref_diagnosis', $selectedDiagnosis?->name) }}"
                                    autocomplete="off"
                                    required>

                                {{-- Dropdown hasil --}}
                                <div id="ref_diagnosis_result" class="list-group position-absolute w-100 shadow"
                                    style="z-index:1000;"></div>

                                @error('ref_diagnosis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Asal Resep / Dokter</label>
                                <input type="text" name="ref_doctor_name" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Resep</label>
                                <input type="date" name="ref_exam_date" class="form-control">
                            </div>

                            <div class="col-md-12 mt-2">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <input type="text" name="ref_notes" class="form-control">
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Resep Dokter --}}
                <div class="card mb-3">
                    <div class="card-header fw-semibold">Ukuran Resep Dokter</div>
                    <div class="card-body">

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle text-center">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>SPH</th>
                                        <th>CYL</th>
                                        <th>AXIS</th>
                                        <th>PRISM</th>
                                        <th>ADD</th>
                                        <th>MPD</th>
                                        <th>CC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>
                                            <span class="badge bg-danger fs-6">OD</span>
                                            <div class="text-muted" style="font-size:.72rem">Kanan</div>
                                        </th>
                                        <td><input type="number" name="rx_od_sph" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="rx_od_cyl" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="rx_od_axis" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="rx_od_prism" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="rx_od_add" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="rx_od_mpd" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="text" name="rx_od_cc" class="form-control input-compact" placeholder="3/60"></td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <span class="badge bg-info fs-6">OS</span>
                                            <div class="text-muted" style="font-size:.72rem">Kiri</div>
                                        </th>
                                        <td><input type="number" name="rx_os_sph" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="rx_os_cyl" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="rx_os_axis" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="rx_os_prism" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="rx_os_add" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="number" name="rx_os_mpd" class="form-control input-compact" step="0.25"
                                                min="-30" max="30" placeholder="0.00"></td>
                                        <td><input type="text" name="rx_os_cc" class="form-control input-compact" placeholder="3/60"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            {{-- Diagnosis --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Diagnosis <span class="text-danger">*</span>
                                </label>

                                {{-- Input tampil --}}
                                <input type="text"
                                    id="rx_diagnosis_search"
                                    name="rx_diagnosis"
                                    class="form-control @error('rx_diagnosis') is-invalid @enderror"
                                    placeholder="Cari atau ketik Diagnosis..."
                                    value="{{ old('rx_diagnosis', $selectedDiagnosis?->name) }}"
                                    autocomplete="off"
                                    required>

                                {{-- Dropdown hasil --}}
                                <div id="rx_diagnosis_result" class="list-group position-absolute w-100 shadow"
                                    style="z-index:1000;"></div>

                                @error('rx_diagnosis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Asal Resep / Dokter</label>
                                <input type="text" name="rx_doctor_name" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Resep</label>
                                <input type="date" name="rx_exam_date" class="form-control">
                            </div>

                            <div class="col-md-12 mt-2">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <input type="text" name="rx_notes" class="form-control">
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Tombol Aksi --}}
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

// refraksi diagnosis autocomplete
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('ref_diagnosis_search');
    const resultBox = document.getElementById('ref_diagnosis_result');

    let timeout = null;

    input.addEventListener('keyup', function () {
        const query = this.value;

        clearTimeout(timeout);

        if (query.length < 2) {
            resultBox.innerHTML = '';
            return;
        }

        timeout = setTimeout(() => {
            fetch(`{{ route('diagnoses.search') }}?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    resultBox.innerHTML = '';

                    data.forEach(item => {
                        const el = document.createElement('a');
                        el.href = "#";
                        el.classList.add('list-group-item', 'list-group-item-action');
                        el.textContent = `${item.name}`;

                        el.addEventListener('click', function (e) {
                            e.preventDefault();
                            input.value = `${item.name}`;
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

// recipe diagnosis autocomplete
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('rx_diagnosis_search');
    const resultBox = document.getElementById('rx_diagnosis_result');

    let timeout = null;

    input.addEventListener('keyup', function () {
        const query = this.value;

        clearTimeout(timeout);

        if (query.length < 2) {
            resultBox.innerHTML = '';
            return;
        }

        timeout = setTimeout(() => {
            fetch(`{{ route('diagnoses.search') }}?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    resultBox.innerHTML = '';

                    data.forEach(item => {
                        const el = document.createElement('a');
                        el.href = "#";
                        el.classList.add('list-group-item', 'list-group-item-action');
                        el.textContent = `${item.name}`;

                        el.addEventListener('click', function (e) {
                            e.preventDefault();
                            input.value = `${item.name}`;
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