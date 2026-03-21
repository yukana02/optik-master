@extends('layouts.admin')
@section('title', 'Import & Export Data')
@section('page-title', 'Import & Export Data')

@push('styles')
<style>
/* ── Tabs ── */
.import-tab-btn {
    background: none; border: none; padding: 10px 20px;
    font-weight: 600; font-size: .9rem; color: #6c757d;
    border-bottom: 2px solid transparent;
    cursor: pointer; transition: all .15s;
}
.import-tab-btn.active { color: #1e2a5e; border-bottom-color: #1e2a5e; }
.import-tab-panel { display: none; }
.import-tab-panel.active { display: block; }

/* ── Drop zone ── */
.drop-zone {
    border: 2px dashed #cbd5e1; border-radius: 12px;
    padding: 36px 20px; text-align: center; cursor: pointer;
    transition: border-color .2s, background .2s;
    background: #f8fafc;
}
.drop-zone:hover, .drop-zone.drag-over {
    border-color: #1e2a5e; background: #eef1fb;
}
.drop-zone .dz-icon { font-size: 2.5rem; color: #94a3b8; margin-bottom: 10px; }
.drop-zone.has-file { border-color: #059669; background: #f0fdf4; }
.drop-zone.has-file .dz-icon { color: #059669; }

/* ── Mode options ── */
.mode-card {
    border: 1.5px solid #e2e8f0; border-radius: 10px;
    padding: 12px 14px; cursor: pointer; transition: all .15s;
}
.mode-card:hover { border-color: #1e2a5e; }
.mode-card:has(input:checked) { border-color: #1e2a5e; background: #eef1fb; }
.mode-title { font-weight: 600; font-size: .88rem; margin-bottom: 2px; }
.mode-desc  { font-size: .78rem; color: #64748b; }

/* ── Result panel ── */
.result-row-ok  { background: #f0fdf4; }
.result-row-err { background: #fff1f2; }
.err-badge  { display: inline-flex; align-items: center; gap: 4px; font-size: .75rem;
              background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 20px; }
.ok-badge   { display: inline-flex; align-items: center; gap: 4px; font-size: .75rem;
              background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 20px; }

/* ── Export cards ── */
.export-card {
    border: 1.5px solid #e2e8f0; border-radius: 12px;
    padding: 20px; transition: border-color .15s, box-shadow .15s;
}
.export-card:hover { border-color: #1e2a5e; box-shadow: 0 2px 12px rgba(30,42,94,.1); }

/* ── Steps ── */
.step-badge {
    width: 26px; height: 26px; border-radius: 50%; font-size: .78rem;
    font-weight: 700; display: inline-flex; align-items: center; justify-content: center;
    background: #1e2a5e; color: #fff; flex-shrink: 0;
}
</style>
@endpush

@section('content')

{{-- ── Import Result Banner ── --}}
@if(session('import_result'))
@php $result = session('import_result'); @endphp
<div class="card mb-4 border-0" style="box-shadow: 0 2px 16px rgba(0,0,0,.1)">
    <div class="card-header p-3 d-flex align-items-center gap-2 {{ $result['gagal'] ? 'bg-warning bg-opacity-10' : 'bg-success bg-opacity-10' }}">
        <i class="bi {{ $result['gagal'] ? 'bi-exclamation-circle text-warning' : 'bi-check-circle-fill text-success' }} fs-5"></i>
        <span class="fw-bold">Hasil Import {{ $result['tipe'] }}</span>
    </div>
    <div class="card-body p-3">
        <div class="row g-3 mb-3">
            <div class="col-auto">
                <div class="d-flex align-items-center gap-2">
                    <span class="ok-badge"><i class="bi bi-check"></i> Berhasil</span>
                    <span class="fw-bold fs-5">{{ $result['sukses'] }}</span> baris
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex align-items-center gap-2">
                    <span class="err-badge"><i class="bi bi-x"></i> Gagal</span>
                    <span class="fw-bold fs-5">{{ count($result['gagal']) }}</span> baris
                </div>
            </div>
        </div>
        @if($result['gagal'])
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">Baris</th>
                        <th>Data</th>
                        <th>Alasan Gagal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result['gagal'] as $err)
                    <tr class="result-row-err">
                        <td class="text-center">{{ $err['baris'] }}</td>
                        <td>{{ $err['data'] }}</td>
                        <td class="text-danger small">{{ $err['alasan'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endif



{{-- ── Tab Header ── --}}
<div class="card border-0" style="box-shadow: 0 2px 16px rgba(0,0,0,.08)">
    <div class="card-header bg-white p-0 border-bottom">
        <div class="d-flex">
            <button class="import-tab-btn active" onclick="switchTab('import', this)">
                <i class="bi bi-upload me-2"></i>Import Data
            </button>
            <button class="import-tab-btn" onclick="switchTab('export', this)">
                <i class="bi bi-download me-2"></i>Export Data
            </button>
        </div>
    </div>

    <div class="card-body p-4">

        {{-- ══════════════ TAB: IMPORT ══════════════ --}}
        <div class="import-tab-panel active" id="tab-import">

            {{-- Import Produk --}}
            <div class="card mb-4 border">
                <div class="card-header p-3 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bi bi-box-seam text-primary me-2"></i>
                            <span class="fw-bold">Import Produk</span>
                        </div>
                        <a href="{{ route('import.template', 'produk') }}"
                           class="btn btn-sm btn-outline-success">
                            <i class="bi bi-file-earmark-excel me-1"></i>Download Template (.xlsx)
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        {{-- Panduan langkah --}}
                        <div class="col-md-4">
                            <p class="fw-semibold small mb-3">Langkah-langkah:</p>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex gap-2">
                                    <span class="step-badge">1</span>
                                    <div class="small">Download template Excel, isi data sesuai kolom</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <span class="step-badge">2</span>
                                    <div class="small">Pastikan nama kategori sama persis dengan sistem</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <span class="step-badge">3</span>
                                    <div class="small">Pilih mode import lalu upload file .xlsx</div>
                                </div>
                            </div>
                        </div>

                        {{-- Form --}}
                        <div class="col-md-8">
                            <form action="{{ route('import.produk') }}" method="POST"
                                  enctype="multipart/form-data" id="form-produk">
                                @csrf
                                {{-- Mode --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Mode Import</label>
                                    <div class="d-flex flex-column gap-2">
                                        <label class="mode-card">
                                            <div class="d-flex gap-2 align-items-start">
                                                <input type="radio" name="mode" value="tambah" checked class="mt-1">
                                                <div>
                                                    <div class="mode-title">Tambah Baru</div>
                                                    <div class="mode-desc">Semua baris dianggap produk baru</div>
                                                </div>
                                            </div>
                                        </label>
                                        <label class="mode-card">
                                            <div class="d-flex gap-2 align-items-start">
                                                <input type="radio" name="mode" value="update" class="mt-1">
                                                <div>
                                                    <div class="mode-title">Update atau Tambah</div>
                                                    <div class="mode-desc">Update jika nama sama, tambah jika baru</div>
                                                </div>
                                            </div>
                                        </label>
                                        <label class="mode-card">
                                            <div class="d-flex gap-2 align-items-start">
                                                <input type="radio" name="mode" value="replace" class="mt-1">
                                                <div>
                                                    <div class="mode-title text-danger">Ganti Semua</div>
                                                    <div class="mode-desc">Hapus semua produk lama, ganti dengan data baru</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Drop zone --}}
                                <div class="drop-zone" id="dz-produk"
                                     onclick="document.getElementById('file-produk').click()"
                                     ondragover="dzDragOver(event,'dz-produk')"
                                     ondragleave="dzDragLeave('dz-produk')"
                                     ondrop="dzDrop(event,'file-produk','dz-produk','dz-name-produk')">
                                    <div class="dz-icon"><i class="bi bi-file-earmark-excel"></i></div>
                                    <div class="fw-semibold mb-1" id="dz-name-produk">Klik atau seret file di sini</div>
                                    <div class="text-muted mt-1" style="font-size:.75rem">Format: .xlsx · Maks 5MB</div>
                                </div>
                                <input type="file" id="file-produk" name="file"
                                       accept=".xlsx,.xls" class="d-none"
                                       onchange="dzFileSelected(this,'dz-produk','dz-name-produk')">

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-upload me-2"></i>Upload & Import
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Import Pasien --}}
            <div class="card border">
                <div class="card-header p-3 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bi bi-people text-info me-2"></i>
                            <span class="fw-bold">Import Pasien</span>
                        </div>
                        <a href="{{ route('import.template', 'pasien') }}"
                           class="btn btn-sm btn-outline-success">
                            <i class="bi bi-file-earmark-excel me-1"></i>Download Template (.xlsx)
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <p class="fw-semibold small mb-3">Panduan kolom:</p>
                            <ul class="small text-muted ps-3">
                                <li><strong>nama</strong> — wajib</li>
                                <li><strong>tanggal_lahir</strong> — format YYYY-MM-DD</li>
                                <li><strong>jenis_kelamin</strong> — L atau P</li>
                                <li><strong>no_hp, no_bpjs, email, alamat</strong> — opsional</li>
                            </ul>
                        </div>
                        <div class="col-md-8">
                            <form action="{{ route('import.pasien') }}" method="POST"
                                  enctype="multipart/form-data" id="form-pasien">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Mode Import</label>
                                    <div class="d-flex flex-column gap-2">
                                        <label class="mode-card">
                                            <div class="d-flex gap-2 align-items-start">
                                                <input type="radio" name="mode" value="tambah" checked class="mt-1">
                                                <div>
                                                    <div class="mode-title">Tambah Baru</div>
                                                    <div class="mode-desc">Semua baris dianggap pasien baru</div>
                                                </div>
                                            </div>
                                        </label>
                                        <label class="mode-card">
                                            <div class="d-flex gap-2 align-items-start">
                                                <input type="radio" name="mode" value="update" class="mt-1">
                                                <div>
                                                    <div class="mode-title">Update atau Tambah</div>
                                                    <div class="mode-desc">Update jika nama sama, tambah jika baru</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="drop-zone" id="dz-pasien"
                                     onclick="document.getElementById('file-pasien').click()"
                                     ondragover="dzDragOver(event,'dz-pasien')"
                                     ondragleave="dzDragLeave('dz-pasien')"
                                     ondrop="dzDrop(event,'file-pasien','dz-pasien','dz-name-pasien')">
                                    <div class="dz-icon"><i class="bi bi-file-earmark-excel"></i></div>
                                    <div class="fw-semibold mb-1" id="dz-name-pasien">Klik atau seret file di sini</div>
                                    <div class="text-muted mt-1" style="font-size:.75rem">Format: .xlsx · Maks 5MB</div>
                                </div>
                                <input type="file" id="file-pasien" name="file"
                                       accept=".xlsx,.xls" class="d-none"
                                       onchange="dzFileSelected(this,'dz-pasien','dz-name-pasien')">

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-upload me-2"></i>Upload & Import
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- /tab-import --}}

        {{-- ══════════════ TAB: EXPORT ══════════════ --}}
        <div class="import-tab-panel" id="tab-export">
            <div class="row g-4">

                {{-- Export Produk --}}
                <div class="col-md-6">
                    <div class="export-card h-100">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:48px;height:48px;background:#eef1fb">
                                <i class="bi bi-box-seam text-primary fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Export Produk</div>
                                <div class="text-muted small">File Excel (.xlsx)</div>
                            </div>
                        </div>

                        <form action="{{ route('export.produk') }}" method="GET">
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Filter Stok</label>
                                <select name="stok" class="form-select form-select-sm">
                                    <option value="">Semua stok</option>
                                    <option value="menipis">Stok menipis</option>
                                    <option value="habis">Stok habis</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Cari Nama</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                       placeholder="Kosongkan untuk semua produk">
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-file-earmark-excel me-2"></i>Download Excel Produk
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Export Pasien --}}
                <div class="col-md-6">
                    <div class="export-card h-100">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:48px;height:48px;background:#e0f2fe">
                                <i class="bi bi-people text-info fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Export Pasien</div>
                                <div class="text-muted small">File Excel (.xlsx)</div>
                            </div>
                        </div>

                        <form action="{{ route('export.pasien') }}" method="GET">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Cari Nama Pasien</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                       placeholder="Kosongkan untuk semua pasien">
                            </div>
                            <button type="submit" class="btn btn-success w-100 mt-4">
                                <i class="bi bi-file-earmark-excel me-2"></i>Download Excel Pasien
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>{{-- /tab-export --}}

    </div>{{-- /card-body --}}
</div>{{-- /card --}}
@endsection

@push('scripts')
<script>
// ── Tab switching ──
function switchTab(id, btn) {
    document.querySelectorAll('.import-tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.import-tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + id).classList.add('active');
    btn.classList.add('active');
}

// ── Drop zone helpers ──
function dzDragOver(e, dzId) {
    e.preventDefault();
    document.getElementById(dzId).classList.add('drag-over');
}
function dzDragLeave(dzId) {
    document.getElementById(dzId).classList.remove('drag-over');
}
function dzDrop(e, inputId, dzId, nameId) {
    e.preventDefault();
    dzDragLeave(dzId);
    const file = e.dataTransfer.files[0];
    if (!file) return;
    const input = document.getElementById(inputId);
    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;
    dzFileSelected(input, dzId, nameId);
}
function dzFileSelected(input, dzId, nameId) {
    const dz = document.getElementById(dzId);
    const nm = document.getElementById(nameId);
    if (input.files.length) {
        const f = input.files[0];
        // Validasi ekstensi
        const ext = f.name.split('.').pop().toLowerCase();
        if (!['xlsx','xls'].includes(ext)) {
            showToast('error', 'Hanya file Excel (.xlsx atau .xls) yang diizinkan.');
            input.value = '';
            return;
        }
        nm.textContent = f.name + ' (' + (f.size / 1024).toFixed(1) + ' KB)';
        dz.classList.add('has-file');
        dz.querySelector('.dz-icon i').className = 'bi bi-file-earmark-check';
    }
}

// ── Auto switch to export tab if URL has #export ──
if (window.location.hash === '#export') {
    switchTab('export', document.querySelectorAll('.import-tab-btn')[1]);
}
</script>
@endpush
