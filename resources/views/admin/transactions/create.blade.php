@extends('layouts.admin')
@section('title', 'POS / Kasir')
@section('page-title', 'Point of Sale')

@push('styles')
<style>
/* ===================== STEP INDICATOR ===================== */
.step-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
    background: #fff;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
}

/* Garis penghubung antar step */
.step-indicator::before {
    content: '';
    position: absolute;
    top: 30px;
    left: 10%;
    right: 10%;
    height: 2px;
    background: #e9ecef;
    z-index: 0;
}

.step-item {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 1;
}
.step-number {
    width: 40px;
    height: 40px;
    background: #e9ecef;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 8px;
    transition: all .25s;
}
.step-label {
    font-size: 0.78rem;
    color: #6c757d;
    font-weight: 500;
}
.step-item.active .step-number {
    background: #1e2a5e;
    color: white;
    box-shadow: 0 0 0 4px rgba(30,42,94,.15);
}
.step-item.active .step-label {
    color: #1e2a5e;
    font-weight: 600;
}
.step-item.completed .step-number {
    background: #28a745;
    color: white;
}
.step-item.completed .step-label {
    color: #28a745;
}

/* ===================== FASE CONTAINER ===================== */
.step-container {
    display: none;
}
.step-container.active {
    display: block;
    animation: fadeSlideIn .2s ease;
}
@keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ===================== CART & PRODUK ===================== */
.pos-search-result {
    position: absolute; top: 100%; left: 0; right: 0; z-index: 1000;
    background: #fff; border: 1px solid #dee2e6; border-radius: 0 0 8px 8px;
    max-height: 280px; overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,.1);
}
.pos-product-item {
    padding: 10px 14px; cursor: pointer; display: flex;
    justify-content: space-between; align-items: center;
    border-bottom: 1px solid #f0f0f0; transition: background .1s;
}
.pos-product-item:hover { background: #f8f9ff; }
.cart-table td { vertical-align: middle; }
.numpad-btn { font-size: .95rem; font-weight: 600; }
#total-display { font-size: 2rem; font-weight: 700; color: #1e2a5e; }
#kembalian-display { font-size: 1.4rem; font-weight: 600; }

#patient-list {
    z-index: 9999;
    max-height: 200px;
    overflow-y: auto;
}

/* ===================== RINGKASAN PREVIEW (FASE 3) ===================== */
.summary-preview {
    background: #f8f9fa;
    border-radius: 8px;
    position: sticky;
    top: 20px;
}

/* ===================== CART READONLY ===================== */
.cart-readonly td {
    background: #fefefe;
}

/* ===================== FASE 2 — TIPE TRANSAKSI ===================== */
.tipe-card {
    border: 2px solid #dee2e6;
    border-radius: 12px;
    padding: 1.5rem;
    cursor: pointer;
    transition: all .2s;
    text-align: center;
}
.tipe-card:hover {
    border-color: #1e2a5e;
    background: #f8f9ff;
}
.tipe-card.selected {
    border-color: #1e2a5e;
    background: #f0f3ff;
}
.tipe-card .tipe-icon {
    font-size: 2rem;
    margin-bottom: .5rem;
    display: block;
}
.tipe-card .tipe-title {
    font-weight: 600;
    color: #212529;
}
.tipe-card .tipe-desc {
    font-size: .8rem;
    color: #6c757d;
    margin-top: .25rem;
}

/* Resep form */
#resep-form {
    display: none;
    animation: fadeSlideIn .2s ease;
}
#resep-form.show {
    display: block;
}

/* ===================== FASE 4 — PEMBAYARAN ===================== */
.payment-status-toggle .btn-check:checked + .btn {
    font-weight: 600;
}
#dp-section {
    display: none;
    animation: fadeSlideIn .2s ease;
}
#dp-section.show {
    display: block;
}

/* ===================== FASE 5 — KONFIRMASI ===================== */
.confirm-section {
    border-left: 3px solid #1e2a5e;
    padding-left: 1rem;
    margin-bottom: 1.25rem;
}
.confirm-section-title {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #6c757d;
    font-weight: 600;
    margin-bottom: .5rem;
}
.confirm-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .3rem 0;
    font-size: .9rem;
}
.confirm-row .label { color: #6c757d; }
.confirm-row .value { font-weight: 500; }
.confirm-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .5rem 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e2a5e;
}
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('transactions.store') }}" id="pos-form">
@csrf

{{-- ===================== STEP INDICATOR ===================== --}}
<div class="step-indicator">
    <div class="step-item active" id="step-1-indicator">
        <div class="step-number">1</div>
        <div class="step-label">Pelanggan</div>
    </div>
    <div class="step-item" id="step-2-indicator">
        <div class="step-number">2</div>
        <div class="step-label">Tipe & Resep</div>
    </div>
    <div class="step-item" id="step-3-indicator">
        <div class="step-number">3</div>
        <div class="step-label">Produk</div>
    </div>
    <div class="step-item" id="step-4-indicator">
        <div class="step-number">4</div>
        <div class="step-label">Pembayaran</div>
    </div>
    <div class="step-item" id="step-5-indicator">
        <div class="step-number">5</div>
        <div class="step-label">Konfirmasi</div>
    </div>
</div>

{{-- ============================================================
     FASE 1: PELANGGAN
     Tidak ada perubahan dari kode asli.
     ============================================================ --}}
<div id="step-1" class="step-container active">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header p-3">
                    <i class="bi bi-person text-primary me-2"></i> Identifikasi Pelanggan
                    <small class="text-muted ms-2">(Opsional - bisa dilewati)</small>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-semibold">Cari Pasien</label>
                        <input type="text" id="patient-search" class="form-control"
                            placeholder="Cari pasien (nama / no RM)...">
                        <input type="hidden" name="patient_id" id="patient-id">
                        <div id="patient-list" class="list-group position-absolute w-100"></div>
                        <small class="text-muted">Kosongkan jika bukan untuk pasien</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rekam Medis</label>
                        <select name="medical_record_id" id="med-rec-select" class="form-select">
                            <option value="">-- Pilih Rekam Medis (opsional) --</option>
                        </select>
                    </div>

                    <div id="bpjs-section" class="mb-3 d-none">
                        <label class="form-label fw-semibold">Potongan BPJS</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="potongan-bpjs" name="potongan_bpjs"
                                   class="form-control" value="0" placeholder="0">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="skipPatient()">
                            <i class="bi bi-skip-forward"></i> Lewati
                        </button>
                        <button type="button" class="btn btn-primary" onclick="goToStep(2)">
                            <i class="bi bi-arrow-right"></i> Lanjut
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     FASE 2: TIPE TRANSAKSI & RESEP
     Baru. Kasir wajib memilih jalur sebelum ke produk.
     ============================================================ --}}
<div id="step-2" class="step-container">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header p-3">
                    <i class="bi bi-tag text-primary me-2"></i> Tipe Transaksi
                    <small class="text-muted ms-2">Pilih jalur yang sesuai</small>
                </div>
                <div class="card-body p-4">

                    {{-- Pilihan tipe --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="tipe-card selected" id="tipe-regular"
                                 onclick="selectTipe('regular')">
                                <span class="tipe-icon">🛍️</span>
                                <div class="tipe-title">Jual Langsung</div>
                                <div class="tipe-desc">Frame, aksesoris, lensa jadi.<br>Transaksi selesai hari ini.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="tipe-card" id="tipe-prescription"
                                 onclick="selectTipe('prescription')">
                                <span class="tipe-icon">👓</span>
                                <div class="tipe-title">Kacamata / Butuh Proses</div>
                                <div class="tipe-desc">Butuh pengerjaan lensa.<br>Perlu data resep & estimasi selesai.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Hidden input tipe --}}
                    <input type="hidden" name="transaction_type" id="transaction-type" value="regular">

                    {{-- Form resep — muncul jika tipe = prescription --}}
                    <div id="resep-form">
                        <hr class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold mb-0">
                                <i class="bi bi-eyeglasses text-primary me-2"></i>Data Resep
                            </h6>
                            <span id="resep-source-badge" class="badge bg-success d-none">
                                <i class="bi bi-check-circle me-1"></i>Auto dari Rekam Medis
                            </span>
                        </div>

                        {{-- OD --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted" style="font-size:.8rem">
                                OD (Mata Kanan)
                            </label>
                            <div class="row g-2">
                                <div class="col">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">SPH</span>
                                        <input type="number" name="od_sph" id="od-sph"
                                               class="form-control" step="0.25" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">CYL</span>
                                        <input type="number" name="od_cyl" id="od-cyl"
                                               class="form-control" step="0.25" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">AX</span>
                                        <input type="number" name="od_axis" id="od-axis"
                                               class="form-control" step="1" min="0" max="180" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- OS --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted" style="font-size:.8rem">
                                OS (Mata Kiri)
                            </label>
                            <div class="row g-2">
                                <div class="col">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">SPH</span>
                                        <input type="number" name="os_sph" id="os-sph"
                                               class="form-control" step="0.25" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">CYL</span>
                                        <input type="number" name="os_cyl" id="os-cyl"
                                               class="form-control" step="0.25" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">AX</span>
                                        <input type="number" name="os_axis" id="os-axis"
                                               class="form-control" step="1" min="0" max="180" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PD & Estimasi --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">PD (mm)</label>
                                <input type="number" name="pd" id="pd"
                                       class="form-control" step="0.5" placeholder="63.0">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Estimasi Selesai</label>
                                <input type="date" name="estimated_done" id="estimated-done"
                                       class="form-control"
                                       min="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        {{-- Catatan Resep --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan Resep</label>
                            <textarea name="catatan_resep" id="catatan-resep"
                                      class="form-control" rows="2"
                                      placeholder="Misal: lensa antiradiasi, coating biru, dll..."></textarea>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" onclick="goToStep(1)">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-primary" onclick="goToStep(3)">
                            Lanjut ke Produk <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     FASE 3: PRODUK & CART
     Dipindah dari step-2 lama. Tidak ada perubahan logika.
     ============================================================ --}}
<div id="step-3" class="step-container">
    <div class="row g-3">
        {{-- LEFT: Search & Cart --}}
        <div class="col-lg-8">
            {{-- Search Produk --}}
            <div class="card mb-3">
                <div class="card-body p-3">
                    <div class="position-relative">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="search-product"
                                   class="form-control form-control-lg"
                                   placeholder="Cari produk / kode / merek... (ketik minimal 2 huruf)"
                                   autocomplete="off">
                        </div>
                        <div class="pos-search-result d-none" id="search-result"></div>
                    </div>
                </div>
            </div>

            {{-- Cart --}}
            <div class="card">
                <div class="card-header p-3 d-flex justify-content-between align-items-center">
                    <div><i class="bi bi-cart3 text-primary me-2"></i>Keranjang Belanja</div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearCart()">
                        <i class="bi bi-trash me-1"></i>Kosongkan
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table cart-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width:40%">Produk</th>
                                <th>Harga</th>
                                <th style="width:120px">Qty</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cart-body">
                            <tr id="cart-empty">
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-cart-x fs-1 d-block mb-2 opacity-25"></i>
                                    Belum ada produk dipilih
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Diskon --}}
                <div class="p-3 border-top bg-light">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <label class="form-label mb-0 fw-semibold">Diskon:</label>
                        </div>
                        <div class="col-auto">
                            <div class="input-group input-group-sm">
                                <input type="number" id="diskon-persen" name="diskon_persen"
                                       class="form-control" style="width:80px"
                                       min="0" max="100" value="0" placeholder="0">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-auto text-muted">atau</div>
                        <div class="col-auto">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="diskon-nominal" name="diskon_nominal"
                                       class="form-control" style="width:130px"
                                       value="0" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Summary Preview --}}
        <div class="col-lg-4">
            <div class="card summary-preview">
                <div class="card-header p-3">
                    <i class="bi bi-receipt text-primary me-2"></i>Ringkasan Belanja
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span id="subtotal-text-step3">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Diskon</span>
                        <span id="diskon-text-step3" class="text-danger">- Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Pot. BPJS</span>
                        <span id="bpjs-text-step3">- Rp 0</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total</span>
                        <span id="total-text-step3" class="fw-bold fs-4 text-primary">Rp 0</span>
                    </div>
                </div>
                <div class="card-footer p-3 bg-transparent">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary w-50" onclick="goToStep(2)">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-primary w-50" onclick="goToStep(4)" id="btn-to-payment">
                            Pembayaran <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     FASE 4: PEMBAYARAN
     Dipindah dari bagian kiri step-3 lama.
     Tambahan: toggle Lunas / DP.
     ============================================================ --}}
<div id="step-4" class="step-container">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            {{-- Ringkasan Total --}}
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
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Potongan BPJS</span>
                        <span id="bpjs-text">- Rp 0</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total Bayar</span>
                        <div id="total-display">Rp 0</div>
                    </div>
                </div>
            </div>

            {{-- Metode & Nominal Bayar --}}
            <div class="card mb-3">
                <div class="card-header p-3">
                    <i class="bi bi-credit-card text-primary me-2"></i>Pembayaran
                </div>
                <div class="card-body p-3">

                    {{-- Metode Bayar --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Bayar</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['tunai'=>'Tunai','transfer'=>'Transfer','qris'=>'QRIS','debit'=>'Debit','kredit'=>'Kredit'] as $val => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metode_bayar"
                                       id="mp_{{ $val }}" value="{{ $val }}" {{ $val=='tunai'?'checked':'' }}>
                                <label class="form-check-label" for="mp_{{ $val }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Status Bayar: Lunas / DP --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Pembayaran</label>
                        <div class="payment-status-toggle d-flex gap-2">
                            <input type="radio" class="btn-check" name="payment_status"
                                   id="status-lunas" value="lunas" checked
                                   onchange="onPaymentStatusChange()">
                            <label class="btn btn-outline-success" for="status-lunas">
                                <i class="bi bi-check-circle me-1"></i>Lunas
                            </label>

                            <input type="radio" class="btn-check" name="payment_status"
                                   id="status-dp" value="dp"
                                   onchange="onPaymentStatusChange()">
                            <label class="btn btn-outline-warning" for="status-dp">
                                <i class="bi bi-hourglass-split me-1"></i>DP / Uang Muka
                            </label>
                        </div>
                    </div>

                    {{-- Section DP — muncul kalau pilih DP --}}
                    <div id="dp-section" class="alert alert-warning py-2 px-3 mb-3">
                        <label class="form-label fw-semibold mb-1">Nominal DP</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="dp-amount" name="dp_amount"
                                   class="form-control fw-bold" value="0" placeholder="0">
                        </div>
                        <small class="text-muted mt-1 d-block">
                            Sisa tagihan akan tercatat sebagai piutang.
                        </small>
                    </div>

                    {{-- Jumlah Bayar (selalu tampil) --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold" id="label-bayar">Jumlah Bayar</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="bayar" id="bayar-input"
                                   class="form-control form-control-lg fw-bold"
                                   value="0" required>
                        </div>
                        <div class="invalid-feedback">
                            Jumlah bayar kurang dari total!
                        </div>
                    </div>

                    {{-- Nominal cepat --}}
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        @foreach([50000,100000,200000,500000] as $nom)
                        <button type="button" class="btn btn-outline-secondary btn-sm numpad-btn"
                                onclick="setBayar({{ $nom }})">
                            {{ number_format($nom/1000) }}rb
                        </button>
                        @endforeach
                        <button type="button" class="btn btn-outline-primary btn-sm"
                                onclick="setBayarPas()">Pas</button>
                    </div>

                    {{-- Kembalian --}}
                    <div class="d-flex justify-content-between align-items-center p-2 rounded"
                         style="background:#f0fdf4">
                        <span class="fw-semibold">Kembalian</span>
                        <div id="kembalian-display" class="text-success">Rp 0</div>
                    </div>

                    {{-- Catatan --}}
                    <textarea name="catatan" class="form-control form-control-sm mt-3"
                              rows="2" placeholder="Catatan transaksi..."></textarea>
                </div>
            </div>

            {{-- Navigasi --}}
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary w-50" onclick="goToStep(3)">
                    <i class="bi bi-arrow-left"></i> Kembali
                </button>
                <button type="button" class="btn btn-primary w-50" onclick="goToStep(5)" id="btn-to-confirm">
                    Review & Konfirmasi <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     FASE 5: KONFIRMASI
     Pindahan dari bagian kanan step-3 lama, diperluas.
     Submit ada di sini, bukan di fase 4.
     ============================================================ --}}
<div id="step-5" class="step-container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header p-3 d-flex align-items-center gap-2">
                    <i class="bi bi-check2-circle text-success fs-5"></i>
                    <span class="fw-semibold">Konfirmasi Transaksi</span>
                    <small class="text-muted ms-1">Periksa kembali sebelum diproses</small>
                </div>
                <div class="card-body p-4">

                    {{-- Bagian Pelanggan --}}
                    <div class="confirm-section">
                        <div class="confirm-section-title">
                            <i class="bi bi-person me-1"></i>Pelanggan
                        </div>
                        <div id="confirm-patient">
                            <span class="text-muted fst-italic">Tanpa pasien (transaksi umum)</span>
                        </div>
                    </div>

                    {{-- Bagian Tipe Transaksi --}}
                    <div class="confirm-section">
                        <div class="confirm-section-title">
                            <i class="bi bi-tag me-1"></i>Tipe Transaksi
                        </div>
                        <div id="confirm-tipe">—</div>
                        <div id="confirm-resep" class="mt-2 d-none">
                            <small class="text-muted d-block" id="confirm-resep-detail"></small>
                            <small class="text-muted d-block" id="confirm-estimasi"></small>
                        </div>
                    </div>

                    {{-- Bagian Produk --}}
                    <div class="confirm-section">
                        <div class="confirm-section-title">
                            <i class="bi bi-cart3 me-1"></i>Produk
                        </div>
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-0">Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th class="text-end pe-0">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="cart-readonly-body">
                                <tr>
                                    <td colspan="4" class="text-center text-muted ps-0 py-3">
                                        Belum ada produk
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Bagian Total --}}
                    <div class="confirm-section">
                        <div class="confirm-section-title">
                            <i class="bi bi-receipt me-1"></i>Rincian Harga
                        </div>
                        <div class="confirm-row">
                            <span class="label">Subtotal</span>
                            <span class="value" id="confirm-subtotal">Rp 0</span>
                        </div>
                        <div class="confirm-row">
                            <span class="label">Diskon</span>
                            <span class="value text-danger" id="confirm-diskon">- Rp 0</span>
                        </div>
                        <div class="confirm-row">
                            <span class="label">Potongan BPJS</span>
                            <span class="value" id="confirm-bpjs">- Rp 0</span>
                        </div>
                        <hr class="my-1">
                        <div class="confirm-total-row">
                            <span>Total</span>
                            <span id="confirm-total">Rp 0</span>
                        </div>
                    </div>

                    {{-- Bagian Pembayaran --}}
                    <div class="confirm-section">
                        <div class="confirm-section-title">
                            <i class="bi bi-credit-card me-1"></i>Pembayaran
                        </div>
                        <div class="confirm-row">
                            <span class="label">Metode</span>
                            <span class="value" id="confirm-metode">—</span>
                        </div>
                        <div class="confirm-row">
                            <span class="label">Status</span>
                            <span class="value" id="confirm-status-bayar">—</span>
                        </div>
                        <div class="confirm-row" id="confirm-dp-row" style="display:none">
                            <span class="label">Nominal DP</span>
                            <span class="value text-warning fw-bold" id="confirm-dp">Rp 0</span>
                        </div>
                        <div class="confirm-row">
                            <span class="label">Dibayar</span>
                            <span class="value" id="confirm-bayar">Rp 0</span>
                        </div>
                        <div class="confirm-row">
                            <span class="label">Kembalian</span>
                            <span class="value text-success" id="confirm-kembalian">Rp 0</span>
                        </div>
                    </div>

                </div>
                <div class="card-footer p-3 bg-transparent">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary w-50" onclick="goToStep(4)">
                            <i class="bi bi-arrow-left"></i> Kembali Edit
                        </button>
                        <button type="submit" class="btn btn-success w-50 fw-bold" id="btn-bayar">
                            <i class="bi bi-check-circle me-2"></i>Proses Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hidden inputs untuk items --}}
<div id="hidden-items"></div>
</form>

@endsection

@push('scripts')
<script>
const SEARCH_URL = "{{ route('transactions.product.search') }}";
const CSRF       = document.querySelector('meta[name=csrf-token]').content;

let cart        = {};
let currentStep = 1;

// ===================== STEP NAVIGATION =====================
// Validasi per step sebelum boleh maju.
// Loop indicator diupdate dari 3 → 5.
function goToStep(step) {

    // --- Validasi masuk Step 3: cart tidak dicek di sini (baru isi di step 3) ---
    if (step === 3 && currentStep < 3) {
        // Tidak ada validasi khusus, step 2 selalu bisa dilanjutkan
        // (kasir wajib lewati step 2, tapi boleh pilih "Jual Langsung")
    }

    // --- Validasi masuk Step 4: cart harus tidak kosong ---
    if (step === 4 && Object.keys(cart).length === 0) {
        alert('Keranjang belanja masih kosong! Silakan tambahkan produk terlebih dahulu.');
        return;
    }

    // --- Validasi masuk Step 5: bayar harus cukup ---
    if (step === 5) {
        const valid = validatePaymentStep();
        if (!valid) return;
        renderConfirmation(); // populate ringkasan konfirmasi
    }

    // Sembunyikan semua step
    document.querySelectorAll('.step-container').forEach(el => {
        el.classList.remove('active');
    });

    // Tampilkan step yang dipilih
    document.getElementById(`step-${step}`).classList.add('active');

    // Update indicator (5 steps)
    for (let i = 1; i <= 5; i++) {
        const indicator = document.getElementById(`step-${i}-indicator`);
        if (i < step) {
            indicator.classList.add('completed');
            indicator.classList.remove('active');
        } else if (i === step) {
            indicator.classList.add('active');
            indicator.classList.remove('completed');
        } else {
            indicator.classList.remove('active', 'completed');
        }
    }

    currentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validatePaymentStep() {
    const isDP  = document.getElementById('status-dp').checked;
    const total = calculateTotal();
    const bayar = parseAngka(document.getElementById('bayar-input').value);

    if (isDP) {
        const dp = parseAngka(document.getElementById('dp-amount').value);
        if (dp <= 0) {
            alert('Nominal DP harus diisi!');
            return false;
        }
        if (dp > total) {
            alert('Nominal DP tidak boleh melebihi total transaksi!');
            return false;
        }
        return true;
    }

    // Lunas: bayar harus >= total
    if (bayar < total) {
        document.getElementById('bayar-input').classList.add('is-invalid');
        alert('Jumlah bayar kurang dari total!');
        return false;
    }
    return true;
}

function skipPatient() {
    document.getElementById('patient-search').value = '';
    document.getElementById('patient-id').value     = '';
    document.getElementById('med-rec-select').innerHTML =
        '<option value="">-- Pilih Rekam Medis --</option>';
    document.getElementById('bpjs-section').classList.add('d-none');
    document.getElementById('potongan-bpjs').value = 0;
    goToStep(2);
}

// ===================== FASE 2 — TIPE TRANSAKSI =====================
function selectTipe(tipe) {
    // Update kartu visual
    document.getElementById('tipe-regular').classList.toggle('selected', tipe === 'regular');
    document.getElementById('tipe-prescription').classList.toggle('selected', tipe === 'prescription');

    // Simpan ke hidden input
    document.getElementById('transaction-type').value = tipe;

    // Tampilkan / sembunyikan form resep
    const resepForm = document.getElementById('resep-form');
    if (tipe === 'prescription') {
        resepForm.classList.add('show');
        tryAutoFillResep(); // coba auto-fill dari rekam medis jika ada
    } else {
        resepForm.classList.remove('show');
    }
}

// Auto-fill resep dari rekam medis yang dipilih di Fase 1
function tryAutoFillResep() {
    const select = document.getElementById('med-rec-select');
    const badge  = document.getElementById('resep-source-badge');

    // Jika ada rekam medis yang dipilih dan punya data atribut resep
    const selectedOpt = select.options[select.selectedIndex];
    if (selectedOpt && selectedOpt.dataset.odSph) {
        document.getElementById('od-sph').value  = selectedOpt.dataset.odSph  || '';
        document.getElementById('od-cyl').value  = selectedOpt.dataset.odCyl  || '';
        document.getElementById('od-axis').value = selectedOpt.dataset.odAxis || '';
        document.getElementById('os-sph').value  = selectedOpt.dataset.osSph  || '';
        document.getElementById('os-cyl').value  = selectedOpt.dataset.osCyl  || '';
        document.getElementById('os-axis').value = selectedOpt.dataset.osAxis || '';
        document.getElementById('pd').value      = selectedOpt.dataset.pd     || '';
        badge.classList.remove('d-none');
    } else {
        badge.classList.add('d-none');
    }
}

// Jalankan auto-fill juga saat rekam medis berubah (kalau sudah di tipe prescription)
document.getElementById('med-rec-select').addEventListener('change', function () {
    if (document.getElementById('transaction-type').value === 'prescription') {
        tryAutoFillResep();
    }
});

// ===================== FASE 4 — STATUS BAYAR =====================
function onPaymentStatusChange() {
    const isDP      = document.getElementById('status-dp').checked;
    const dpSection = document.getElementById('dp-section');
    const labelBayar = document.getElementById('label-bayar');

    if (isDP) {
        dpSection.classList.add('show');
        labelBayar.textContent = 'Jumlah Diterima Kasir';
    } else {
        dpSection.classList.remove('show');
        labelBayar.textContent = 'Jumlah Bayar';
    }
    updateTotal();
}

// ===================== FASE 5 — RENDER KONFIRMASI =====================
function renderConfirmation() {
    // Pelanggan
    const patientName = document.getElementById('patient-search').value;
    const medRec      = document.getElementById('med-rec-select');
    const medRecText  = medRec.options[medRec.selectedIndex]?.text || '';
    const confirmPatientEl = document.getElementById('confirm-patient');

    if (patientName) {
        let html = `<span class="fw-semibold">${patientName}</span>`;
        if (medRec.value) {
            html += `<br><small class="text-muted">Rekam Medis: ${medRecText}</small>`;
        }
        confirmPatientEl.innerHTML = html;
    } else {
        confirmPatientEl.innerHTML = '<span class="text-muted fst-italic">Tanpa pasien (transaksi umum)</span>';
    }

    // Tipe Transaksi
    const tipe = document.getElementById('transaction-type').value;
    const confirmTipeEl  = document.getElementById('confirm-tipe');
    const confirmResepEl = document.getElementById('confirm-resep');

    if (tipe === 'prescription') {
        confirmTipeEl.innerHTML = '<span class="badge bg-primary">👓 Kacamata / Butuh Proses</span>';
        const odSph = document.getElementById('od-sph').value;
        const osSph = document.getElementById('os-sph').value;
        const pd    = document.getElementById('pd').value;
        const est   = document.getElementById('estimated-done').value;
        const cat   = document.getElementById('catatan-resep').value;

        let resepText = '';
        if (odSph || osSph) {
            resepText += `OD: SPH ${odSph||'—'}  |  OS: SPH ${osSph||'—'}`;
        }
        if (pd) resepText += `  |  PD: ${pd}mm`;
        if (cat) resepText += `<br>Catatan: ${cat}`;

        document.getElementById('confirm-resep-detail').innerHTML = resepText;
        document.getElementById('confirm-estimasi').textContent =
            est ? `Estimasi selesai: ${est}` : '';
        confirmResepEl.classList.remove('d-none');
    } else {
        confirmTipeEl.innerHTML = '<span class="badge bg-secondary">🛍️ Jual Langsung</span>';
        confirmResepEl.classList.add('d-none');
    }

    // Produk (cart readonly)
    renderCartReadonly();

    // Rincian harga — ambil nilai yang sudah dihitung di updateTotal()
    document.getElementById('confirm-subtotal').textContent =
        document.getElementById('subtotal-text').textContent;
    document.getElementById('confirm-diskon').textContent =
        document.getElementById('diskon-text').textContent;
    document.getElementById('confirm-bpjs').textContent =
        document.getElementById('bpjs-text').textContent;
    document.getElementById('confirm-total').textContent =
        document.getElementById('total-display').textContent;

    // Pembayaran
    const metodeBayar  = document.querySelector('input[name="metode_bayar"]:checked')?.value || '—';
    const isDP         = document.getElementById('status-dp').checked;
    const bayarInput   = document.getElementById('bayar-input').value;
    const kembalian    = document.getElementById('kembalian-display').textContent;

    document.getElementById('confirm-metode').textContent =
        metodeBayar.charAt(0).toUpperCase() + metodeBayar.slice(1);
    document.getElementById('confirm-status-bayar').innerHTML = isDP
        ? '<span class="badge bg-warning text-dark">DP / Uang Muka</span>'
        : '<span class="badge bg-success">Lunas</span>';
    document.getElementById('confirm-bayar').textContent = 'Rp ' + bayarInput;
    document.getElementById('confirm-kembalian').textContent = kembalian;

    // Baris DP
    const dpRow = document.getElementById('confirm-dp-row');
    if (isDP) {
        const dpVal = document.getElementById('dp-amount').value;
        document.getElementById('confirm-dp').textContent = 'Rp ' + dpVal;
        dpRow.style.display = 'flex';
    } else {
        dpRow.style.display = 'none';
    }
}

// ===================== SEARCH PRODUK =====================
// Tidak ada perubahan dari kode asli.
let searchTimeout;
document.getElementById('search-product').addEventListener('input', function () {
    clearTimeout(searchTimeout);
    const q = this.value.trim();
    if (q.length < 2) {
        document.getElementById('search-result').classList.add('d-none');
        return;
    }
    searchTimeout = setTimeout(() => fetchProducts(q), 300);
});

async function fetchProducts(q) {
    const res  = await fetch(`${SEARCH_URL}?q=${encodeURIComponent(q)}`);
    const data = await res.json();
    const el   = document.getElementById('search-result');
    if (!data.length) {
        el.innerHTML = '<div class="pos-product-item text-muted">Produk tidak ditemukan</div>';
    } else {
        el.innerHTML = data.map(p => `
            <div class="pos-product-item" onclick='addToCart(${JSON.stringify(p)})'>
                <div>
                    <div class="fw-semibold">${p.nama}</div>
                    <small class="text-muted">${p.kode_produk} ${p.merek ? '· '+p.merek : ''}</small>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-primary">Rp ${formatNum(p.harga_jual)}</div>
                    <small class="text-muted">Stok: ${p.stok}</small>
                </div>
            </div>`).join('');
    }
    el.classList.remove('d-none');
}

document.addEventListener('click', e => {
    if (!e.target.closest('#search-product') && !e.target.closest('#search-result')) {
        document.getElementById('search-result').classList.add('d-none');
    }
});

// ===================== CART =====================
// Tidak ada perubahan dari kode asli.
function addToCart(p) {
    if (cart[p.id]) {
        if (cart[p.id].qty >= p.stok) { alert(`Stok ${p.nama} hanya ${p.stok}`); return; }
        cart[p.id].qty++;
    } else {
        cart[p.id] = { ...p, harga_satuan: p.harga_jual, qty: 1 };
    }
    document.getElementById('search-product').value = '';
    document.getElementById('search-result').classList.add('d-none');
    renderCart();
}

function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    renderCart();
}

function removeItem(id) {
    delete cart[id];
    renderCart();
}

function clearCart() {
    if (confirm('Yakin ingin mengosongkan keranjang?')) {
        cart = {};
        renderCart();
    }
}

function renderCart() {
    const tbody = document.getElementById('cart-body');
    const items = Object.values(cart);

    if (!items.length) {
        tbody.innerHTML = `<tr id="cart-empty">
            <td colspan="5" class="text-center text-muted py-5">
                <i class="bi bi-cart-x fs-1 d-block mb-2 opacity-25"></i>Belum ada produk dipilih
            </td>
        </tr>`;
        updateTotal();
        syncHiddenInputs();
        return;
    }

    tbody.innerHTML = items.map((item) => `
        <tr>
            <td class="ps-3">
                <div class="fw-semibold">${item.nama}</div>
                <small class="text-muted">${item.kode_produk}</small>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" style="width:110px"
                       value="${item.harga_satuan}" min="0"
                       onchange="updateHarga(${item.id}, this.value)">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <button type="button" class="btn btn-outline-secondary" onclick="changeQty(${item.id},-1)">−</button>
                    <input type="text" class="form-control text-center" value="${item.qty}" readonly style="max-width:45px">
                    <button type="button" class="btn btn-outline-secondary" onclick="changeQty(${item.id},1)">+</button>
                </div>
            </td>
            <td class="fw-semibold">Rp ${formatNum(item.harga_satuan * item.qty)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${item.id})">
                    <i class="bi bi-x"></i>
                </button>
            </td>
        </tr>`).join('');

    updateTotal();
    syncHiddenInputs();
}

function renderCartReadonly() {
    const tbody = document.getElementById('cart-readonly-body');
    const items = Object.values(cart);

    if (!items.length) {
        tbody.innerHTML = `<tr>
            <td colspan="4" class="text-center text-muted ps-0 py-3">Belum ada produk</td>
        </tr>`;
        return;
    }

    tbody.innerHTML = items.map(item => `
        <tr>
            <td class="ps-0">
                <div class="fw-semibold">${item.nama}</div>
                <small class="text-muted">${item.kode_produk}</small>
            </td>
            <td>Rp ${formatNum(item.harga_satuan)}</td>
            <td>${item.qty}</td>
            <td class="text-end pe-0 fw-semibold">Rp ${formatNum(item.harga_satuan * item.qty)}</td>
        </tr>`).join('');
}

function updateHarga(id, val) {
    if (cart[id]) { cart[id].harga_satuan = parseFloat(val) || 0; renderCart(); }
}

// ===================== TOTAL =====================
// calculateTotal() dipisah sebagai helper agar bisa dipakai validasi.
function calculateTotal() {
    const items      = Object.values(cart);
    const subtotal   = items.reduce((s, i) => s + (i.harga_satuan * i.qty), 0);
    const diskonP    = parseFloat(document.getElementById('diskon-persen').value) || 0;
    let   diskonNom  = parseAngka(document.getElementById('diskon-nominal').value);
    if (diskonP > 0) diskonNom = Math.round(subtotal * diskonP / 100);
    const potonganBpjs = parseAngka(document.getElementById('potongan-bpjs').value);
    return Math.max(0, subtotal - diskonNom - potonganBpjs);
}

function updateTotal() {
    const items      = Object.values(cart);
    const subtotal   = items.reduce((s, i) => s + (i.harga_satuan * i.qty), 0);
    const diskonP    = parseFloat(document.getElementById('diskon-persen').value) || 0;
    let   diskonNom  = parseAngka(document.getElementById('diskon-nominal').value);
    if (diskonP > 0) diskonNom = Math.round(subtotal * diskonP / 100);

    const potonganBpjs = parseAngka(document.getElementById('potongan-bpjs').value);
    const total        = Math.max(0, subtotal - diskonNom - potonganBpjs);
    const bayar        = parseAngka(document.getElementById('bayar-input').value);
    const kembalian    = bayar - total;

    // Update tampilan di Fase 3 (ringkasan preview)
    document.getElementById('subtotal-text-step3').textContent = 'Rp ' + formatNum(subtotal);
    document.getElementById('diskon-text-step3').textContent   = '- Rp ' + formatNum(diskonNom);
    document.getElementById('bpjs-text-step3').textContent     = '- Rp ' + formatNum(potonganBpjs);
    document.getElementById('total-text-step3').textContent    = 'Rp ' + formatNum(total);

    // Update tampilan di Fase 4 (pembayaran)
    document.getElementById('subtotal-text').textContent  = 'Rp ' + formatNum(subtotal);
    document.getElementById('diskon-text').textContent    = '- Rp ' + formatNum(diskonNom);
    document.getElementById('bpjs-text').textContent      = '- Rp ' + formatNum(potonganBpjs);
    document.getElementById('total-display').textContent  = 'Rp ' + formatNum(total);
    document.getElementById('kembalian-display').textContent =
        'Rp ' + formatNum(Math.max(0, kembalian));
    document.getElementById('kembalian-display').style.color =
        kembalian < 0 ? '#dc3545' : '#16a34a';

    const bayarInput = document.getElementById('bayar-input');
    const btnBayar   = document.getElementById('btn-bayar');

    bayarInput.classList.remove('is-invalid');

    const isDP = document.getElementById('status-dp').checked;
    if (!isDP && bayar < total) {
        bayarInput.classList.add('is-invalid');
        btnBayar.disabled = true;
    } else {
        bayarInput.classList.remove('is-invalid');
        btnBayar.disabled = false;
    }
}

document.getElementById('diskon-persen').addEventListener('input', updateTotal);
document.getElementById('diskon-nominal').addEventListener('input', updateTotal);
document.getElementById('potongan-bpjs').addEventListener('input', updateTotal);
document.getElementById('bayar-input').addEventListener('input', updateTotal);

function setBayar(val) {
    const input = document.getElementById('bayar-input');
    input.value = formatRibuan(val);
    updateTotal();
}

function setBayarPas() {
    const total = calculateTotal();
    document.getElementById('bayar-input').value = formatRibuan(total);
    updateTotal();
}

// ===================== HIDDEN INPUTS =====================
function syncHiddenInputs() {
    const container = document.getElementById('hidden-items');
    container.innerHTML = Object.values(cart).map((item, i) => `
        <input type="hidden" name="items[${i}][product_id]"  value="${item.id}">
        <input type="hidden" name="items[${i}][qty]"          value="${item.qty}">
        <input type="hidden" name="items[${i}][harga_satuan]" value="${item.harga_satuan}">
        <input type="hidden" name="items[${i}][diskon]"       value="0">
    `).join('');
}

// ===================== SEARCH PATIENT =====================
// Tidak ada perubahan dari kode asli.
const patientInput = document.getElementById('patient-search');
const patientList  = document.getElementById('patient-list');
const patientIdEl  = document.getElementById('patient-id');

let patientTimeout = null;

patientInput.addEventListener('keyup', function () {
    clearTimeout(patientTimeout);
    const query = this.value;
    if (query.length < 2) { patientList.innerHTML = ''; return; }
    patientTimeout = setTimeout(() => {
        fetch(`{{ route('patients.search') }}?q=${query}`)
            .then(res => res.json())
            .then(data => {
                patientList.innerHTML = '';
                data.forEach(item => {
                    const el = document.createElement('a');
                    el.classList.add('list-group-item', 'list-group-item-action');
                    el.innerHTML = `
                        <strong>${item.nama}</strong><br>
                        <small class="text-muted">${item.no_rm}</small>
                    `;
                    el.addEventListener('click', () => {
                        patientInput.value  = item.nama;
                        patientIdEl.value   = item.id;
                        patientList.innerHTML = '';
                        loadMedicalRecords(item.id, item.no_bpjs);
                    });
                    el.dataset.id   = item.id;
                    el.dataset.bpjs = item.no_bpjs;
                    patientList.appendChild(el);
                });
            });
    }, 300);
});

document.addEventListener('click', function (e) {
    if (!patientInput.contains(e.target)) {
        patientList.innerHTML = '';
    }
});

// ===================== REKAM MEDIS AJAX =====================
// Perubahan: opsi rekam medis sekarang menyimpan data resep sebagai
// data-* attribute sehingga tryAutoFillResep() bisa membacanya.
async function loadMedicalRecords(patientId, bpjs = null) {
    const sel          = document.getElementById('med-rec-select');
    const bpjsSection  = document.getElementById('bpjs-section');
    const potonganBpjs = document.getElementById('potongan-bpjs');

    sel.innerHTML = '<option value="">-- Pilih Rekam Medis --</option>';
    bpjsSection.classList.add('d-none');
    potonganBpjs.value = 0;

    if (!patientId) return;

    if (bpjs) bpjsSection.classList.remove('d-none');

    try {
        const res  = await fetch(
            `{{ route('transactions.medical-records') }}?patient_id=${patientId}`,
            { headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } }
        );
        const data = await res.json();

        if (data.length === 0) {
            sel.innerHTML += '<option value="" disabled>Belum ada rekam medis</option>';
        } else {
            data.forEach(r => {
                // Simpan data resep ke data-* agar bisa di-auto-fill di Fase 2
                sel.innerHTML += `<option value="${r.id}"
                    data-od-sph="${r.od_sph  ?? ''}"
                    data-od-cyl="${r.od_cyl  ?? ''}"
                    data-od-axis="${r.od_axis ?? ''}"
                    data-os-sph="${r.os_sph  ?? ''}"
                    data-os-cyl="${r.os_cyl  ?? ''}"
                    data-os-axis="${r.os_axis ?? ''}"
                    data-pd="${r.pd          ?? ''}">
                    ${r.tanggal_kunjungan} — OD: ${r.od_sph} OS: ${r.os_sph}
                </option>`;
            });
        }
    } catch (e) {
        console.error('Error fetch rekam medis:', e);
    }
}

// ===================== SUBMIT VALIDATION =====================
document.getElementById('pos-form').addEventListener('submit', function (e) {
    // Validasi: jika nama pasien diketik tapi tidak dipilih dari list
    if (patientInput.value && !patientIdEl.value) {
        e.preventDefault();
        alert('Pilih pasien dari daftar!');
        return;
    }

    // Validasi: cart tidak boleh kosong
    if (!Object.keys(cart).length) {
        e.preventDefault();
        alert('Keranjang belanja kosong!');
        return;
    }

    // Sanitasi format angka sebelum submit
    ['bayar-input', 'diskon-nominal', 'potongan-bpjs', 'dp-amount'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = parseAngka(el.value);
    });

    syncHiddenInputs();
    this.appendChild(document.getElementById('hidden-items'));
});

// ===================== UTILITIES =====================
function formatNum(n) {
    return new Intl.NumberFormat('id-ID').format(Math.round(n));
}

function formatRibuan(value) {
    return new Intl.NumberFormat('id-ID').format(value);
}

function parseAngka(value) {
    return parseInt(String(value).replace(/\./g, '')) || 0;
}

function setupCurrencyInput(el) {
    if (el) {
        el.addEventListener('input', function () {
            let angka = parseAngka(this.value);
            this.value = formatRibuan(angka);
            updateTotal();
        });
    }
}

// Setup currency inputs
setupCurrencyInput(document.getElementById('diskon-nominal'));
setupCurrencyInput(document.getElementById('potongan-bpjs'));
setupCurrencyInput(document.getElementById('bayar-input'));
setupCurrencyInput(document.getElementById('dp-amount'));

// Inisialisasi
renderCart();
</script>
@endpush