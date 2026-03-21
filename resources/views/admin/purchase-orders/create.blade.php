@extends('layouts.admin')
@section('title', 'Buat Purchase Order')
@section('page-title', 'Buat Purchase Order')

@push('styles')
<style>
    /* Fix gap di bawah page title */
    .page-header,
    .page-title-box {
        padding-bottom: 0 !important;
        margin-bottom: 0.75rem !important;
    }
    .content-wrapper,
    .main-content {
        padding-top: 1rem !important;
    }

    /* Item row styling */
    .item-row {
        background: #f8f9fc;
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
    }

    /* Subtotal text tidak terpotong */
    .subtotal-text {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('purchase-orders.store') }}" id="po-form">
    @csrf
    <div class="row g-3">

        {{-- ===== Kolom Kiri: Info PO ===== --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header p-3">
                    <i class="bi bi-info-circle text-primary me-2"></i>Info PO
                </div>
                <div class="card-body p-4">

                    {{-- Supplier --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Supplier <span class="text-danger">*</span>
                        </label>
                        <select name="supplier_id"
                                class="form-select @error('supplier_id') is-invalid @enderror"
                                required>
                            <option value="">— Pilih Supplier —</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}"
                                    {{ old('supplier_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal PO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Tanggal PO <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="tanggal_po"
                               class="form-control"
                               value="{{ old('tanggal_po', now()->format('Y-m-d')) }}"
                               required>
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="catatan"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Opsional...">{{ old('catatan') }}</textarea>
                    </div>

                    {{-- Total Ringkasan --}}
                    <div class="p-3 bg-primary bg-opacity-10 rounded-3">
                        <div class="text-muted small">Total Nilai PO</div>
                        <div class="fw-bold fs-5 text-primary" id="grand-total">Rp 0</div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ===== Kolom Kanan: Item PO ===== --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header p-3 d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-box-seam text-primary me-2"></i>Item Produk
                    </span>
                    <button type="button"
                            class="btn btn-sm btn-outline-primary"
                            onclick="addRow()">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Produk
                    </button>
                </div>

                <div class="card-body p-3">
                    <div id="items-container"></div>
                    <div id="empty-hint" class="text-center text-muted py-4">
                        <i class="bi bi-box-seam fs-2 d-block mb-2 opacity-50"></i>
                        Klik <strong>+ Tambah Produk</strong> untuk mulai
                    </div>
                </div>

                <div class="card-footer p-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan PO
                    </button>
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-light">
                        Batal
                    </a>
                </div>
            </div>
        </div>

    </div>
</form>
@endsection

{{-- ===== Scripts (di luar @section) ===== --}}
@push('scripts')
<script>
// FIX: Gunakan json_encode +  agar tidak konflik dengan sintaks Blade
const products = {!! json_encode($products->map(fn($p) => [
    'id'         => $p->id,
    'kode'       => $p->kode_produk,
    'nama'       => $p->nama,
    'harga_beli' => $p->harga_beli,
])->values()) !!};

let rowIdx = 0;

function addRow() {
    document.getElementById('empty-hint').style.display = 'none';
    const idx = rowIdx++;

    const opts = products.map(p =>
        `<option value="${p.id}" data-harga="${p.harga_beli}">
            ${p.kode} — ${p.nama}
        </option>`
    ).join('');

    const html = `
    <div class="item-row" id="row-${idx}">
        <div class="row g-2 align-items-end">

            {{-- Produk --}}
            <div class="col-md-5">
                <label class="form-label small fw-semibold mb-1">Produk</label>
                <select name="items[${idx}][product_id]"
                        class="form-select form-select-sm"
                        onchange="fillHarga(this, ${idx})"
                        required>
                    <option value="">— Pilih —</option>
                    ${opts}
                </select>
            </div>

            {{-- Harga Beli --}}
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Harga Beli (Rp)</label>
                <input type="number"
                       name="items[${idx}][harga_beli]"
                       id="harga-${idx}"
                       class="form-control form-control-sm"
                       min="0"
                       oninput="recalc()"
                       required>
            </div>

            {{-- Jumlah --}}
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Jumlah</label>
                <input type="number"
                       name="items[${idx}][qty]"
                       id="qty-${idx}"
                       class="form-control form-control-sm"
                       value="1"
                       min="1"
                       oninput="recalc()"
                       required>
            </div>

            {{-- Subtotal + Hapus (digabung agar proporsi pas) --}}
            <div class="col-md-2 d-flex align-items-end justify-content-between pb-1">
                <div>
                    <label class="form-label small fw-semibold mb-1">Subtotal</label>
                    <div class="subtotal-text" id="sub-${idx}">Rp 0</div>
                </div>
                <button type="button"
                        class="btn btn-sm btn-light text-danger ms-2"
                        onclick="removeRow(${idx})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

        </div>
    </div>`;

    document.getElementById('items-container').insertAdjacentHTML('beforeend', html);
    recalc();
}

function fillHarga(sel, idx) {
    const opt   = sel.options[sel.selectedIndex];
    const harga = opt?.dataset?.harga || 0;
    document.getElementById(`harga-${idx}`).value = harga;
    recalc();
}

function removeRow(idx) {
    document.getElementById(`row-${idx}`)?.remove();
    if (!document.querySelectorAll('.item-row').length) {
        document.getElementById('empty-hint').style.display = '';
    }
    recalc();
}

function fmt(n) {
    return 'Rp ' + Math.round(n).toLocaleString('id-ID');
}

function recalc() {
    let grand = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const id  = row.id.replace('row-', '');
        const h   = parseFloat(document.getElementById(`harga-${id}`)?.value || 0);
        const q   = parseInt(document.getElementById(`qty-${id}`)?.value   || 0);
        const sub = h * q;
        grand    += sub;
        const subEl = document.getElementById(`sub-${id}`);
        if (subEl) subEl.textContent = fmt(sub);
    });
    document.getElementById('grand-total').textContent = fmt(grand);
}

// Tambah 1 baris kosong saat halaman load
addRow();
</script>
@endpush