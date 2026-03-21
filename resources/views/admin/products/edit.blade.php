@extends('layouts.admin')
@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')
<div class="row justify-content-center">
<div class="col-xl-9">
<form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="card mb-3">
    <div class="card-header p-3">
        <i class="bi bi-pencil text-warning me-2"></i>Edit: {{ $product->nama }}
        <span class="badge bg-secondary ms-2">{{ $product->kode_produk }}</span>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                       value="{{ old('nama', $product->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Merek</label>
                <input type="text" name="merek" class="form-control" value="{{ old('merek', $product->merek) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select" required>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Satuan</label>
                <select name="satuan" class="form-select">
                    @foreach(['pcs','pasang','box','lusin'] as $s)
                    <option value="{{ $s }}" {{ old('satuan',$product->satuan)==$s?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                           value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Produk Aktif</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $product->deskripsi) }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header p-3"><i class="bi bi-currency-dollar text-success me-2"></i>Harga & Stok</div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Harga Beli <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="harga_beli" class="form-control"
                           value="{{ old('harga_beli', $product->harga_beli) }}" min="0" required>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Harga Jual <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="harga_jual" class="form-control"
                           value="{{ old('harga_jual', $product->harga_jual) }}" min="0" required>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Stok Saat Ini <span class="text-danger">*</span></label>
                <input type="number" name="stok" class="form-control"
                       value="{{ old('stok', $product->stok) }}" min="0" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Minimum Stok</label>
                <input type="number" name="stok_minimum" class="form-control"
                       value="{{ old('stok_minimum', $product->stok_minimum) }}" min="0">
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
        <div class="row align-items-start g-3">
            {{-- Preview / Gambar saat ini --}}
            <div class="col-auto">
                <div class="text-center">
                    <div id="img-preview-wrap"
                         style="width:120px;height:120px;border:2px solid {{ $product->gambar ? '#0d6efd' : '#dee2e6' }};
                                border-radius:12px;overflow:hidden;background:#f8f9fa;
                                display:flex;align-items:center;justify-content:center;transition:.2s;">
                        @if($product->gambar)
                            <img id="img-preview"
                                 src="{{ asset('storage/'.$product->gambar) }}"
                                 alt="{{ $product->nama }}"
                                 style="width:100%;height:100%;object-fit:cover;">
                            <i class="bi bi-image text-muted" style="font-size:2.5rem;display:none" id="img-placeholder"></i>
                        @else
                            <i class="bi bi-image text-muted" style="font-size:2.5rem" id="img-placeholder"></i>
                            <img id="img-preview" src="#" alt="Preview"
                                 style="width:100%;height:100%;object-fit:cover;display:none;">
                        @endif
                    </div>
                    <small class="text-muted d-block mt-1">
                        {{ $product->gambar ? 'Foto saat ini' : 'Belum ada foto' }}
                    </small>
                </div>
            </div>

            {{-- Upload input --}}
            <div class="col">
                <label class="form-label fw-semibold">Ganti / Upload Foto Baru</label>
                <input type="file" name="gambar" id="gambar"
                       class="form-control @error('gambar') is-invalid @enderror"
                       accept="image/jpeg,image/jpg,image/png,image/webp"
                       onchange="previewImage(this)">
                @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Format: JPG, PNG, atau WEBP · Ukuran maks. 2 MB. Biarkan kosong jika tidak ingin mengubah foto.</div>

                <div class="d-flex gap-2 mt-2">
                    <button type="button" id="btn-hapus-pilihan"
                            class="btn btn-sm btn-outline-secondary d-none"
                            onclick="batalPilihan()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Batal Pilih
                    </button>
                    @if($product->gambar)
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" name="hapus_gambar"
                               id="hapus_gambar" value="1">
                        <label class="form-check-label text-danger small" for="hapus_gambar">
                            Hapus foto ini
                        </label>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-warning px-4">
        <i class="bi bi-check-lg me-1"></i>Update Produk
    </button>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>
</form>
</div>
</div>

@push('scripts')
<script>
const originalSrc = '{{ $product->gambar ? asset("storage/".$product->gambar) : "" }}';

function previewImage(input) {
    const preview  = document.getElementById('img-preview');
    const holder   = document.getElementById('img-placeholder');
    const btnBatal = document.getElementById('btn-hapus-pilihan');
    const wrap     = document.getElementById('img-preview-wrap');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src           = e.target.result;
            preview.style.display = 'block';
            holder.style.display  = 'none';
            wrap.style.border     = '2px solid #0d6efd';
            btnBatal.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function batalPilihan() {
    const input    = document.getElementById('gambar');
    const preview  = document.getElementById('img-preview');
    const holder   = document.getElementById('img-placeholder');
    const btnBatal = document.getElementById('btn-hapus-pilihan');
    const wrap     = document.getElementById('img-preview-wrap');
    input.value = '';
    if (originalSrc) {
        preview.src           = originalSrc;
        preview.style.display = 'block';
        holder.style.display  = 'none';
        wrap.style.border     = '2px solid #0d6efd';
    } else {
        preview.style.display = 'none';
        holder.style.display  = '';
        wrap.style.border     = '2px dashed #dee2e6';
    }
    btnBatal.classList.add('d-none');
}
</script>
@endpush
@endsection
