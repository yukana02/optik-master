@extends('layouts.admin')
@section('title', 'POS / Kasir')
@section('page-title', 'Point of Sale')

@push('styles')
<style>
/* ════════════════════════════════════════
   LAYOUT
   ════════════════════════════════════════ */
.pos-wrap {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 14px;
    align-items: start;
    width: 100%;
    min-width: 0;
}
.pos-left, .pos-right { min-width: 0; }
@media (max-width: 1300px) { .pos-wrap { grid-template-columns: 1fr 310px; gap: 12px; } }
@media (max-width: 1100px) { .pos-wrap { grid-template-columns: 1fr 290px; gap: 10px; } }

/* ════════════════════════════════════════
   MOBILE
   ════════════════════════════════════════ */
@media (max-width: 991.98px) {
    .pos-wrap { display: block; }
    .mob-tab-bar {
        display: flex !important;
        position: sticky; top: 0; z-index: 200;
        background: #fff; border-bottom: 2px solid #e9ecef;
        margin: -16px -12px 16px; padding: 0 4px;
    }
    .mob-tab-btn {
        flex: 1; padding: 12px 8px; font-weight: 700; font-size: .88rem;
        text-align: center; border: none; background: none; color: #6c757d;
        border-bottom: 3px solid transparent; cursor: pointer; transition: .15s;
    }
    .mob-tab-btn.active { color: #1e2a5e; border-bottom-color: #1e2a5e; }
    .pos-left, .pos-right { display: none; }
    .pos-left.mob-active, .pos-right.mob-active { display: block; }
    #btn-float-cart {
        display: flex !important;
        position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
        z-index: 300; min-width: 200px;
        box-shadow: 0 4px 20px rgba(0,0,0,.25); border-radius: 50px;
        padding: 12px 24px; font-weight: 700; white-space: nowrap;
        align-items: center; justify-content: center; gap: 8px;
    }
    #btn-float-cart.hidden { display: none !important; }
    .pos-right { position: static; max-height: none; overflow: visible; }
    .pos-right-inner { position: static; max-height: none; overflow: visible; padding-bottom: 80px; }
    .product-grid { grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); max-height: 55vh; gap: 8px; overflow-y: auto; }
    .product-card { padding: 8px 6px; }
    .product-card-img { width: 40px; height: 40px; }
    .product-card-name  { font-size: .72rem; }
    .product-card-price { font-size: .7rem; }
    .product-card-stok  { font-size: .62rem; }
    .cart-scroll-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .pos-right .btn-bayar-wrap {
        position: sticky; bottom: 0; background: #fff;
        padding: 12px 0 4px; margin-top: 12px;
        border-top: 1px solid #e9ecef; z-index: 100;
    }
}

/* ════════════════════════════════════════
   DESKTOP
   ════════════════════════════════════════ */
@media (min-width: 992px) {
    .mob-tab-bar    { display: none; }
    #btn-float-cart { display: none !important; }
    .pos-left, .pos-right { display: block !important; }
    .pos-right { position: static; }
    .pos-right-inner {
        position: sticky;
        top: 70px;
        max-height: calc(100vh - 80px);
        overflow-y: auto;
        overflow-x: hidden;
    }
    .pos-right-inner::-webkit-scrollbar { width: 4px; }
    .pos-right-inner::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 4px; }
    .pos-right .btn-bayar-wrap { position: static; padding: 0; border: none; margin: 0; }
}

/* ════════════════════════════════════════
   SEARCH
   ════════════════════════════════════════ */
.pos-search-wrap { position: relative; }
.pos-search-result {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 1050;
    background: #fff; border: 1px solid #dee2e6; border-radius: 10px;
    max-height: 280px; overflow-y: auto;
    box-shadow: 0 8px 24px rgba(0,0,0,.14);
}
.pos-product-item {
    padding: 10px 14px; cursor: pointer;
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px solid #f0f0f0; transition: background .1s; gap: 8px;
}
.pos-product-item:last-child { border-bottom: none; }
.pos-product-item:hover { background: #f0f4ff; }

/* ════════════════════════════════════════
   KATEGORI PILLS — [1] SCROLL + ARROW BTN
   ════════════════════════════════════════ */
.cat-pills-wrap {
    position: relative;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
}
.cat-arrow-btn {
    flex-shrink: 0;
    width: 30px; height: 30px;
    border: 1.5px solid #dee2e6; border-radius: 50%;
    background: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .75rem; color: #495057;
    transition: background .15s, border-color .15s;
    user-select: none;
}
.cat-arrow-btn:hover { background: #1e2a5e; color: #fff; border-color: #1e2a5e; }
.cat-arrow-btn:disabled { opacity: .35; pointer-events: none; }
.cat-pills {
    display: flex;
    flex-wrap: nowrap;
    gap: 6px;
    overflow-x: auto;
    padding-bottom: 2px;
    scroll-behavior: smooth;
    flex: 1;
    /* sembunyikan scrollbar tapi tetap bisa scroll */
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.cat-pills::-webkit-scrollbar { display: none; }
.cat-pill {
    white-space: nowrap; padding: 5px 14px; border-radius: 20px;
    border: 1.5px solid #dee2e6; font-size: .78rem; font-weight: 500;
    cursor: pointer; background: #fff; color: #495057; transition: all .15s;
    flex-shrink: 0; user-select: none;
}
.cat-pill:hover, .cat-pill.active { background: #1e2a5e; color: #fff; border-color: #1e2a5e; }
.cat-pill * { pointer-events: none; }

/* ════════════════════════════════════════
   PRODUK GRID
   ════════════════════════════════════════ */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 8px; max-height: 440px; overflow-y: auto;
    min-width: 0; padding: 2px;
}
.product-card {
    border: 1.5px solid #e9ecef; border-radius: 10px;
    padding: 10px 8px; cursor: pointer; text-align: center;
    transition: border-color .15s, box-shadow .15s, transform .1s;
    background: #fff; position: relative; user-select: none;
}
.product-card:hover  { border-color: #1e2a5e; box-shadow: 0 2px 12px rgba(30,42,94,.12); transform: translateY(-1px); }
.product-card:active { transform: scale(.95); }
.product-card.out-of-stock { opacity: .4; cursor: not-allowed; pointer-events: none; }
.product-card.low-stock::after {
    content: 'Menipis'; position: absolute; top: 4px; right: 4px;
    background: #fef3c7; color: #92400e; font-size: .58rem;
    padding: 1px 5px; border-radius: 20px; font-weight: 700;
}
.product-card-img {
    width: 48px; height: 48px; object-fit: cover; border-radius: 8px;
    margin: 0 auto 6px; background: #f4f6fb;
    display: flex; align-items: center; justify-content: center;
    pointer-events: none;
}
.product-card-img img { pointer-events: none; }
.product-card-name  { font-size: .77rem; font-weight: 600; line-height: 1.3; margin-bottom: 3px; color: #1e2a5e; pointer-events: none; }
.product-card-price { font-size: .74rem; color: #059669; font-weight: 700; pointer-events: none; }
.product-card-stok  { font-size: .67rem; color: #6c757d; pointer-events: none; }
.product-card.added { border-color: #059669 !important; background: #f0fdf4 !important; }

/* ════════════════════════════════════════
   CART
   ════════════════════════════════════════ */
.cart-table { table-layout: fixed; width: 100%; }
.cart-table td { vertical-align: middle; padding: 5px 6px; overflow: hidden; }
.cart-table .qty-ctrl { display: flex; align-items: center; gap: 2px; }
.cart-table .qty-ctrl input {
    width: 32px; text-align: center; font-weight: 600; padding: 2px 2px;
    border: 1px solid #dee2e6; border-radius: 6px; font-size: .78rem;
}

#total-display     { font-size: 1.55rem; font-weight: 700; color: #1e2a5e; }
#kembalian-display { font-size: 1.05rem; font-weight: 600; }

.metode-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 5px; }
@media (max-width: 420px) { .metode-grid { grid-template-columns: repeat(3, 1fr); } }
.metode-btn {
    border: 1.5px solid #dee2e6; border-radius: 8px; padding: 7px 4px;
    text-align: center; cursor: pointer; font-size: .76rem; font-weight: 600;
    transition: all .15s; color: #495057; background: #fff;
    -webkit-tap-highlight-color: transparent;
}
.metode-btn.active { background: #1e2a5e; color: #fff; border-color: #1e2a5e; }
.metode-btn * { pointer-events: none; }

#bpjs-section { display: none; }
#bpjs-section.show { display: block; }

/* [2] FORMAT HARGA — styling currency inputs */
.rp-input-wrap { position: relative; }
.rp-input-wrap .rp-display {
    width: 100%;
    padding: .375rem .75rem .375rem 2.6rem;
    font-size: 1rem; font-weight: 700;
    border: 1px solid #ced4da; border-radius: .375rem;
    background: #fff; cursor: text;
    min-height: 46px; display: flex; align-items: center;
}
.rp-input-wrap .rp-prefix {
    position: absolute; left: .75rem; top: 50%; transform: translateY(-50%);
    color: #6c757d; font-size: .9rem; pointer-events: none; z-index: 2;
}
.rp-input-wrap input[type="hidden"] { display: none; }

/* [3] DISABLE TOMBOL BAYAR */
#btn-bayar { transition: opacity .2s, background .2s; }
#btn-bayar:disabled {
    opacity: .55; cursor: not-allowed;
    background: #6c757d; border-color: #6c757d;
}

.numpad-btn { font-size: .88rem; font-weight: 600; min-height: 36px; }

/* [4] Alert field BPJS invalid */
.field-required-hint {
    font-size: .72rem; color: #dc3545; margin-top: 3px;
    display: none;
}
.field-required-hint.show { display: block; }
.input-danger { border-color: #dc3545 !important; }
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('transactions.store') }}" id="pos-form">
@csrf

{{-- Mobile tab bar --}}
<div class="mob-tab-bar">
    <button type="button" class="mob-tab-btn active" data-tab="produk">
        <i class="bi bi-grid-3x3-gap me-1"></i>Produk
    </button>
    <button type="button" class="mob-tab-btn" data-tab="kasir">
        <i class="bi bi-receipt me-1"></i>Kasir
        <span class="badge bg-primary ms-1" id="cart-count-mob">0</span>
    </button>
</div>

<div class="pos-wrap">

    {{-- ════ KIRI — Grid Produk ════ --}}
    <div class="pos-left mob-active" id="panel-produk">
        <div class="card">
            <div class="card-body p-3">

                {{-- Search --}}
                <div class="pos-search-wrap mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="search-product" class="form-control"
                               placeholder="Cari produk, kode, merek..." autocomplete="off">
                        <button type="button" class="btn btn-outline-secondary" id="btn-clear-search" style="display:none">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="pos-search-result d-none" id="search-result"></div>
                </div>

                {{-- [1] Kategori dengan tombol panah kiri/kanan --}}
                <div class="cat-pills-wrap" id="cat-pills-wrap">
                    <button type="button" class="cat-arrow-btn" id="cat-prev" title="Scroll kiri">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <div class="cat-pills" id="cat-pills">
                        <div class="cat-pill active" data-cat="all">
                            <i class="bi bi-grid me-1"></i>Semua
                        </div>
                        @foreach($categories as $cat)
                        <div class="cat-pill" data-cat="{{ $cat->id }}">{{ $cat->nama }}</div>
                        @endforeach
                    </div>
                    <button type="button" class="cat-arrow-btn" id="cat-next" title="Scroll kanan">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                {{-- Grid produk --}}
                <div class="product-grid" id="product-grid">
                    @forelse($products as $p)
                    <div class="product-card {{ $p->stok == 0 ? 'out-of-stock' : ($p->stok_menipis ? 'low-stock' : '') }}"
                         data-cat="{{ $p->category_id }}"
                         data-id="{{ $p->id }}"
                         data-name="{{ e($p->nama) }}"
                         data-kode="{{ $p->kode_produk }}"
                         data-merek="{{ e($p->merek ?? '') }}"
                         data-harga="{{ $p->harga_jual }}"
                         data-stok="{{ $p->stok }}">
                        <div class="product-card-img">
                            @if($p->gambar)
                            <img src="{{ asset('storage/'.$p->gambar) }}" alt="{{ $p->nama }}"
                                 style="width:100%;height:100%;object-fit:cover;border-radius:8px">
                            @else
                            <i class="bi bi-box-seam text-muted" style="font-size:1.3rem"></i>
                            @endif
                        </div>
                        <div class="product-card-name">{{ Str::limit($p->nama, 26) }}</div>
                        <div class="product-card-price">Rp {{ number_format($p->harga_jual,0,',','.') }}</div>
                        <div class="product-card-stok">Stok: {{ $p->stok }}</div>
                    </div>
                    @empty
                    <div style="grid-column:1/-1" class="text-center text-muted py-5">
                        <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-25"></i>
                        Belum ada produk aktif
                    </div>
                    @endforelse
                </div>

                <div id="empty-grid" class="text-center text-muted py-4 d-none">
                    <i class="bi bi-search fs-2 d-block mb-2 opacity-25"></i>
                    Tidak ada produk di kategori ini
                </div>
            </div>
        </div>
    </div>{{-- /pos-left --}}

    {{-- ════ KANAN — Keranjang & Bayar ════ --}}
    <div class="pos-right" id="panel-kasir">
    <div class="pos-right-inner">

        {{-- Keranjang --}}
        <div class="card mb-3">
            <div class="card-header p-3 d-flex justify-content-between align-items-center">
                <div class="fw-bold">
                    <i class="bi bi-cart3 text-primary me-2"></i>Keranjang
                    <span class="badge bg-primary ms-1" id="cart-count">0</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" id="btn-clear-cart">
                    <i class="bi bi-trash me-1"></i>Kosongkan
                </button>
            </div>
            <div class="cart-scroll-wrap" style="max-height:240px;overflow-y:auto;overflow-x:hidden;">
                <table class="table cart-table mb-0" style="font-size:.8rem;">
                    <thead class="table-light" style="position:sticky;top:0;z-index:1;">
                        <tr>
                            <th class="ps-2">Produk</th>
                            <th style="width:80px">Harga</th>
                            <th style="width:84px">Qty</th>
                            <th class="text-end pe-1" style="width:70px">Sub</th>
                            <th style="width:22px"></th>
                        </tr>
                    </thead>
                    <tbody id="cart-body">
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-cart-x fs-2 d-block mb-1 opacity-25"></i>
                                Klik produk untuk ditambahkan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- [2] Diskon pakai format rupiah --}}
            <div class="p-2 border-top bg-light">
                <div class="row g-2 align-items-center flex-nowrap">
                    <div class="col-auto">
                        <label class="form-label mb-0 fw-semibold small">Diskon:</label>
                    </div>
                    <div class="col-auto">
                        <div class="input-group input-group-sm">
                            <input type="number" id="diskon-persen" name="diskon_persen"
                                   class="form-control" style="width:56px" min="0" max="100" value="0">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-auto text-muted" style="font-size:.72rem">atau</div>
                    <div class="col-auto">
                        {{-- Currency formatted diskon nominal --}}
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="diskon-nominal-display" class="form-control"
                                   style="width:100px" placeholder="0" autocomplete="off" inputmode="numeric">
                        </div>
                        <input type="hidden" name="diskon_nominal" id="diskon-nominal" value="0">
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Pasien --}}
        <div class="card mb-3">
            <div class="card-header p-3">
                <i class="bi bi-person text-primary me-2"></i>Informasi Pasien
            </div>
            <div class="card-body p-3">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Tipe Pasien</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe_pasien" id="tp_umum" value="umum" checked>
                            <label class="form-check-label" for="tp_umum">
                                <i class="bi bi-person me-1"></i>Umum
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipe_pasien" id="tp_bpjs" value="bpjs">
                            <label class="form-check-label" for="tp_bpjs">
                                <i class="bi bi-shield-check me-1 text-success"></i>BPJS
                            </label>
                        </div>
                    </div>
                </div>

                {{-- [4] BPJS Section — semua field wajib saat BPJS dipilih --}}
                <div id="bpjs-section" class="mb-3 p-3 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-success">
                            No. Kartu BPJS <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="no_bpjs" id="no_bpjs"
                               class="form-control form-control-sm"
                               placeholder="0001-xxxx-xxxx-xxxx" maxlength="30">
                        <div class="field-required-hint" id="hint-bpjs-no">
                            <i class="bi bi-exclamation-circle me-1"></i>No. BPJS wajib diisi
                        </div>
                    </div>
                    <div>
                        <label class="form-label small fw-semibold text-success">
                            Subsidi BPJS (Rp) <span class="text-danger">*</span>
                        </label>
                        {{-- [2] Currency formatted subsidi --}}
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="subsidi-bpjs-display" class="form-control"
                                   placeholder="0" inputmode="numeric" autocomplete="off">
                        </div>
                        <input type="hidden" name="subsidi_bpjs" id="subsidi-bpjs" value="0">
                        <div class="field-required-hint" id="hint-bpjs-subsidi">
                            <i class="bi bi-exclamation-circle me-1"></i>Subsidi BPJS wajib diisi (> 0)
                        </div>
                        <div class="form-text">Nominal ditanggung BPJS dikurangi dari total.</div>
                    </div>
                </div>

                {{-- [4] Pasien wajib diisi saat BPJS --}}
                <div class="mb-2">
                    <label class="form-label small fw-semibold">
                        Pasien
                        <span class="text-muted fw-normal" id="lbl-pasien-opt">(Opsional)</span>
                        <span class="text-danger d-none" id="lbl-pasien-req">*</span>
                    </label>
                    <select name="patient_id" id="patient-select" class="form-select form-select-sm">
                        <option value="">-- Tanpa Pasien --</option>
                        @foreach($patients as $pasien)
                        <option value="{{ $pasien->id }}">{{ $pasien->no_rm }} — {{ $pasien->nama }}</option>
                        @endforeach
                    </select>
                    <div class="field-required-hint" id="hint-pasien">
                        <i class="bi bi-exclamation-circle me-1"></i>Pasien wajib dipilih untuk BPJS
                    </div>
                </div>

                {{-- [4] Rekam medis wajib diisi saat BPJS --}}
                <div>
                    <label class="form-label small fw-semibold">
                        Rekam Medis
                        <span class="text-muted fw-normal" id="lbl-rm-opt">(Opsional)</span>
                        <span class="text-danger d-none" id="lbl-rm-req">*</span>
                    </label>
                    <select name="medical_record_id" id="med-rec-select" class="form-select form-select-sm">
                        <option value="">-- Pilih Rekam Medis --</option>
                    </select>
                    <div class="field-required-hint" id="hint-rm">
                        <i class="bi bi-exclamation-circle me-1"></i>Rekam medis wajib dipilih untuk BPJS
                    </div>
                </div>
            </div>
        </div>

        {{-- Total --}}
        <div class="card mb-3">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span id="subtotal-text">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Diskon</span>
                    <span id="diskon-text" class="text-danger">- Rp 0</span>
                </div>
                <div class="d-flex justify-content-between mb-2" id="bpjs-row" style="display:none!important">
                    <span class="text-muted">
                        <i class="bi bi-shield-check text-success me-1"></i>Subsidi BPJS
                    </span>
                    <span id="bpjs-subsidi-text" class="text-success">- Rp 0</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-6">Total Bayar</span>
                    <div id="total-display">Rp 0</div>
                </div>
            </div>
        </div>

        {{-- Pembayaran --}}
        <div class="card mb-3">
            <div class="card-header p-3">
                <i class="bi bi-credit-card text-primary me-2"></i>Pembayaran
            </div>
            <div class="card-body p-3">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Metode Bayar</label>
                    <div class="metode-grid" id="metode-grid">
                        @foreach(['tunai'=>['Tunai','cash'],'transfer'=>['Transfer','bank'],'qris'=>['QRIS','qr-code'],'debit'=>['Debit','credit-card'],'kredit'=>['Kredit','credit-card-2-front']] as $val => [$label, $icon])
                        <div class="metode-btn {{ $val=='tunai' ? 'active' : '' }}" data-metode="{{ $val }}">
                            <i class="bi bi-{{ $icon }} d-block mb-1"></i>{{ $label }}
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="metode_bayar" id="metode-bayar-input" value="tunai">
                </div>

                {{-- [2] Jumlah Bayar — auto format rupiah --}}
                <div class="mb-2" id="bayar-section">
                    <label class="form-label small fw-semibold">Jumlah Bayar</label>
                    <div class="input-group">
                        <span class="input-group-text fw-semibold">Rp</span>
                        <input type="text" id="bayar-display"
                               class="form-control form-control-lg fw-bold"
                               placeholder="0" inputmode="numeric" autocomplete="off">
                    </div>
                    <input type="hidden" name="bayar" id="bayar-input" value="0" required>
                </div>

                {{-- Nominals numpad --}}
                <div class="d-flex flex-wrap gap-1 mb-3" id="numpad-section">
                    @foreach([20000,50000,100000,200000,500000] as $nom)
                    <button type="button" class="btn btn-outline-secondary btn-sm numpad-btn"
                            data-nominal="{{ $nom }}">
                        {{ number_format($nom/1000) }}rb
                    </button>
                    @endforeach
                    <button type="button" class="btn btn-outline-primary btn-sm numpad-btn"
                            id="btn-bayar-pas">Pas</button>
                </div>

                <div class="rounded p-2 mb-3 d-flex justify-content-between align-items-center"
                     style="background:#f0fdf4">
                    <span class="fw-semibold small">Kembalian</span>
                    <div id="kembalian-display" class="text-success">Rp 0</div>
                </div>
                <textarea name="catatan" class="form-control form-control-sm"
                          rows="2" placeholder="Catatan transaksi..."></textarea>
            </div>
        </div>

        <div class="btn-bayar-wrap">
            {{-- [3] Tombol disabled secara default, aktif saat bayar >= total dan ada item --}}
            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold" id="btn-bayar" disabled>
                <i class="bi bi-check-circle me-2"></i>Proses Pembayaran
            </button>
            <div id="bayar-hint" class="text-center text-muted small mt-1" style="font-size:.72rem"></div>
        </div>

    </div>{{-- /pos-right-inner --}}
    </div>{{-- /pos-right --}}

</div>{{-- /pos-wrap --}}
</form>

<button type="button" id="btn-float-cart" class="btn btn-primary hidden">
    <i class="bi bi-cart3"></i>
    <span id="float-cart-label">Keranjang</span>
    <span class="badge bg-warning text-dark ms-1" id="float-cart-count">0</span>
</button>

<div id="hidden-items"></div>
@endsection

@push('scripts')
<script>
(function () {
'use strict';

const POS_SEARCH_URL  = "{{ route('transactions.product.search') }}";
const POS_MED_REC_URL = "{{ route('transactions.medical-records') }}";

let cart = {};

/* ════════════════════════════════════
   [1] KATEGORI SCROLL — TOMBOL PANAH
   ════════════════════════════════════ */
const catPills = document.getElementById('cat-pills');
const catPrev  = document.getElementById('cat-prev');
const catNext  = document.getElementById('cat-next');
const SCROLL_STEP = 180;

function updateCatArrows() {
    catPrev.disabled = catPills.scrollLeft <= 2;
    catNext.disabled = catPills.scrollLeft + catPills.clientWidth >= catPills.scrollWidth - 2;
}

catPrev.addEventListener('click', function () {
    catPills.scrollLeft -= SCROLL_STEP;
    setTimeout(updateCatArrows, 320);
});
catNext.addEventListener('click', function () {
    catPills.scrollLeft += SCROLL_STEP;
    setTimeout(updateCatArrows, 320);
});
catPills.addEventListener('scroll', updateCatArrows);

// Juga scroll ke pill aktif saat dipilih
function scrollToActivePill(pill) {
    const pillLeft   = pill.offsetLeft;
    const pillRight  = pillLeft + pill.offsetWidth;
    const wrapLeft   = catPills.scrollLeft;
    const wrapRight  = wrapLeft + catPills.clientWidth;
    if (pillLeft < wrapLeft)       catPills.scrollLeft = pillLeft - 8;
    else if (pillRight > wrapRight) catPills.scrollLeft = pillRight - catPills.clientWidth + 8;
}

// Init arrow state after DOM settles
setTimeout(updateCatArrows, 50);

/* ════════════════════════════════════
   [2] CURRENCY FORMAT HELPERS
   ════════════════════════════════════ */
function fmtRaw(n) {
    return new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
}

function parseCurrency(str) {
    if (!str) return 0;
    return parseInt(String(str).replace(/\./g, '').replace(/[^\d]/g, '')) || 0;
}

function bindCurrencyInput(displayId, hiddenId, onChangeCb) {
    const display = document.getElementById(displayId);
    const hidden  = document.getElementById(hiddenId);

    display.addEventListener('input', function () {
        const raw = parseCurrency(this.value);
        // Format tampilan sambil menjaga posisi cursor
        const formatted = raw > 0 ? fmtRaw(raw) : '';
        this.value = formatted;
        hidden.value = raw;
        if (onChangeCb) onChangeCb();
    });

    display.addEventListener('keydown', function (e) {
        // Allow: backspace, delete, tab, escape, enter, arrows
        if ([8, 9, 27, 13, 46, 37, 38, 39, 40].includes(e.keyCode)) return;
        // Block non-numeric (allow numpad 0-9)
        if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    // Expose setter
    display._setValue = function (n) {
        display.value = n > 0 ? fmtRaw(n) : '';
        hidden.value  = n;
    };
    display._getValue = function () { return parseCurrency(hidden.value); };
}

// Init currency fields
bindCurrencyInput('diskon-nominal-display', 'diskon-nominal', updateTotal);
bindCurrencyInput('subsidi-bpjs-display',   'subsidi-bpjs',   updateTotal);

// Jumlah bayar — khusus karena harus trigger validateBayar juga
(function () {
    const display = document.getElementById('bayar-display');
    const hidden  = document.getElementById('bayar-input');

    display.addEventListener('input', function () {
        const raw = parseCurrency(this.value);
        this.value = raw > 0 ? fmtRaw(raw) : '';
        hidden.value = raw;
        updateTotal();
    });

    display.addEventListener('keydown', function (e) {
        if ([8, 9, 27, 13, 46, 37, 38, 39, 40].includes(e.keyCode)) return;
        if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) e.preventDefault();
    });

    display._setValue = function (n) {
        display.value = n > 0 ? fmtRaw(n) : '';
        hidden.value  = n;
        updateTotal();
    };
    display._getValue = function () { return parseCurrency(hidden.value); };
})();

/* ════════════════════════════════════
   MOBILE TAB SWITCH
   ════════════════════════════════════ */
document.querySelectorAll('.mob-tab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () { mobSwitch(this.dataset.tab); });
});

function mobSwitch(tab) {
    if (window.innerWidth >= 992) return;
    document.getElementById('panel-produk').classList.toggle('mob-active', tab === 'produk');
    document.getElementById('panel-kasir').classList.toggle('mob-active',  tab === 'kasir');
    document.querySelectorAll('.mob-tab-btn').forEach(function (b) {
        b.classList.toggle('active', b.dataset.tab === tab);
    });
    updateFloatBtn();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

document.getElementById('btn-float-cart').addEventListener('click', function () { mobSwitch('kasir'); });

function updateFloatBtn() {
    const isMobile  = window.innerWidth < 992;
    const btnFloat  = document.getElementById('btn-float-cart');
    const isKasir   = document.getElementById('panel-kasir').classList.contains('mob-active');
    const itemCount = Object.values(cart).reduce(function (s, i) { return s + i.qty; }, 0);
    if (isMobile && !isKasir && itemCount > 0) {
        btnFloat.classList.remove('hidden');
        document.getElementById('float-cart-count').textContent = itemCount;
        document.getElementById('float-cart-label').textContent = 'Keranjang (' + itemCount + ')';
    } else {
        btnFloat.classList.add('hidden');
    }
}
window.addEventListener('resize', updateFloatBtn);

/* ════════════════════════════════════
   FILTER KATEGORI
   ════════════════════════════════════ */
document.getElementById('cat-pills').addEventListener('click', function (e) {
    const pill = e.target.closest('.cat-pill');
    if (!pill) return;

    document.querySelectorAll('#cat-pills .cat-pill').forEach(function (p) { p.classList.remove('active'); });
    pill.classList.add('active');
    scrollToActivePill(pill);

    const catId = pill.dataset.cat;
    let visible = 0;
    document.querySelectorAll('#product-grid .product-card').forEach(function (card) {
        const show = catId === 'all' || card.dataset.cat === catId;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('empty-grid').classList.toggle('d-none', visible > 0);
    setTimeout(updateCatArrows, 50);
});

/* ════════════════════════════════════
   PRODUK GRID KLIK
   ════════════════════════════════════ */
document.getElementById('product-grid').addEventListener('click', function (e) {
    const card = e.target.closest('.product-card');
    if (!card || card.classList.contains('out-of-stock')) return;
    const p = {
        id: parseInt(card.dataset.id), kode_produk: card.dataset.kode || '',
        nama: card.dataset.name || '', merek: card.dataset.merek || '',
        harga_jual: parseFloat(card.dataset.harga), stok: parseInt(card.dataset.stok)
    };
    if (!p.id || isNaN(p.harga_jual) || p.stok <= 0) return;
    addToCart(p);
    card.classList.add('added');
    setTimeout(function () { card.classList.remove('added'); }, 400);
});

/* ════════════════════════════════════
   SEARCH
   ════════════════════════════════════ */
let searchTimeout;
document.getElementById('search-product').addEventListener('input', function () {
    clearTimeout(searchTimeout);
    const q = this.value.trim();
    document.getElementById('btn-clear-search').style.display = q ? '' : 'none';
    if (q.length < 2) { document.getElementById('search-result').classList.add('d-none'); return; }
    searchTimeout = setTimeout(function () { fetchProducts(q); }, 280);
});

document.getElementById('btn-clear-search').addEventListener('click', function () {
    document.getElementById('search-product').value = '';
    document.getElementById('search-product').focus();
    this.style.display = 'none';
    document.getElementById('search-result').classList.add('d-none');
});

document.getElementById('search-result').addEventListener('click', function (e) {
    const item = e.target.closest('.pos-product-item');
    if (!item || !item._product) return;
    addToCart(Object.assign({}, item._product, { harga_jual: parseFloat(item._product.harga_jual) }));
    document.getElementById('search-product').value = '';
    document.getElementById('btn-clear-search').style.display = 'none';
    document.getElementById('search-result').classList.add('d-none');
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('#search-product') && !e.target.closest('#search-result')) {
        document.getElementById('search-result').classList.add('d-none');
    }
});

async function fetchProducts(q) {
    try {
        const res  = await fetch(POS_SEARCH_URL + '?q=' + encodeURIComponent(q));
        const data = await res.json();
        const el   = document.getElementById('search-result');
        el.innerHTML = '';
        if (!data.length) {
            el.innerHTML = '<div class="pos-product-item text-muted">Produk tidak ditemukan</div>';
        } else {
            data.forEach(function (p) {
                const div = document.createElement('div');
                div.className = 'pos-product-item';
                div.innerHTML =
                    '<div><div class="fw-semibold" style="font-size:.86rem">' + esc(p.nama) + '</div>' +
                    '<small class="text-muted">' + esc(p.kode_produk) + (p.merek ? ' · ' + esc(p.merek) : '') + '</small></div>' +
                    '<div class="text-end flex-shrink-0"><div class="fw-bold text-primary" style="font-size:.86rem">Rp ' + fmtRaw(p.harga_jual) + '</div>' +
                    '<small class="text-muted">Stok: ' + p.stok + '</small></div>';
                div._product = p;
                el.appendChild(div);
            });
        }
        el.classList.remove('d-none');
    } catch (err) { console.error('Search error:', err); }
}

/* ════════════════════════════════════
   CART OPERATIONS
   ════════════════════════════════════ */
function addToCart(p) {
    if (cart[p.id]) {
        if (cart[p.id].qty >= p.stok) {
            showToast('warning', 'Stok "' + p.nama + '" hanya tersisa ' + p.stok + ' pcs.', 4000);
            return;
        }
        cart[p.id].qty++;
    } else {
        cart[p.id] = Object.assign({}, p, { harga_satuan: parseFloat(p.harga_jual), qty: 1 });
    }
    renderCart();
}

document.getElementById('cart-body').addEventListener('click', function (e) {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;
    const id = parseInt(btn.dataset.id), action = btn.dataset.action;
    if (action === 'inc')    changeQty(id,  1);
    else if (action === 'dec') changeQty(id, -1);
    else if (action === 'del') removeItem(id);
});

document.getElementById('cart-body').addEventListener('change', function (e) {
    const inp = e.target.closest('[data-qty-id]');
    if (inp) setQty(parseInt(inp.dataset.qtyId), inp.value);
    const hinp = e.target.closest('[data-harga-id]');
    if (hinp) {
        const id = parseInt(hinp.dataset.hargaId);
        if (cart[id]) { cart[id].harga_satuan = parseCurrency(hinp.value) || 0; renderCart(); }
    }
});

document.getElementById('btn-clear-cart').addEventListener('click', function () { cart = {}; renderCart(); });

function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty = Math.max(0, cart[id].qty + delta);
    if (cart[id].qty === 0) delete cart[id];
    renderCart();
}

function setQty(id, val) {
    const q = parseInt(val) || 0;
    if (q <= 0) { delete cart[id]; }
    else if (cart[id] && q > cart[id].stok) {
        showToast('warning', 'Stok hanya ' + cart[id].stok + ' pcs.', 4000);
        cart[id].qty = cart[id].stok;
    } else if (cart[id]) { cart[id].qty = q; }
    renderCart();
}

function removeItem(id) { delete cart[id]; renderCart(); }

function renderCart() {
    const tbody = document.getElementById('cart-body');
    const items = Object.values(cart);
    const total = items.reduce(function (s, i) { return s + i.qty; }, 0);
    document.getElementById('cart-count').textContent     = total;
    document.getElementById('cart-count-mob').textContent = total;

    if (!items.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">' +
            '<i class="bi bi-cart-x fs-2 d-block mb-1 opacity-25"></i>Klik produk untuk ditambahkan</td></tr>';
        updateTotal(); syncHiddenInputs(); updateFloatBtn();
        return;
    }

    tbody.innerHTML = items.map(function (item) {
        return '<tr>' +
            '<td class="ps-2" style="overflow:hidden;">' +
            '  <div class="fw-semibold text-truncate" style="font-size:.78rem;max-width:100px" title="' + esc(item.nama) + '">' + esc(item.nama) + '</div>' +
            '  <small class="text-muted">' + esc(item.kode_produk) + '</small>' +
            '</td>' +
            '<td><input type="text" class="form-control form-control-sm" style="width:74px;font-size:.76rem;padding:2px 4px"' +
            '     value="' + fmtRaw(item.harga_satuan) + '" data-harga-id="' + item.id + '" inputmode="numeric"></td>' +
            '<td><div class="qty-ctrl">' +
            '  <button type="button" class="btn btn-outline-secondary btn-sm px-1 py-0" style="line-height:1.3;font-size:.8rem" data-action="dec" data-id="' + item.id + '">−</button>' +
            '  <input type="text" value="' + item.qty + '" data-qty-id="' + item.id + '"' +
            '         style="width:28px;text-align:center;border:1px solid #dee2e6;border-radius:5px;padding:1px 2px;font-weight:700;font-size:.76rem">' +
            '  <button type="button" class="btn btn-outline-secondary btn-sm px-1 py-0" style="line-height:1.3;font-size:.8rem" data-action="inc" data-id="' + item.id + '">+</button>' +
            '</div></td>' +
            '<td class="text-end fw-semibold pe-1" style="font-size:.76rem">Rp ' + fmtRaw(item.harga_satuan * item.qty) + '</td>' +
            '<td><button type="button" class="btn btn-sm text-danger p-0" data-action="del" data-id="' + item.id + '">' +
            '  <i class="bi bi-x-lg" style="font-size:.7rem;pointer-events:none"></i></button></td>' +
            '</tr>';
    }).join('');

    updateTotal(); syncHiddenInputs(); updateFloatBtn();
}

/* ════════════════════════════════════
   TOTAL & [3] VALIDASI TOMBOL BAYAR
   ════════════════════════════════════ */
function getTotal() {
    const items    = Object.values(cart);
    const subtotal = items.reduce(function (s, i) { return s + i.harga_satuan * i.qty; }, 0);
    let diskonN    = parseCurrency(document.getElementById('diskon-nominal').value);
    const diskonP  = parseFloat(document.getElementById('diskon-persen').value) || 0;
    if (diskonP > 0) diskonN = Math.round(subtotal * diskonP / 100);
    const isBpjs   = document.getElementById('tp_bpjs').checked;
    const subsidi  = isBpjs ? (parseCurrency(document.getElementById('subsidi-bpjs').value) || 0) : 0;
    return { subtotal, diskonN, subsidi, total: Math.max(0, subtotal - diskonN - subsidi) };
}

function updateTotal() {
    const { subtotal, diskonN, subsidi, total } = getTotal();
    const bayar    = parseCurrency(document.getElementById('bayar-input').value);
    const isBpjs   = document.getElementById('tp_bpjs').checked;

    document.getElementById('subtotal-text').textContent     = 'Rp ' + fmtRaw(subtotal);
    document.getElementById('diskon-text').textContent       = '- Rp ' + fmtRaw(diskonN);
    document.getElementById('bpjs-subsidi-text').textContent = '- Rp ' + fmtRaw(subsidi);
    document.getElementById('bpjs-row').style.display        = isBpjs ? '' : 'none';
    document.getElementById('total-display').textContent     = 'Rp ' + fmtRaw(total);

    const kembalian = bayar - total;
    document.getElementById('kembalian-display').textContent = 'Rp ' + fmtRaw(Math.max(0, kembalian));
    document.getElementById('kembalian-display').style.color = kembalian < 0 ? '#ef4444' : '#16a34a';

    validateBayarBtn(bayar, total, subtotal);
}

// [3] Aktifkan/nonaktifkan tombol bayar
function validateBayarBtn(bayar, total, subtotal) {
    const btn  = document.getElementById('btn-bayar');
    const hint = document.getElementById('bayar-hint');
    const hasItem   = Object.keys(cart).length > 0;
    const metode    = document.getElementById('metode-bayar-input').value;
    const isTunai   = metode === 'tunai';

    let ok = true;
    let msg = '';

    if (!hasItem) {
        ok = false; msg = 'Tambahkan produk ke keranjang';
    } else if (total > 0 && isTunai && bayar < total) {
        ok = false; msg = 'Bayar kurang Rp ' + fmtRaw(total - bayar);
    } else if (total > 0 && isTunai && bayar <= 0) {
        ok = false; msg = 'Isi jumlah uang yang dibayarkan';
    }

    btn.disabled = !ok;
    hint.textContent = msg;
}

document.getElementById('diskon-persen').addEventListener('input',   updateTotal);

/* ════════════════════════════════════
   NUMPAD
   ════════════════════════════════════ */
document.getElementById('numpad-section').addEventListener('click', function (e) {
    const btn = e.target.closest('.numpad-btn');
    if (!btn) return;
    if (btn.id === 'btn-bayar-pas') {
        setBayarPas();
    } else {
        const nom = parseInt(btn.dataset.nominal);
        if (!isNaN(nom)) document.getElementById('bayar-display')._setValue(nom);
    }
});

function setBayarPas() {
    const { total } = getTotal();
    document.getElementById('bayar-display')._setValue(total);
}

/* ════════════════════════════════════
   METODE BAYAR
   ════════════════════════════════════ */
document.getElementById('metode-grid').addEventListener('click', function (e) {
    const btn = e.target.closest('.metode-btn');
    if (!btn) return;
    const val = btn.dataset.metode;
    document.querySelectorAll('#metode-grid .metode-btn').forEach(function (b) { b.classList.remove('active'); });
    btn.classList.add('active');
    document.getElementById('metode-bayar-input').value = val;
    const nonTunai = ['transfer','qris','debit','kredit'].includes(val);
    document.getElementById('numpad-section').style.display = nonTunai ? 'none' : '';
    if (nonTunai) setBayarPas();
    updateTotal(); // re-check button state
});

/* ════════════════════════════════════
   [4] BPJS — validasi & field wajib
   ════════════════════════════════════ */
function setBpjsMode(isBpjs) {
    document.getElementById('bpjs-section').classList.toggle('show', isBpjs);

    // Label opsional vs wajib
    document.getElementById('lbl-pasien-opt').classList.toggle('d-none', isBpjs);
    document.getElementById('lbl-pasien-req').classList.toggle('d-none', !isBpjs);
    document.getElementById('lbl-rm-opt').classList.toggle('d-none', isBpjs);
    document.getElementById('lbl-rm-req').classList.toggle('d-none', !isBpjs);

    if (!isBpjs) {
        // Reset field saat kembali ke umum
        document.getElementById('no_bpjs').value = '';
        document.getElementById('subsidi-bpjs-display')._setValue(0);
        clearBpjsHints();
    }
    updateTotal();
}

function clearBpjsHints() {
    ['hint-bpjs-no','hint-bpjs-subsidi','hint-pasien','hint-rm'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('show');
    });
    document.getElementById('no_bpjs').classList.remove('input-danger');
    document.getElementById('subsidi-bpjs-display').classList.remove('input-danger');
    document.getElementById('patient-select').classList.remove('input-danger');
    document.getElementById('med-rec-select').classList.remove('input-danger');
}

function validateBpjsFields() {
    if (!document.getElementById('tp_bpjs').checked) return true;

    let ok = true;
    clearBpjsHints();

    const noBpjs   = document.getElementById('no_bpjs').value.trim();
    const subsidi  = parseCurrency(document.getElementById('subsidi-bpjs').value);
    const patientId = document.getElementById('patient-select').value;
    const rmId      = document.getElementById('med-rec-select').value;

    if (!noBpjs) {
        document.getElementById('hint-bpjs-no').classList.add('show');
        document.getElementById('no_bpjs').classList.add('input-danger');
        ok = false;
    }
    if (!subsidi || subsidi <= 0) {
        document.getElementById('hint-bpjs-subsidi').classList.add('show');
        document.getElementById('subsidi-bpjs-display').classList.add('input-danger');
        ok = false;
    }
    if (!patientId) {
        document.getElementById('hint-pasien').classList.add('show');
        document.getElementById('patient-select').classList.add('input-danger');
        ok = false;
    }
    if (!rmId) {
        document.getElementById('hint-rm').classList.add('show');
        document.getElementById('med-rec-select').classList.add('input-danger');
        ok = false;
    }

    if (!ok) {
        showToast('error', 'Lengkapi semua data wajib untuk pasien BPJS.', 5000);
        // Scroll ke section BPJS
        document.getElementById('bpjs-section').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return ok;
}

// Clear hint saat field diisi
document.getElementById('no_bpjs').addEventListener('input', function () {
    if (this.value.trim()) {
        document.getElementById('hint-bpjs-no').classList.remove('show');
        this.classList.remove('input-danger');
    }
});
document.getElementById('subsidi-bpjs-display').addEventListener('input', function () {
    setTimeout(function () {
        const v = parseCurrency(document.getElementById('subsidi-bpjs').value);
        if (v > 0) {
            document.getElementById('hint-bpjs-subsidi').classList.remove('show');
            document.getElementById('subsidi-bpjs-display').classList.remove('input-danger');
        }
    }, 50);
});
document.getElementById('patient-select').addEventListener('change', function () {
    if (this.value) {
        document.getElementById('hint-pasien').classList.remove('show');
        this.classList.remove('input-danger');
    }
});
document.getElementById('med-rec-select').addEventListener('change', function () {
    if (this.value) {
        document.getElementById('hint-rm').classList.remove('show');
        this.classList.remove('input-danger');
    }
});

document.querySelectorAll('input[name="tipe_pasien"]').forEach(function (radio) {
    radio.addEventListener('change', function () { setBpjsMode(this.value === 'bpjs'); });
});

/* ════════════════════════════════════
   REKAM MEDIS AJAX
   ════════════════════════════════════ */
document.getElementById('patient-select').addEventListener('change', async function () {
    const sel = document.getElementById('med-rec-select');
    sel.innerHTML = '<option value="">-- Pilih Rekam Medis --</option>';
    if (!this.value) return;
    try {
        const res  = await fetch(POS_MED_REC_URL + '?patient_id=' + this.value,
            { headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
        const data = await res.json();
        if (!data.length) {
            sel.innerHTML += '<option disabled>Belum ada rekam medis</option>';
        } else {
            data.forEach(function (r) {
                sel.innerHTML += '<option value="' + r.id + '">' +
                    r.tanggal_kunjungan + ' — OD: ' + r.od_sph + ' OS: ' + r.os_sph + '</option>';
            });
        }
    } catch (err) { console.error(err); }
});

/* ════════════════════════════════════
   HIDDEN INPUTS & SUBMIT
   ════════════════════════════════════ */
function syncHiddenInputs() {
    document.getElementById('hidden-items').innerHTML =
        Object.values(cart).map(function (item, i) {
            return '<input type="hidden" name="items[' + i + '][product_id]"  value="' + item.id + '">' +
                   '<input type="hidden" name="items[' + i + '][qty]"          value="' + item.qty + '">' +
                   '<input type="hidden" name="items[' + i + '][harga_satuan]" value="' + item.harga_satuan + '">' +
                   '<input type="hidden" name="items[' + i + '][diskon]"       value="0">';
        }).join('');
}

document.getElementById('pos-form').addEventListener('submit', function (e) {
    if (!Object.keys(cart).length) {
        e.preventDefault();
        showToast('warning', 'Keranjang masih kosong! Tambahkan produk terlebih dahulu.', 4000);
        if (window.innerWidth < 992) mobSwitch('produk');
        return;
    }

    // [4] Validasi field BPJS
    if (!validateBpjsFields()) {
        e.preventDefault();
        return;
    }

    syncHiddenInputs();
    this.appendChild(document.getElementById('hidden-items'));
});

/* ════════════════════════════════════
   HELPERS
   ════════════════════════════════════ */
function esc(s) {
    if (!s) return '';
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(String(s)));
    return d.innerHTML;
}

// Init: pastikan state awal tombol bayar benar
updateTotal();

})(); // ── end IIFE ──
</script>
@endpush
