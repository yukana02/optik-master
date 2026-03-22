@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk Baru')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-xl-9">
<form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
@csrf
<div class="card mb-3">
    <div class="card-header p-3"><i class="bi bi-box-seam text-primary me-2"></i>Informasi Produk</div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-12 col-md-8">
                <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                       value="{{ old('nama') }}" required placeholder="Contoh: Frame Ray-Ban RB2140">
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Merek</label>
                <input type="text" name="merek" class="form-control" value="{{ old('merek') }}"
                       placeholder="Ray-Ban, Oakley, Hoya...">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nama }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Satuan</label>
                <select name="satuan" class="form-select">
                    <option value="pcs" {{ old('satuan','pcs')=='pcs'?'selected':'' }}>pcs</option>
                    <option value="pasang" {{ old('satuan')=='pasang'?'selected':'' }}>pasang</option>
                    <option value="box" {{ old('satuan')=='box'?'selected':'' }}>box</option>
                    <option value="lusin" {{ old('satuan')=='lusin'?'selected':'' }}>lusin</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                           value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Produk Aktif</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"
                          placeholder="Deskripsi produk, spesifikasi, keterangan...">{{ old('deskripsi') }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header p-3"><i class="bi bi-currency-dollar text-success me-2"></i>Harga & Stok</div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Harga Beli <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="harga_beli" class="form-control @error('harga_beli') is-invalid @enderror"
                           value="{{ old('harga_beli', 0) }}" min="0" required>
                </div>
                @error('harga_beli')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Harga Jual <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="harga_jual" class="form-control @error('harga_jual') is-invalid @enderror"
                           value="{{ old('harga_jual', 0) }}" min="0" required>
                </div>
                @error('harga_jual')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Stok Awal <span class="text-danger">*</span></label>
                <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror"
                       value="{{ old('stok', 0) }}" min="0" required>
                @error('stok')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Minimum Stok</label>
                <input type="number" name="stok_minimum" class="form-control"
                       value="{{ old('stok_minimum', 5) }}" min="0">
                <small class="text-muted">Peringatan jika stok ≤ nilai ini</small>
            </div>
        </div>
    </div>
</div>

{{-- ══ FOTO PRODUK ══ --}}
<div class="card mb-3">
    <div class="card-header p-3">
        <i class="bi bi-image text-info me-2"></i>Foto Produk
        <small class="text-muted fw-normal ms-1">(opsional)</small>
    </div>
    <div class="card-body p-4">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <div id="img-preview-wrap"
                     style="width:120px;height:120px;border:2px dashed #dee2e6;border-radius:12px;
                            display:flex;align-items:center;justify-content:center;
                            overflow:hidden;background:#f8f9fa;transition:.2s;">
                    <i class="bi bi-image text-muted" style="font-size:2.5rem" id="img-placeholder"></i>
                    <img id="img-preview" src="#" alt="Preview"
                         style="width:100%;height:100%;object-fit:cover;display:none;border-radius:10px;">
                </div>
            </div>
            <div class="col">
                <label class="form-label fw-semibold">Upload Gambar Produk</label>
                <input type="file" name="gambar" id="gambar"
                       class="form-control @error('gambar') is-invalid @enderror"
                       accept="image/jpeg,image/jpg,image/png,image/webp"
                       onchange="previewImage(this)">
                @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Format: JPG, PNG, atau WEBP · Ukuran maks. 2 MB.</div>
                <button type="button" id="btn-hapus-gambar"
                        class="btn btn-sm btn-outline-danger mt-2 d-none"
                        onclick="hapusGambar()">
                    <i class="bi bi-x-circle me-1"></i>Hapus Pilihan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary px-4">
        <i class="bi bi-check-lg me-1"></i>Simpan Produk
    </button>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>
</form>
</div>
</div>

@push('scripts')
<script>
function previewImage(input) {
    const preview   = document.getElementById('img-preview');
    const holder    = document.getElementById('img-placeholder');
    const btnHapus  = document.getElementById('btn-hapus-gambar');
    const wrap      = document.getElementById('img-preview-wrap');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src            = e.target.result;
            preview.style.display  = 'block';
            holder.style.display   = 'none';
            wrap.style.border      = '2px solid #0d6efd';
            btnHapus.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function hapusGambar() {
    const input   = document.getElementById('gambar');
    const preview = document.getElementById('img-preview');
    const holder  = document.getElementById('img-placeholder');
    const btnHapus = document.getElementById('btn-hapus-gambar');
    const wrap    = document.getElementById('img-preview-wrap');
    input.value            = '';
    preview.style.display  = 'none';
    holder.style.display   = '';
    wrap.style.border      = '2px dashed #dee2e6';
    btnHapus.classList.add('d-none');
}
</script>
@endpush
@endsection
