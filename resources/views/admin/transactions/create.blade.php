@extends('layouts.admin')

@section('title', 'Point of Sale — Wizard')
@section('page-title', '')

@push('styles')
    <style>
        /* =============================================
           ROOT & BASE
        ============================================= */
        :root {
            --primary: #0d6efd;
            --primary-dark: #0b5ed7;
            --success: #198754;
            --danger: #dc3545;
            --warning: #ffc107;
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.35);
            --gradient-blue: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            --shadow-soft: 0 8px 32px rgba(31, 38, 135, 0.08);
            --radius-card: 18px;
            --radius-input: 10px;
        }

        body {
            background: #f0f2f5;
            font-family: 'Inter', system-ui, sans-serif;
        }

        /* =============================================
           WIZARD STEP INDICATOR
        ============================================= */
        .wizard-steps {
            display: flex;
            align-items: center;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-card);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-soft);
            overflow-x: auto;
            gap: 0;
        }

        .wizard-step {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 130px;
            padding: 8px 10px;
            border-radius: 12px;
            cursor: default;
            transition: background 0.2s;
            user-select: none;
        }

        .wizard-step.is-done {
            cursor: pointer;
        }

        .wizard-step.is-done:hover {
            background: rgba(13, 110, 253, 0.06);
        }

        .ws-num {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
            transition: all 0.3s;
            border: 2px solid #dee2e6;
            background: #fff;
            color: #adb5bd;
        }

        .ws-text {
            line-height: 1.2;
        }

        .ws-title {
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
            transition: color 0.2s;
        }

        .ws-sub {
            font-size: 11px;
            color: #adb5bd;
        }

        .wizard-step.is-active .ws-num {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.35);
        }

        .wizard-step.is-active .ws-title {
            color: #212529;
        }

        .wizard-step.is-active .ws-sub {
            color: #6c757d;
        }

        .wizard-step.is-done .ws-num {
            background: var(--success);
            border-color: var(--success);
            color: #fff;
        }

        .wizard-step.is-done .ws-title {
            color: var(--success);
        }

        .ws-connector {
            flex: 0 0 28px;
            height: 2px;
            background: #dee2e6;
            transition: background 0.3s;
            border-radius: 2px;
            margin: 0 2px;
        }

        .ws-connector.is-done {
            background: var(--success);
        }

        /* =============================================
           STEP PANELS
        ============================================= */
        .wizard-panel {
            display: none;
            animation: fadeSlide 0.25s ease;
        }

        .wizard-panel.is-active {
            display: block;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* =============================================
           CARDS
        ============================================= */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.25rem;
        }

        .glass-card:last-child {
            margin-bottom: 0;
        }

        .card-header-section {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-header-section i {
            font-size: 1rem;
        }

        .badge-opt {
            font-size: 0.65rem;
            background: #f1f3f5;
            color: #6c757d;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 500;
        }

        .badge-req {
            font-size: 0.65rem;
            background: #fff3cd;
            color: #856404;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 500;
        }

        .badge-auto {
            font-size: 0.65rem;
            background: #d1e7dd;
            color: #0a3622;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 500;
        }

        /* =============================================
           FORM ELEMENTS
        ============================================= */
        .form-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .form-control-sm,
        .form-select-sm {
            border-radius: var(--radius-input);
            border: 1px solid #dee2e6;
            padding: 0.45rem 0.7rem;
            background: rgba(255, 255, 255, 0.9);
            font-size: 0.82rem;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-control-sm:focus,
        .form-select-sm:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
            outline: none;
        }

        .form-control-sm.is-invalid {
            border-color: var(--danger);
        }

        .form-control-sm.is-valid {
            border-color: var(--success);
        }

        .invalid-feedback {
            font-size: 0.7rem;
        }

        /* =============================================
           REFRACTION TABLE
        ============================================= */
        .refraction-grid {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #eee;
            overflow: hidden;
        }

        .refraction-grid table {
            margin-bottom: 0;
        }

        .refraction-grid thead th {
            background: #f8f9fa;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #495057;
            padding: 8px 6px;
            text-align: center;
            border-bottom: 2px solid #dee2e6;
        }

        .refraction-grid thead th:first-child {
            text-align: left;
            padding-left: 12px;
        }

        .refraction-grid tbody td {
            padding: 6px 4px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .refraction-grid tbody td:first-child {
            padding-left: 12px;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .refraction-grid input {
            border: none;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
            padding: 4px 6px;
            font-size: 0.82rem;
            width: 100%;
            min-width: 52px;
            transition: background 0.15s;
        }

        .refraction-grid input:focus {
            outline: none;
            background: #e8f0fe;
        }

        /* =============================================
           CART — ALWAYS VISIBLE (STICKY RIGHT)
        ============================================= */
        .cart-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-soft);
            position: sticky;
            top: 1rem;
        }

        .cart-item-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .cart-item-row:last-child {
            border-bottom: none;
        }

        .cart-type-badge {
            font-size: 0.62rem;
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 600;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .ct-frame {
            background: #ede9fe;
            color: #5b21b6;
        }

        .ct-lensa {
            background: #d1fae5;
            color: #065f46;
        }

        .ct-other {
            background: #f3f4f6;
            color: #374151;
        }

        .cart-item-name {
            flex: 1;
            font-size: 0.78rem;
            color: #212529;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cart-item-qty {
            width: 42px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 0.78rem;
            padding: 3px 0;
            background: #fff;
        }

        .cart-item-price {
            font-size: 0.78rem;
            font-weight: 600;
            color: #212529;
            white-space: nowrap;
            min-width: 72px;
            text-align: right;
        }

        .cart-item-del {
            color: #adb5bd;
            cursor: pointer;
            font-size: 0.75rem;
            flex-shrink: 0;
            transition: color 0.15s;
        }

        .cart-item-del:hover {
            color: var(--danger);
        }

        .cart-total-section {
            border-top: 2px solid rgba(0, 0, 0, 0.07);
            margin-top: 8px;
            padding-top: 8px;
        }

        .cart-empty {
            text-align: center;
            padding: 2rem 0;
            color: #adb5bd;
            font-size: 0.8rem;
        }

        /* =============================================
           PRODUCT INPUT FORM (STEP 3)
        ============================================= */
        .product-type-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .ptype-btn {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.73rem;
            font-weight: 600;
            border: 1.5px solid #dee2e6;
            background: #fff;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.15s;
        }

        .ptype-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .ptype-btn.active-frame {
            border-color: #7c3aed;
            background: #ede9fe;
            color: #5b21b6;
        }

        .ptype-btn.active-lensa {
            border-color: #059669;
            background: #d1fae5;
            color: #065f46;
        }

        .ptype-btn.active-other {
            border-color: #374151;
            background: #f3f4f6;
            color: #374151;
        }

        /* =============================================
           FINANCE CARD (STEP 4)
        ============================================= */
        .finance-card {
            background: var(--gradient-blue);
            border-radius: var(--radius-card);
            padding: 1.5rem;
            color: #fff;
        }

        .finance-card .form-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.72rem;
        }

        .finance-card .form-control {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #fff;
            border-radius: var(--radius-input);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .finance-card .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .finance-card .form-control:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: none;
        }

        .sisa-field {
            background: rgba(220, 53, 69, 0.25) !important;
            border-color: rgba(220, 53, 69, 0.5) !important;
            font-size: 1.1rem !important;
        }

        .finance-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.72rem;
            font-weight: 600;
            display: block;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* =============================================
           CHECKOUT SUMMARY
        ============================================= */
        .summary-box {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 16px;
        }

        .summary-box .s-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            padding: 3px 0;
        }

        .summary-box .s-label {
            color: rgba(255, 255, 255, 0.75);
        }

        .summary-box .s-val {
            font-weight: 600;
            color: #fff;
        }

        .summary-box .s-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 6px 0;
        }

        /* =============================================
           NAVIGATION BAR (BOTTOM)
        ============================================= */
        .wizard-nav-bar {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-card);
            padding: 1rem 1.5rem;
            box-shadow: var(--shadow-soft);
            margin-top: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .wizard-nav-bar .step-hint {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .wizard-nav-bar .nav-right {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        /* =============================================
           ACTION BAR (TOP GLOBAL)
        ============================================= */
        .global-action-bar {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-card);
            padding: 0.75rem 1.25rem;
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: space-between;
        }

        .global-action-bar .trx-info {
            font-size: 0.78rem;
            color: #6c757d;
        }

        .global-action-bar .trx-info strong {
            color: var(--primary);
        }

        .btn-action {
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.78rem;
            padding: 0.5rem 1rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
            border: 1px solid transparent;
        }

        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .btn-action:active {
            transform: translateY(0);
        }

        /* =============================================
           AUTOCOMPLETE DROPDOWN
        ============================================= */
        .ac-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1050;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0 0 10px 10px;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .ac-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f8f8f8;
            font-size: 0.78rem;
        }

        .ac-item:hover {
            background: #f0f4ff;
            color: var(--primary);
        }

        .ac-item:last-child {
            border-bottom: none;
        }

        /* =============================================
           VALIDATION ERROR STATES
        ============================================= */
        .step-error-msg {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.78rem;
            color: #664d03;
            margin-bottom: 12px;
            display: none;
            align-items: flex-start;
            gap: 8px;
        }

        .step-error-msg.show {
            display: flex;
        }

        /* =============================================
           HISTORY REFRACTION BADGE
        ============================================= */
        .history-tag {
            font-size: 0.67rem;
            background: #e8f0fe;
            color: #1a56db;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 500;
        }

        /* =============================================
           RESPONSIVE
        ============================================= */
        @media (max-width: 767px) {
            .wizard-steps {
                padding: 0.75rem 1rem;
            }

            .ws-sub {
                display: none;
            }

            .wizard-nav-bar {
                padding: 0.75rem 1rem;
            }
        }

        /* =============================================
           PRINT FRAME
        ============================================= */
        #printFrame {
            display: none;
        }

        /* =============================================
           CHECKOUT READ-ONLY ITEMS
        ============================================= */
        .co-item-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.8rem;
        }

        .co-item-row:last-child {
            border-bottom: none;
        }

        .co-item-name {
            flex: 1;
            color: rgba(255, 255, 255, 0.9);
        }

        .co-item-qty {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.72rem;
        }

        .co-item-price {
            font-weight: 600;
            color: #fff;
        }

        .btn-xs {
            padding: 3px 8px;
            font-size: .75rem;
        }
    </style>
@endpush

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <form action="{{ route('transactions.store') }}" method="POST" id="pos-form">
                @csrf
                <input type="hidden" name="transaction_data" id="transaction_data" value="">

                <input type="hidden" name="id" id="trx_id">
                <input type="hidden" name="patient_id" id="patient_id">
                <input type="hidden" name="cart_data" id="cart_data" value="[]">

                {{-- =============================================
                GLOBAL ACTION BAR (selalu tampil)
                ============================================= --}}
                <div class="global-action-bar">
                    <div class="trx-info">
                        <strong id="display-no-trx">—</strong>
                        <span class="mx-2">|</span>
                        <span id="display-step-label">Step 1: Transaksi</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-action btn-secondary shadow-sm" onclick="openSearchModal()"><i
                                class="bi bi-search"></i> Cari</button>
                    </div>
                </div>

                {{-- =============================================
                STEP INDICATOR
                ============================================= --}}
                <div class="wizard-steps" id="wizardStepIndicator">
                    <div class="wizard-step is-active" id="step-tab-1" onclick="tryGoStep(1)">
                        <div class="ws-num">1</div>
                        <div class="ws-text">
                            <div class="ws-title">Transaksi</div>
                            <div class="ws-sub">No faktur &amp; tanggal</div>
                        </div>
                    </div>
                    <div class="ws-connector" id="conn-1"></div>
                    <div class="wizard-step" id="step-tab-2" onclick="tryGoStep(2)">
                        <div class="ws-num">2</div>
                        <div class="ws-text">
                            <div class="ws-title">Pasien &amp; Resep</div>
                            <div class="ws-sub">Data + refraksi</div>
                        </div>
                    </div>
                    <div class="ws-connector" id="conn-2"></div>
                    <div class="wizard-step" id="step-tab-3" onclick="tryGoStep(3)">
                        <div class="ws-num">3</div>
                        <div class="ws-text">
                            <div class="ws-title">Produk</div>
                            <div class="ws-sub">Frame &amp; lensa</div>
                        </div>
                    </div>
                    <div class="ws-connector" id="conn-3"></div>
                    <div class="wizard-step" id="step-tab-4" onclick="tryGoStep(4)">
                        <div class="ws-num">4</div>
                        <div class="ws-text">
                            <div class="ws-title">Checkout</div>
                            <div class="ws-sub">Pembayaran</div>
                        </div>
                    </div>
                </div>

                {{-- =============================================
                STEP 1 — TRANSAKSI
                ============================================= --}}
                <div class="wizard-panel is-active" id="panel-step-1">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="glass-card">
                                <div class="card-body p-4">
                                    <h6 class="card-header-section">
                                        <i class="bi bi-receipt"></i> Informasi Faktur
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">No Faktur <span
                                                    class="badge-auto ms-1">auto</span></label>
                                            <input type="text" name="no_transaksi" id="no_transaksi"
                                                class="form-control form-control-sm bg-light fw-semibold text-primary"
                                                value="{{ \App\Models\Transaction::generateNomor() }}" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tanggal Faktur</label>
                                            <input type="date" name="tgl_faktur" id="tgl_faktur"
                                                class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tgl Order</label>
                                            <input type="date" name="tgl_order" class="form-control form-control-sm"
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status Ambil</label>
                                            <div class="d-flex gap-3 align-items-center mt-1">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="radio" name="diambil" id="belum"
                                                        value="2" checked>
                                                    <label class="form-check-label small" for="belum">Belum</label>
                                                </div>
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="radio" name="diambil" id="sudah"
                                                        value="1">
                                                    <label class="form-check-label small" for="sudah">Sudah</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tgl Ambil</label>
                                            <input type="date" name="tgl_selesai_janji"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tipe Transaksi</label>
                                            <div class="d-flex gap-3 align-items-center mt-1">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="radio" name="typefaktur"
                                                        id="tunai" value="1" checked>
                                                    <label class="form-check-label small" for="tunai">Umum</label>
                                                </div>
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="radio" name="typefaktur" id="bpjs"
                                                        value="2">
                                                    <label class="form-check-label small" for="bpjs">BPJS</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="glass-card">
                                <div class="card-body p-4">
                                    <h6 class="card-header-section">
                                        <i class="bi bi-gear"></i> Data Tambahan
                                        <span class="badge-opt ms-1">opsional</span>
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Lab</label>
                                            <input type="text" name="lab" class="form-control form-control-sm"
                                                placeholder="Nama lab lensa...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tempat Faset</label>
                                            <input type="text" name="tempat_faset" class="form-control form-control-sm"
                                                placeholder="Lokasi faset...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">No Legalisasi</label>
                                            <input type="text" name="no_legalisasi" class="form-control form-control-sm"
                                                placeholder="No. legalisasi...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tgl Legalisasi</label>
                                            <input type="date" name="tgl_legalisasi" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tgl Faset</label>
                                            <input type="date" name="tgl_faset" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tgl Datang Faset</label>
                                            <input type="date" name="tgl_datang_faset" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tgl Selesai Faset</label>
                                            <input type="date" name="tgl_selesai_faset"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Catatan</label>
                                            <textarea name="catatan" class="form-control form-control-sm" rows="2"
                                                placeholder="Keterangan untuk lab / faset..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wizard-nav-bar">
                        <span class="step-hint"><i class="bi bi-info-circle me-1"></i>No faktur &amp; tanggal wajib
                            diisi</span>
                        <div class="nav-right">
                            <button type="button" class="btn btn-action btn-primary shadow-sm px-4" onclick="goNextStep(1)">
                                Lanjut ke Pasien &amp; Resep <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- =============================================
                STEP 2 — PASIEN + RESEP + REFRAKSI
                ============================================= --}}
                <div class="wizard-panel" id="panel-step-2">
                    <div class="step-error-msg" id="err-step-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span id="err-step-2-msg">Mohon lengkapi data yang wajib diisi.</span>
                    </div>

                    <div class="row g-3">
                        {{-- KIRI: Pasien --}}
                        <div class="col-lg-6">
                            <div class="glass-card">
                                <div class="card-body p-4">
                                    <h6 class="card-header-section">
                                        <i class="bi bi-person-badge"></i> Data Pasien
                                        <span class="badge-opt ms-1">opsional — kosong = UMUM</span>
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Cari Pasien (Nama / BPJS / Telp)</label>
                                            <div class="input-group input-group-sm position-relative">
                                                <input type="text" id="ac_pasien" class="form-control"
                                                    placeholder="Ketik nama atau No BPJS..." autocomplete="off">
                                                <button class="btn btn-outline-secondary" type="button"
                                                    onclick="clearPatient()" title="Reset pasien">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                                <div id="dd_pasien" class="ac-dropdown d-none"></div>
                                            </div>
                                            <div id="patient-selected-badge" class="mt-1 d-none">
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle small py-1 px-2">
                                                    <i class="bi bi-check-circle-fill me-1"></i>
                                                    <span id="patient-selected-name">—</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">No BPJS <span
                                                    class="badge-opt ms-1">opsional</span></label>
                                            <input type="text" name="no_bpjs" id="no_bpjs"
                                                class="form-control form-control-sm" placeholder="000...">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input type="text" name="nama" id="nama_pasien"
                                                class="form-control form-control-sm"
                                                placeholder="Nama pasien (kosong = UMUM)">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">No. Telepon</label>
                                            <input type="text" name="telp" class="form-control form-control-sm"
                                                placeholder="08xx-xxxx-xxxx">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Alamat</label>
                                            <textarea name="alamat" class="form-control form-control-sm" rows="2"
                                                placeholder="Alamat pasien..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- RESEP --}}
                            <div class="glass-card">
                                <div class="card-body p-4">
                                    <h6 class="card-header-section">
                                        <i class="bi bi-file-medical"></i> Resep
                                        <span class="badge-req ms-1">wajib</span>
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6 position-relative">
                                            <label class="form-label">Nama Dokter / Klinik <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="nama_dokter" id="nama_dokter"
                                                class="form-control form-control-sm" placeholder="dr. Nama Dokter..."
                                                autocomplete="off">

                                            <input type="hidden" name="doctor_id" id="doctor_id">

                                            <div id="dd_dokter" class="ac-dropdown d-none"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tanggal Resep</label>
                                            <input type="date" name="tgl_resep" class="form-control form-control-sm"
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Catatan Resep</label>
                                            <textarea name="catatan_resep" class="form-control form-control-sm" rows="2"
                                                placeholder="Instruksi / catatan resep..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KANAN: Refraksi --}}
                        <div class="col-lg-6">
                            <div class="glass-card h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                        <h6 class="card-header-section mb-0">
                                            <i class="bi bi-eye"></i> Refraksi (Ukuran Lensa)
                                            <span class="badge-req ms-1">wajib</span>
                                        </h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-action"
                                            id="btn-load-history" onclick="loadPatientHistory()" style="display:none">
                                            <i class="bi bi-clock-history"></i> Dari Histori
                                        </button>
                                    </div>

                                    <div id="history-tag-container" class="mb-2 d-none">
                                        <span class="history-tag"><i class="bi bi-info-circle me-1"></i>Data dari histori —
                                            bisa diubah langsung</span>
                                    </div>

                                    <div class="refraction-grid">
                                        <table class="table table-sm table-borderless mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="width:55px">Mata</th>
                                                    <th>Sph</th>
                                                    <th>Cyl</th>
                                                    <th>Axis</th>
                                                    <th>Add</th>
                                                    <th>MPD</th>
                                                    <th>Prism</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-primary fw-bold">OD</td>
                                                    <td><input type="text" name="od_sph" id="od_sph" placeholder="0.00"
                                                            inputmode="decimal"></td>
                                                    <td><input type="text" name="od_cyl" id="od_cyl" placeholder="0.00"
                                                            inputmode="decimal"></td>
                                                    <td><input type="text" name="od_axis" id="od_axis" placeholder="0"
                                                            inputmode="numeric"></td>
                                                    <td><input type="text" name="od_add" id="od_add" placeholder="0.00"
                                                            inputmode="decimal"></td>
                                                    <td><input type="text" name="od_mpd" id="od_mpd" placeholder="0.0"
                                                            inputmode="decimal"></td>
                                                    <td><input type="text" name="od_prism" id="od_prism" placeholder="-"
                                                            inputmode="decimal"></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-danger fw-bold">OS</td>
                                                    <td><input type="text" name="os_sph" id="os_sph" placeholder="0.00"
                                                            inputmode="decimal"></td>
                                                    <td><input type="text" name="os_cyl" id="os_cyl" placeholder="0.00"
                                                            inputmode="decimal"></td>
                                                    <td><input type="text" name="os_axis" id="os_axis" placeholder="0"
                                                            inputmode="numeric"></td>
                                                    <td><input type="text" name="os_add" id="os_add" placeholder="0.00"
                                                            inputmode="decimal"></td>
                                                    <td><input type="text" name="os_mpd" id="os_mpd" placeholder="0.0"
                                                            inputmode="decimal"></td>
                                                    <td><input type="text" name="os_prism" id="os_prism" placeholder="-"
                                                            inputmode="decimal"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Histori Rekam Medis --}}
                                    <div id="patient-history-section" class="mt-3 pt-3 border-top d-none">
                                        <h6 class="form-label mb-3">
                                            <i class="bi bi-clipboard2-pulse text-primary me-2"></i>
                                            Histori Rekam Medis (<span id="history-count">0</span> kunjungan terakhir)
                                        </h6>
                                        <div id="patient-history-list">
                                            {{-- Histori akan di-render di sini via JS --}}
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-3 border-top">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Lensa (keterangan)</label>
                                                <input type="text" name="lensa" class="form-control form-control-sm"
                                                    placeholder="Jenis lensa...">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Keterangan Ukuran</label>
                                                <input type="text" name="keterangan_frame"
                                                    class="form-control form-control-sm" placeholder="Catatan ukuran...">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Shortcut refraksi common values --}}
                                    <div class="mt-3 pt-2 border-top">
                                        <div class="form-label mb-1">Shortcut isi OD = OS:</div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-action"
                                            onclick="copyOdToOs()">
                                            <i class="bi bi-arrow-down"></i> Salin OD → OS
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wizard-nav-bar">
                        <button type="button" class="btn btn-action btn-light border shadow-sm" onclick="goStep(1)">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </button>
                        <div class="nav-right">
                            <span class="step-hint">Resep (dokter) &amp; refraksi (min. SPH salah satu mata) wajib
                                diisi</span>
                            <button type="button" class="btn btn-action btn-primary shadow-sm px-4" onclick="goNextStep(2)">
                                Lanjut ke Produk <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- =============================================
                STEP 3 — PRODUK + KERANJANG
                ============================================= --}}
                <div class="wizard-panel" id="panel-step-3">
                    <div class="step-error-msg" id="err-step-3">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span id="err-step-3-msg">Tambahkan minimal 1 produk ke keranjang sebelum melanjutkan.</span>
                    </div>

                    <div class="row g-3">
                        {{-- KIRI: Input Produk --}}
                        <div class="col-lg-7">
                            <div class="glass-card">
                                <div class="card-body p-4">
                                    <h6 class="card-header-section">
                                        <i class="bi bi-box-seam"></i> Tambah Produk
                                    </h6>

                                    <input type="hidden" id="new_item_type" value="Frame">

                                    <div id="product-entry">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label">Cari Produk Database <span
                                                        class="badge-opt ms-1">opsional — bisa skip</span></label>
                                                <div class="input-group input-group-sm position-relative">
                                                    <input type="text" id="ac_produk_new" class="form-control"
                                                        autocomplete="off" placeholder="Ketik kode / nama produk...">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        onclick="clearProductSearch()">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                    <div id="dd_produk_new" class="ac-dropdown d-none"></div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Nama Produk <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="new_item_name" class="form-control form-control-sm"
                                                    placeholder="Nama frame / lensa / item...">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Seri / Merek</label>
                                                <input type="text" id="new_item_seri" class="form-control form-control-sm"
                                                    placeholder="Merek / seri...">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Warna</label>
                                                <input type="text" id="new_item_warna" class="form-control form-control-sm"
                                                    placeholder="Warna...">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Harga Satuan</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light">Rp</span>
                                                    <input type="text" id="new_item_harga" class="form-control text-end"
                                                        placeholder="0" value="0">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Qty</label>
                                                <input type="number" id="new_item_qty"
                                                    class="form-control form-control-sm text-center" value="1" min="1">
                                            </div>
                                            <div class="col-md-4 d-flex align-items-end">
                                                <button type="button" class="btn btn-success btn-action w-100"
                                                    id="btn-add-item" onclick="addItemToCart()">
                                                    <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                                </button>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Keterangan</label>
                                                <input type="text" id="new_item_keterangan"
                                                    class="form-control form-control-sm"
                                                    placeholder="Keterangan tambahan...">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Hidden fields lama (untuk backward compat fillForm) --}}
                                    <div id="product-items" style="display:none">
                                        <div class="product-item">
                                            <input type="hidden" name="product_id[]">
                                            <input type="text" name="kode_frame[]" value="">
                                            <input type="text" name="kode_produk_display[]" value="">
                                            <input type="text" name="nama_produk[]" value="">
                                            <input type="text" name="seri[]" value="">
                                            <input type="text" name="warna[]" value="">
                                            <input type="text" name="keterangan[]" value="">
                                            <input type="text" name="harga_satuan[]" value="0" class="harga-satuan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KANAN: Keranjang Sticky --}}
                        <div class="col-lg-5">
                            <div class="cart-panel p-4">
                                <h6 class="card-header-section">
                                    <i class="bi bi-cart3"></i> Keranjang
                                    <span class="badge bg-primary ms-1 rounded-pill" id="cart-count-badge">0</span>
                                </h6>
                                <div id="cart-inline-body">
                                    <div class="cart-empty" id="cart-empty-msg">
                                        <i class="bi bi-cart-x d-block mb-2" style="font-size:2rem"></i>
                                        Keranjang kosong<br>
                                        <small>Tambahkan minimal 1 item</small>
                                    </div>
                                </div>
                                <div class="cart-total-section mt-2 pt-2" id="cart-total-section" style="display:none">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted fw-semibold">SUBTOTAL</small>
                                        <strong class="text-primary" id="cart-inline-total">Rp 0</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wizard-nav-bar">
                        <button type="button" class="btn btn-action btn-light border shadow-sm" onclick="goStep(2)">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </button>
                        <div class="nav-right">
                            <span class="step-hint">Minimal 1 item di keranjang</span>
                            <button type="button" class="btn btn-action btn-primary shadow-sm px-4" onclick="goNextStep(3)">
                                Lanjut ke Checkout <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- =============================================
                STEP 4 — CHECKOUT
                ============================================= --}}
                <div class="wizard-panel" id="panel-step-4">
                    <div class="row g-3">
                        {{-- KIRI: Pembayaran --}}
                        <div class="col-lg-7">
                            {{-- Summary --}}
                            <div class="glass-card mb-0">
                                <div class="card-body p-4">
                                    <h6 class="card-header-section"><i class="bi bi-clipboard-check"></i> Ringkasan
                                        Transaksi</h6>
                                    <div class="row g-2 mb-2" id="checkout-summary-badges">
                                        {{-- diisi JS --}}
                                    </div>
                                    <div id="checkout-item-list" class="border rounded-3 p-2"
                                        style="max-height:140px;overflow-y:auto;background:#f8f9fa;">
                                        {{-- diisi JS --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KANAN: Finance --}}
                        <div class="col-lg-5">
                            <div class="finance-card">
                                <h6 class="card-header-section text-white mb-3"><i class="bi bi-cash-stack"></i> Rincian
                                    Pembayaran</h6>

                                <div class="summary-box" id="finance-cart-summary">
                                    {{-- diisi JS --}}
                                </div>

                                <div class="mb-3">
                                    <span class="finance-label">Harga Jual <small
                                            style="font-size:0.65rem;opacity:0.7">(bisa diubah)</small></span>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-transparent border-0 text-white fw-bold">Rp</span>
                                        <input type="text" name="harga_jual" id="input_harga_jual"
                                            class="form-control text-end fs-6" value="0">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <span class="finance-label">Potongan / Diskon (−)</span>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-transparent border-0 text-white fw-bold">Rp</span>
                                        <input type="text" name="potongan" id="input_potongan" class="form-control text-end"
                                            value="0">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <span class="finance-label">DP / Bayar Pertama</span>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-transparent border-0 text-white fw-bold">Rp</span>
                                        <input type="text" name="dp" id="input_dp" class="form-control text-end" value="0">
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                                    <span class="finance-label opacity-75 d-block mb-1">Sisa Tagihan</span>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text fw-bold border-0"
                                            style="background:rgba(220,53,69,0.3);color:#fff">Rp</span>
                                        <input type="text" name="sisa" id="input_sisa"
                                            class="form-control text-end fw-bold sisa-field" readonly value="0">
                                    </div>
                                </div>

                                {{-- DP warning --}}
                                <div id="dp-warning" class="mt-2 d-none"
                                    style="background:rgba(220,53,69,0.25);border:1px solid rgba(220,53,69,0.4);border-radius:8px;padding:8px 12px;font-size:0.75rem;color:#fff;">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>DP tidak boleh melebihi harga jual!
                                </div>
                                {{-- Price below cart warning --}}
                                <div id="price-low-warning" class="mt-2 d-none"
                                    style="background:rgba(255,193,7,0.25);border:1px solid rgba(255,193,7,0.4);border-radius:8px;padding:8px 12px;font-size:0.75rem;color:#fff;">
                                    <i class="bi bi-info-circle-fill me-1"></i>Harga jual di bawah total item keranjang.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wizard-nav-bar">
                        <button type="button" class="btn btn-action btn-light border shadow-sm" onclick="goStep(3)">
                            <i class="bi bi-arrow-left"></i> Kembali ke Produk
                        </button>
                        <div class="nav-right">
                            <span class="step-hint">Review semua data sebelum simpan</span>
                            <button type="submit" class="btn btn-action btn-success shadow-sm px-4" id="btn-simpan">
                                <i class="bi bi-save"></i> Simpan Transaksi
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- =============================================
    MODALS
    ============================================= --}}
    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:18px;backdrop-filter:blur(16px);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title card-header-section mb-0"><i class="bi bi-search"></i> Cari Riwayat Transaksi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="modalSearchInput"
                        class="form-control form-control-lg mb-3 shadow-sm rounded-pill"
                        placeholder="Ketik No Transaksi, Nama, atau BPJS..." autocomplete="off">
                    <div class="table-responsive rounded-3 border" style="max-height: 350px;">
                        <table class="table table-hover align-middle mb-0" id="searchTable">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Tgl</th>
                                    <th>No Faktur</th>
                                    <th>Pasien</th>
                                    <th class="text-end">Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3" id="searchPagination">
                        <!-- Pagination will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Modal -->
    <div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:18px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title card-header-section mb-0"><i class="bi bi-printer"></i> Print Hub</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    <p class="text-muted mb-4 small">Pilih format cetak untuk transaksi ini.</p>
                    <div class="d-grid gap-2 col-9 mx-auto">
                        <button class="btn btn-outline-primary btn-action justify-content-center"
                            onclick="doPrint('pesanan_besar')"><i class="bi bi-file-earmark-text"></i> Cetak Bon Pesanan
                            Besar</button>
                        <button class="btn btn-outline-primary btn-action justify-content-center"
                            onclick="doPrint('bon_3_rangkap')"><i class="bi bi-file-earmark-ruled"></i> Cetak Bon (3
                            Rangkap)</button>
                        <button class="btn btn-outline-primary btn-action justify-content-center"
                            onclick="doPrint('fasetan')"><i class="bi bi-file-earmark-medical"></i> Cetak Bon
                            Fasetan</button>
                        <button class="btn btn-outline-primary btn-action justify-content-center"
                            onclick="doPrint('garansi')"><i class="bi bi-patch-check"></i> Cetak Kartu Garansi</button>
                        <button class="btn btn-outline-primary btn-action justify-content-center"
                            onclick="doPrint('formulir_bpjs')"><i class="bi bi-file-earmark-medical"></i> Cetak Formulir
                            BPJS</button>
                        <button class="btn btn-outline-secondary btn-action justify-content-center mt-1"
                            onclick="doPrint('bon_1_rangkap')"><i class="bi bi-receipt"></i> Cetak Bon Standar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <iframe id="printFrame" src=""></iframe>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        /* ==========================================================
           HELPERS
        ========================================================== */
        function parseAngka(val) {
            if (!val) return 0;
            return parseInt(String(val).replace(/\./g, '').replace(',', '')) || 0;
        }
        function formatRibuan(val) {
            return new Intl.NumberFormat('id-ID').format(val);
        }
        function toast(icon, title, text = '') {
            Swal.fire({ icon, title, text, timer: 1400, showConfirmButton: false, toast: false });
        }
        function snackbar(msg, type = 'success') {
            Swal.fire({
                icon: type, title: msg, toast: true,
                position: 'bottom-end', showConfirmButton: false, timer: 2200,
                timerProgressBar: true
            });
        }

        /* ==========================================================
           WIZARD STATE
        ========================================================== */
        let currentStep = 1;
        const TOTAL_STEPS = 4;

        const STEP_LABELS = {
            1: 'Step 1: Transaksi',
            2: 'Step 2: Pasien & Resep',
            3: 'Step 3: Produk',
            4: 'Step 4: Checkout',
        };

        function updateStepUI(step) {
            // Panels
            for (let i = 1; i <= TOTAL_STEPS; i++) {
                document.getElementById('panel-step-' + i).classList.remove('is-active');
            }
            document.getElementById('panel-step-' + step).classList.add('is-active');

            // Tabs
            for (let i = 1; i <= TOTAL_STEPS; i++) {
                const tab = document.getElementById('step-tab-' + i);
                tab.classList.remove('is-active', 'is-done');
                if (i === step) tab.classList.add('is-active');
                else if (i < step) tab.classList.add('is-done');
            }

            // Connectors
            for (let i = 1; i < TOTAL_STEPS; i++) {
                const conn = document.getElementById('conn-' + i);
                if (conn) conn.classList.toggle('is-done', i < step);
            }

            // Display label
            document.getElementById('display-step-label').textContent = STEP_LABELS[step];
            document.getElementById('display-no-trx').textContent =
                document.querySelector('input[name="no_transaksi"]').value || '—';

            // Autofocus per step
            const focuses = {
                1: '#no_transaksi',
                2: '#ac_pasien',
                3: '#new_item_name',
                4: '#input_harga_jual',
            };
            const focusEl = document.querySelector(focuses[step]);
            if (focusEl) setTimeout(() => focusEl.focus(), 180);

            currentStep = step;

            // Step 4 specific: refresh checkout summary
            if (step === 4) buildCheckoutSummary();
        }

        function goStep(n) {
            // Always allow going back
            updateStepUI(n);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function tryGoStep(n) {
            // Only allow jumping to done steps (clicking indicator)
            if (n < currentStep) { goStep(n); return; }
            if (n === currentStep) return;
            // Forward: must validate all intermediate steps
            let ok = true;
            for (let i = currentStep; i < n; i++) {
                if (!validateStep(i)) { ok = false; break; }
            }
            if (ok) goStep(n);
        }

        function goNextStep(fromStep) {
            if (!validateStep(fromStep)) return;
            goStep(fromStep + 1);
        }

        /* ==========================================================
           VALIDATION
        ========================================================== */
        function validateStep(step) {
            hideStepError(step);

            if (step === 1) {
                const noTrx = document.querySelector('input[name="no_transaksi"]').value.trim();
                const tgl = document.getElementById('tgl_faktur') ? document.getElementById('tgl_faktur').value : '';
                if (!noTrx) { showStepError(1, 'No Faktur tidak boleh kosong.'); return false; }
                if (!tgl) { showStepError(1, 'Tanggal faktur tidak boleh kosong.'); return false; }
                return true;
            }

            if (step === 2) {
                const dokter = document.getElementById('nama_dokter').value.trim();
                const odSph = document.getElementById('od_sph').value.trim();
                const osSph = document.getElementById('os_sph').value.trim();

                if (!dokter) {
                    showStepError(2, 'Nama dokter / klinik penulis resep wajib diisi.');
                    document.getElementById('nama_dokter').focus();
                    document.getElementById('nama_dokter').classList.add('is-invalid');
                    return false;
                }
                document.getElementById('nama_dokter').classList.remove('is-invalid');

                const refractionFields = ['od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_mpd', 'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_mpd'];

                const hasAnyRefraction = refractionFields.some(fieldId => {
                    const value = document.getElementById(fieldId).value.trim();
                    return value !== '';
                });

                if (!hasAnyRefraction) {
                    showStepError(2, 'Refraksi wajib diisi — minimal satu field (Sph/Cyl/Axis/Add/MPD) untuk OD atau OS.');
                    document.getElementById('od_sph').focus();
                    return false;
                }

                return true;
            }

            if (step === 3) {
                if (cart.length === 0) {
                    showStepError(3, 'Tambahkan minimal 1 produk ke keranjang sebelum melanjutkan.');
                    return false;
                }
                return true;
            }

            if (step === 4) {
                const dp = parseAngka(document.getElementById('input_dp').value);
                const harga = parseAngka(document.getElementById('input_harga_jual').value);
                if (dp > harga && harga > 0) {
                    toast('error', 'DP tidak boleh melebihi harga jual!');
                    return false;
                }
                return true;
            }

            return true;
        }

        function showStepError(step, msg) {
            const el = document.getElementById('err-step-' + step);
            if (!el) return;
            document.getElementById('err-step-' + step + '-msg').textContent = msg;
            el.classList.add('show');
        }
        function hideStepError(step) {
            const el = document.getElementById('err-step-' + step);
            if (el) el.classList.remove('show');
        }

        /* ==========================================================
           AUTOCOMPLETE ENGINE
        ========================================================== */
        function setupAC(inp, dd, url, mapFn, onSelect) {
            let timer = null;
            inp.addEventListener('input', function () {
                clearTimeout(timer);
                const q = this.value.trim();
                if (q.length < 1) { dd.classList.add('d-none'); return; }
                timer = setTimeout(() => {
                    fetch(`${url}?q=${encodeURIComponent(q)}`)
                        .then(r => r.json())
                        .then(data => {
                            if (!data.length) {
                                dd.innerHTML = '<div class="ac-item text-muted">Tidak ditemukan</div>';
                            } else {
                                dd.innerHTML = data.slice(0, 8).map(item =>
                                    `<div class="ac-item" data-item='${JSON.stringify(item).replace(/'/g, "&#39;")}'>${mapFn(item)}</div>`
                                ).join('');
                                dd.querySelectorAll('.ac-item[data-item]').forEach(el => {
                                    el.addEventListener('click', function () {
                                        onSelect(JSON.parse(this.dataset.item));
                                        dd.classList.add('d-none');
                                    });
                                });
                            }
                            dd.classList.remove('d-none');
                        })
                        .catch(() => dd.classList.add('d-none'));
                }, 280);
            });
            document.addEventListener('click', e => {
                if (!inp.contains(e.target) && !dd.contains(e.target)) dd.classList.add('d-none');
            });
            inp.addEventListener('keydown', e => {
                if (e.key === 'Escape') dd.classList.add('d-none');
            });
        }

        // Pasien Autocomplete
        setupAC(
            document.getElementById('ac_pasien'),
            document.getElementById('dd_pasien'),
            '{{ route('patients.autocomplete') }}',
            p => `<b>${p.nama}</b> <span class="text-muted small">${p.no_bpjs || ''} ${p.no_hp || ''}</span>`,
            p => {
                document.getElementById('patient_id').value = p.id;
                document.getElementById('no_bpjs').value = p.no_bpjs || '';
                document.getElementById('nama_pasien').value = p.nama || '';
                document.querySelector('input[name="telp"]').value = p.no_hp || '';
                document.querySelector('textarea[name="alamat"]').value = p.alamat || '';
                document.getElementById('ac_pasien').value = p.nama || '';
                // Show selected badge
                document.getElementById('patient-selected-name').textContent = p.nama;
                document.getElementById('patient-selected-badge').classList.remove('d-none');
                // Show history button
                document.getElementById('btn-load-history').style.display = '';
            }
        );

        // Dokter Autocomplete
        setupAC(
            document.getElementById('nama_dokter'),
            document.getElementById('dd_dokter'),
            '{{ route('doctors.autocomplete') }}',
            d => `<b>${d.name}</b>`,
            d => {
                document.getElementById('nama_dokter').value = d.name;
                document.getElementById('doctor_id').value = d.id;
            }
        );

        document.getElementById('nama_dokter').addEventListener('input', function () {
            document.getElementById('doctor_id').value = '';
        });

        function clearPatient() {
            document.getElementById('patient_id').value = '';
            document.getElementById('ac_pasien').value = '';
            document.getElementById('no_bpjs').value = '';
            document.getElementById('nama_pasien').value = '';
            document.querySelector('input[name="telp"]').value = '';
            document.querySelector('textarea[name="alamat"]').value = '';
            document.getElementById('patient-selected-badge').classList.add('d-none');
            document.getElementById('btn-load-history').style.display = 'none';
            document.getElementById('history-tag-container').classList.add('d-none');
        }

        // Produk Autocomplete (Step 3)
        setupAC(
            document.getElementById('ac_produk_new'),
            document.getElementById('dd_produk_new'),
            '{{ route('products.frame.autocomplete') }}',
            f => `<b>${f.kode_produk || ''}</b> | ${f.nama || ''} <span class="text-muted small">${f.merek || ''}</span>`,
            f => {
                document.getElementById('new_item_name').value = f.nama || '';
                document.getElementById('new_item_seri').value = f.merek || '';
                document.getElementById('new_item_warna').value = f.warna || '';
                document.getElementById('new_item_keterangan').value = f.keterangan || '';
                const harga = parseInt(f.harga_jual) || 0;
                document.getElementById('new_item_harga').value = formatRibuan(harga);
                // Also set hidden product id
                document.querySelector('input[name="product_id[]"]').value = f.id || '';
                document.getElementById('ac_produk_new').value = f.kode_produk || f.nama || '';
                document.getElementById('new_item_name').focus();
            }
        );

        function clearProductSearch() {
            document.getElementById('ac_produk_new').value = '';
            document.getElementById('new_item_name').value = '';
            document.getElementById('new_item_seri').value = '';
            document.getElementById('new_item_warna').value = '';
            document.getElementById('new_item_harga').value = '0';
            document.getElementById('new_item_keterangan').value = '';
            document.querySelector('input[name="product_id[]"]').value = '';
        }

        /* ==========================================================
           CART
        ========================================================== */
        let cart = [];

        function getCartTypeBadge(type) {
            if (type === 'Frame') return 'ct-frame';
            if (type === 'Lensa') return 'ct-lensa';
            return 'ct-other';
        }

        function renderCartInline() {
            const body = document.getElementById('cart-inline-body');
            const totalSec = document.getElementById('cart-total-section');
            const badge = document.getElementById('cart-count-badge');

            const totalQty = cart.reduce((s, i) => s + i.qty, 0);
            badge.textContent = totalQty;

            body.innerHTML = '';

            if (cart.length === 0) {
                body.innerHTML = `
                <div class="cart-empty">
                    <i class="bi bi-cart-x d-block mb-2" style="font-size:2rem"></i>
                    Keranjang kosong<br>
                    <small>Tambahkan minimal 1 item</small>
                </div>
            `;
                totalSec.style.display = 'none';
                return;
            }

            totalSec.style.display = '';

            cart.forEach((item, idx) => {
                const row = document.createElement('div');
                row.className = 'cart-item-row';
                row.innerHTML = `
                <span class="cart-type-badge ${getCartTypeBadge(item.type)}">${item.type}</span>
                <span class="cart-item-name" title="${item.nama}">${item.nama || '—'}</span>
                <input type="number" class="cart-item-qty" value="${item.qty}" min="1" data-index="${idx}">
                <span class="cart-item-price">Rp ${formatRibuan(item.qty * item.harga)}</span>
                <span class="cart-item-del" onclick="removeCartItem(${idx})">✕</span>
            `;
                row.querySelector('.cart-item-qty').addEventListener('change', function () {
                    const val = Math.max(1, parseInt(this.value) || 1);
                    cart[parseInt(this.dataset.index)].qty = val;
                    this.value = val;
                    processCart();
                });
                body.appendChild(row);
            });

            const total = cart.reduce((s, i) => s + i.qty * i.harga, 0);
            document.getElementById('cart-inline-total').textContent = 'Rp ' + formatRibuan(total);
        }

        function processCart() {
            const total = cart.reduce((s, i) => s + i.qty * i.harga, 0);

            // Update hidden fields
            document.getElementById('cart_data').value = JSON.stringify(cart);

            // Sync harga jual default (only if not manually overridden = still matches last total)
            const inputHarga = document.getElementById('input_harga_jual');
            if (parseAngka(inputHarga.value) === 0 || parseAngka(inputHarga.value) === lastAutoHarga) {
                inputHarga.value = formatRibuan(total);
                lastAutoHarga = total;
            }
            checkPriceLow();
            calculateSisa();
            renderCartInline();
        }

        let lastAutoHarga = 0;

        function addItemToCart() {
            const type = document.getElementById('new_item_type').value;
            const nama = document.getElementById('new_item_name').value.trim();
            const seri = document.getElementById('new_item_seri').value.trim();
            const warna = document.getElementById('new_item_warna').value.trim();
            const ket = document.getElementById('new_item_keterangan').value.trim();
            const harga = parseAngka(document.getElementById('new_item_harga').value);
            const qty = Math.max(1, parseInt(document.getElementById('new_item_qty').value) || 1);
            const prodId = document.querySelector('input[name="product_id[]"]').value;

            if (!nama) {
                document.getElementById('new_item_name').classList.add('is-invalid');
                document.getElementById('new_item_name').focus();
                snackbar('Nama produk wajib diisi!', 'warning');
                return;
            }
            document.getElementById('new_item_name').classList.remove('is-invalid');

            // Cek duplikat by nama + type
            const existIdx = cart.findIndex(i => i.nama === nama && i.type === type);
            if (existIdx >= 0) {
                cart[existIdx].qty += qty;
                snackbar(`Qty ${type} "${nama}" diperbarui.`, 'info');
            } else {
                cart.push({ type, nama, seri, warna, keterangan: ket, harga, qty, product_id: prodId });
                snackbar(`${type} "${nama}" ditambahkan ke keranjang.`, 'success');
            }

            processCart();

            // Clear form, fokus ke nama lagi
            document.getElementById('new_item_name').value = '';
            document.getElementById('new_item_seri').value = '';
            document.getElementById('new_item_warna').value = '';
            document.getElementById('new_item_keterangan').value = '';
            document.getElementById('new_item_harga').value = '0';
            document.getElementById('new_item_qty').value = '1';
            document.getElementById('ac_produk_new').value = '';
            document.querySelector('input[name="product_id[]"]').value = '';
            document.getElementById('new_item_name').focus();
        }

        function removeCartItem(idx) {
            cart.splice(idx, 1);
            processCart();
        }

        /* ==========================================================
           FINANCE CALCULATIONS
        ========================================================== */
        const inputHargaJual = document.getElementById('input_harga_jual');
        const inputPotongan = document.getElementById('input_potongan');
        const inputDp = document.getElementById('input_dp');
        const inputSisa = document.getElementById('input_sisa');

        function calculateSisa() {
            const h = parseAngka(inputHargaJual.value);
            const p = parseAngka(inputPotongan.value);
            const d = parseAngka(inputDp.value);
            const sisa = Math.max(0, h - p - d);
            inputSisa.value = formatRibuan(sisa);

            // DP warning
            const dpWarn = document.getElementById('dp-warning');
            if (d > h && h > 0) {
                dpWarn.classList.remove('d-none');
            } else {
                dpWarn.classList.add('d-none');
            }
        }

        function checkPriceLow() {
            const cartTotal = cart.reduce((s, i) => s + i.qty * i.harga, 0);
            const hargaJual = parseAngka(inputHargaJual.value);
            const warn = document.getElementById('price-low-warning');
            if (hargaJual > 0 && hargaJual < cartTotal) {
                warn.classList.remove('d-none');
            } else {
                warn.classList.add('d-none');
            }
        }

        // Formatting on input
        [inputHargaJual, inputPotongan, inputDp].forEach(el => {
            el.addEventListener('input', function () {
                const n = parseAngka(this.value);
                this.value = formatRibuan(n);
                calculateSisa();
                checkPriceLow();
            });
        });

        /* ==========================================================
           CHECKOUT SUMMARY BUILDER
        ========================================================== */
        function buildCheckoutSummary() {
            // Badges row
            const noTrx = document.querySelector('input[name="no_transaksi"]').value || '—';
            const namaPas = document.getElementById('nama_pasien').value.trim() || 'UMUM';
            const odSph = document.getElementById('od_sph').value;
            const osSph = document.getElementById('os_sph').value;
            const refStr = (odSph || osSph) ? `OD ${odSph || '?'} / OS ${osSph || '?'}` : '—';

            document.getElementById('checkout-summary-badges').innerHTML = `
            <div class="col-auto"><span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">${noTrx}</span></div>
            <div class="col-auto"><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small"><i class="bi bi-person me-1"></i>${namaPas}</span></div>
            <div class="col-auto"><span class="badge bg-secondary-subtle text-secondary border px-2 py-1 small"><i class="bi bi-eye me-1"></i>${refStr}</span></div>
        `;

            // Item list
            const listEl = document.getElementById('checkout-item-list');
            if (cart.length === 0) {
                listEl.innerHTML = '<div class="text-muted text-center small py-2">Tidak ada item</div>';
            } else {
                listEl.innerHTML = cart.map(item => `
                <div class="d-flex align-items-center gap-2 py-1 border-bottom" style="font-size:0.8rem">
                    <span class="cart-type-badge ${getCartTypeBadge(item.type)}" style="font-size:0.62rem">${item.type}</span>
                    <span class="flex-grow-1">${item.nama}</span>
                    <span class="text-muted">×${item.qty}</span>
                    <strong>Rp ${formatRibuan(item.qty * item.harga)}</strong>
                </div>
            `).join('');
            }

            // Finance summary in the blue card
            const cartTotal = cart.reduce((s, i) => s + i.qty * i.harga, 0);
            document.getElementById('finance-cart-summary').innerHTML = `
            <div class="s-row"><span class="s-label">Total item keranjang</span><span class="s-val">Rp ${formatRibuan(cartTotal)}</span></div>
            <div class="s-divider"></div>
            <div class="s-row"><span class="s-label">${cart.length} item</span><span class="s-val">${cart.map(i => i.type).join(', ')}</span></div>
        `;

            // Sync harga jual if still 0
            if (parseAngka(inputHargaJual.value) === 0) {
                inputHargaJual.value = formatRibuan(cartTotal);
                lastAutoHarga = cartTotal;
            }
            calculateSisa();
            checkPriceLow();
        }

        /* ==========================================================
           HISTORY REKAM MEDIS
        ========================================================== */
        function loadPatientHistory() {
            const pid = document.getElementById('patient_id').value;
            if (!pid) return;

            fetch(`{{ route('patients.latest-refraction', ':pid') }}`.replace(':pid', pid))
                .then(r => r.json())
                .then(data => {
                    // Isi field refraksi dari latest (backward compatibility)
                    if (data.od_sph !== undefined) {
                        const fields = ['od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_mpd', 'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_mpd'];
                        fields.forEach(f => {
                            const el = document.getElementById(f);
                            if (el && data[f] !== undefined && data[f] !== null) {
                                el.value = data[f];
                                el.dispatchEvent(new Event('input', { bubbles: true }));
                            }
                        });
                        document.getElementById('history-tag-container').classList.remove('d-none');
                        snackbar('Data refraksi dari histori berhasil dimuat. Periksa kembali sebelum lanjut.', 'info');
                    } else {
                        snackbar('Tidak ada histori refraksi untuk pasien ini.', 'info');
                    }

                    // Render histori lengkap
                    if (data.history && data.history.length > 0) {
                        renderPatientHistory(data.history);
                        document.getElementById('history-count').textContent = data.history.length;
                        document.getElementById('patient-history-section').classList.remove('d-none');
                    } else {
                        document.getElementById('patient-history-section').classList.add('d-none');
                    }
                })
                .catch(() => snackbar('Gagal memuat histori pasien.', 'error'));
        }

        function renderPatientHistory(history) {
            const container = document.getElementById('patient-history-list');
            container.innerHTML = '';

            history.forEach(rm => {
                const rmDiv = document.createElement('div');
                rmDiv.className = 'p-3 border-bottom';
                rmDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge bg-primary me-2">${rm.tanggal_kunjungan}</span>
                        <small class="text-muted">Dokter: ${rm.dokter}</small>
                    </div>
                    <button type="button" class="btn btn-xs btn-outline-info" onclick="loadFromHistory('${rm.id}', ${JSON.stringify(rm).replace(/"/g, '&quot;')})">
                        <i class="bi bi-arrow-right-circle"></i> Gunakan
                    </button>
                </div>
                ${rm.keluhan ? `<div class="text-muted small mb-2"><i class="bi bi-chat-dots me-1"></i>${rm.keluhan}</div>` : ''}
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0" style="font-size:.8rem">
                        <thead class="table-light">
                            <tr>
                                <th>Mata</th><th>SPH</th><th>CYL</th><th>AXIS</th><th>ADD</th><th>PD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-danger">OD (Kanan)</span></td>
                                <td>${rm.od_sph ? rm.od_sph : '-'}</td>
                                <td>${rm.od_cyl ? rm.od_cyl : '-'}</td>
                                <td>${rm.od_axis ? rm.od_axis + '°' : '-'}</td>
                                <td>${rm.od_add ? '+' + rm.od_add : '-'}</td>
                                <td>${rm.od_pd ? rm.od_pd : '-'}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info">OS (Kiri)</span></td>
                                <td>${rm.os_sph ? rm.os_sph : '-'}</td>
                                <td>${rm.os_cyl ? rm.os_cyl : '-'}</td>
                                <td>${rm.os_axis ? rm.os_axis + '°' : '-'}</td>
                                <td>${rm.os_add ? '+' + rm.os_add : '-'}</td>
                                <td>${rm.os_pd ? rm.os_pd : '-'}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;
                container.appendChild(rmDiv);
            });
        }

        function loadFromHistory(rmId, rmData) {
            // Isi field refraksi dari histori yang dipilih
            const fields = ['od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_mpd', 'od_prism', 'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_mpd', 'os_prism'];
            fields.forEach(f => {
                const el = document.getElementById(f);
                if (el) {
                    let key = f;
                    if (f.endsWith('_mpd')) key = f.replace('_mpd', '_pd'); // Map mpd to pd for history
                    el.value = rmData[key] || '';
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
            document.getElementById('history-tag-container').classList.remove('d-none');
            snackbar('Data refraksi dari histori berhasil dimuat. Periksa kembali sebelum lanjut.', 'info');
        }

        /* ==========================================================
           REFRAKSI UTILITY
        ========================================================== */
        function copyOdToOs() {
            ['sph', 'cyl', 'axis', 'add', 'mpd', 'prism'].forEach(f => {
                const od = document.getElementById('od_' + f);
                const os = document.getElementById('os_' + f);
                if (od && os) os.value = od.value;
            });
            snackbar('Data OD disalin ke OS.', 'info');
        }

        // Tab key auto-advance dalam tabel refraksi
        (function setupRefractionTabAdvance() {
            const order = ['od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_mpd', 'od_prism', 'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_mpd', 'os_prism'];
            order.forEach((id, idx) => {
                const el = document.getElementById(id);
                if (!el) return;
                el.addEventListener('keydown', function (e) {
                    if (e.key === 'Tab' || e.key === 'Enter') {
                        e.preventDefault();
                        const next = document.getElementById(order[idx + 1]);
                        if (next) next.focus();
                        else document.getElementById('nama_dokter').focus();
                    }
                });
            });
        })();

        /* ==========================================================
           HARGA SATUAN FORMAT
        ========================================================== */
        document.getElementById('new_item_harga').addEventListener('input', function () {
            const n = parseAngka(this.value);
            this.value = formatRibuan(n);
        });

        /* ==========================================================
           SPA: FILL FORM (saat load dari navigasi)
        ========================================================== */
        function fillForm(trx) {
            if (!trx) { toast('info', 'Data tidak ditemukan.'); return; }

            document.getElementById('trx_id').value = trx.id || '';
            document.getElementById('patient_id').value = trx.patient_id || '';
            document.getElementById('display-no-trx').textContent = trx.no_transaksi || '—';

            const fm = document.getElementById('pos-form');
            const sv = (sel, val) => { const el = fm.querySelector(sel); if (el) el.value = val; };

            // Step 1
            sv('input[name="no_transaksi"]', trx.no_transaksi || '');
            sv('input[name="tgl_order"]', trx.tgl_order || '{{ date('Y-m-d') }}');
            sv('input[name="no_legalisasi"]', trx.no_legalisasi || '');
            sv('input[name="tgl_legalisasi"]', trx.tgl_legalisasi || '');
            sv('input[name="tgl_faset"]', trx.tgl_faset || '');
            sv('input[name="lab"]', trx.lab || '');
            sv('input[name="tempat_faset"]', trx.tempat_faset || '');
            sv('input[name="tgl_datang_faset"]', trx.tgl_datang_faset || '');
            sv('input[name="tgl_selesai_faset"]', trx.tgl_selesai_faset || '');
            sv('input[name="tgl_selesai_janji"]', trx.tgl_selesai_janji || '');
            sv('textarea[name="catatan"]', trx.catatan || '');
            if (document.getElementById('tgl_faktur')) {
                document.getElementById('tgl_faktur').value = trx.tgl_faktur || trx.tgl_order || '{{ date('Y-m-d') }}';
            }

            // Step 2 — Pasien
            const p = trx.patient || {};
            sv('input[name="no_bpjs"]', p.no_bpjs || trx.no_bpjs || '');
            sv('input[name="nama"]', p.nama || trx.nama_pasien || '');
            sv('textarea[name="alamat"]', p.alamat || trx.alamat_pasien || '');
            sv('input[name="telp"]', p.no_hp || trx.telp_pasien || '');
            sv('input[name="asal_resep"]', trx.asal_resep || '');
            document.getElementById('nama_pasien').value = p.nama || trx.nama_pasien || '';
            if (p.nama) {
                document.getElementById('patient-selected-name').textContent = p.nama;
                document.getElementById('patient-selected-badge').classList.remove('d-none');
                document.getElementById('btn-load-history').style.display = '';
            }

            // Step 2 — Resep
            sv('input[name="nama_dokter"]', trx.nama_dokter || trx.asal_resep || '');

            // Step 2 — Refraksi
            ['od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_mpd', 'od_prism', 'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_mpd', 'os_prism'].forEach(f => {
                const el = document.getElementById(f);
                if (el) el.value = trx[f] || '';
            });
            sv('input[name="lensa"]', trx.lensa || '');
            sv('input[name="keterangan_frame"]', trx.keterangan_frame || '');

            // Step 3 — Produk/Cart
            cart = [];
            if (trx.items && trx.items.length) {
                trx.items.forEach(item => {
                    cart.push({
                        type: item.type || 'Lainnya',
                        nama: item.nama_produk || '',
                        seri: item.seri || trx.seri || '',
                        warna: item.warna || trx.warna || '',
                        keterangan: item.keterangan || trx.keterangan_frame || '',
                        harga: parseFloat(item.harga_satuan) || 0,
                        qty: item.qty || 1,
                        product_id: item.product_id || '',
                    });
                });
            } else if (trx.kode_frame || trx.nama_produk) {
                // Fallback for old single-item format
                cart.push({
                    type: 'Lainnya',
                    nama: trx.nama_produk || trx.kode_frame || '',
                    seri: trx.seri || '',
                    warna: trx.warna || '',
                    keterangan: trx.keterangan_frame || '',
                    harga: trx.items && trx.items[0] ? parseFloat(trx.items[0].harga_satuan) : 0,
                    qty: 1,
                });
            }

            // Step 4 — Finance
            const hj = parseFloat(trx.harga_jual || trx.total_harga || 0);
            lastAutoHarga = 0; // reset so it doesn't lock
            inputHargaJual.value = formatRibuan(hj);
            inputDp.value = formatRibuan(parseFloat(trx.dp || trx.bayar || 0));
            inputPotongan.value = formatRibuan(parseFloat(trx.potongan || trx.diskon_nominal || 0));
            calculateSisa();

            // Radios
            const tf = trx.typefaktur == 2 ? 'bpjs' : 'tunai';
            const db = trx.diambil == 1 ? 'sudah' : 'belum';
            document.getElementById(tf).checked = true;
            document.getElementById(db).checked = true;

            processCart();
            document.getElementById('btn-simpan').innerHTML = '<i class="bi bi-save"></i> Update';

            // Go to step 1 after load
            goStep(1);
        }

        /* ==========================================================
           RESET
        ========================================================== */
        function resetWizard() {
            Swal.fire({
                title: 'Reset form?',
                text: 'Semua data yang belum disimpan akan hilang.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#f59e0b',
            }).then(r => {
                if (r.isConfirmed) doReset();
            });
        }
        function doReset() {
            document.getElementById('pos-form').reset();
            document.getElementById('trx_id').value = '';
            document.getElementById('patient_id').value = '';
            cart = [];
            lastAutoHarga = 0;
            inputHargaJual.value = '0';
            inputDp.value = '0';
            inputPotongan.value = '0';
            inputSisa.value = '0';
            clearPatient();
            document.getElementById('btn-simpan').innerHTML = '<i class="bi bi-save"></i> Simpan';
            processCart();
            goStep(1);
            snackbar('Form berhasil direset.', 'info');
        }

        /* ==========================================================
           NAVIGATION (SPA prev/next transaction)
        ========================================================== */
        function navTransaction(dir) {
            const currentId = document.getElementById('trx_id').value;
            fetch(`{{ route('transactions.pos.nav') }}?dir=${dir}&current_id=${currentId}`)
                .then(r => r.json())
                .then(data => fillForm(data))
                .catch(() => toast('error', 'Gagal memuat data transaksi.'));
        }

        /* ==========================================================
           (FORM SUBMIT LISTENER MOVED TO BOTTOM)
        ========================================================== */

        /* ==========================================================
           DELETE
        ========================================================== */
        function deleteTransaction() {
            const id = document.getElementById('trx_id').value;
            if (!id) { toast('info', 'Pilih transaksi terlebih dahulu.'); return; }
            Swal.fire({
                title: 'Hapus Transaksi?',
                text: 'Data yang dihapus tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`{{ url('admin/transactions/pos/delete') }}/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.status === 'success') {
                                snackbar('Transaksi berhasil dihapus.', 'success');
                                doReset();
                            } else {
                                toast('error', 'Gagal menghapus data.');
                            }
                        });
                }
            });
        }

        /* ==========================================================
           SEARCH MODAL
        ========================================================== */
        const searchModalEl = new bootstrap.Modal(document.getElementById('searchModal'));

        function openSearchModal() {
            searchModalEl.show();
            loadSearchData('', 1);
            setTimeout(() => document.getElementById('modalSearchInput').focus(), 300);
        }

        let searchTimer;
        document.getElementById('modalSearchInput').addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => loadSearchData(this.value, 1), 300);
        });

        function loadSearchData(q, page = 1) {
            const tbody = document.querySelector('#searchTable tbody');
            const paginationEl = document.getElementById('searchPagination');
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3"><div class="spinner-border text-primary spinner-border-sm"></div></td></tr>';
            paginationEl.innerHTML = '';
            fetch(`{{ route('transactions.pos.search') }}?q=${encodeURIComponent(q)}&page=${page}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.data || !data.data.length) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted small py-3">Tidak ada data</td></tr>';
                        return;
                    }
                    tbody.innerHTML = data.data.map(d => `
                    <tr>
                        <td class="small">${d.tanggal}</td>
                        <td class="fw-bold text-primary" style="cursor:pointer" onclick="selectTrx(${d.id})">${d.no_transaksi}</td>
                        <td>${d.pasien}</td>
                        <td class="text-end fw-semibold">${d.total}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="printTrx(${d.id})"><i class="bi bi-printer me-2"></i>Cetak</a></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteTrx(${d.id})"><i class="bi bi-trash me-2"></i>Hapus</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                `).join('');
                    // Render pagination
                    if (data.last_page > 1) {
                        let paginationHtml = '<nav><ul class="pagination pagination-sm justify-content-center">';
                        if (data.current_page > 1) {
                            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadSearchData('${q}', ${data.current_page - 1})">«</a></li>`;
                        }
                        for (let i = Math.max(1, data.current_page - 2); i <= Math.min(data.last_page, data.current_page + 2); i++) {
                            paginationHtml += `<li class="page-item ${i === data.current_page ? 'active' : ''}"><a class="page-link" href="#" onclick="loadSearchData('${q}', ${i})">${i}</a></li>`;
                        }
                        if (data.current_page < data.last_page) {
                            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadSearchData('${q}', ${data.current_page + 1})">»</a></li>`;
                        }
                        paginationHtml += '</ul></nav>';
                        paginationEl.innerHTML = paginationHtml;
                    }
                });
        }

        function selectTrx(id) {
            searchModalEl.hide();
            window.location.href = `{{ url('admin/transactions') }}/${id}`;
        }

        function printTrx(id) {
            currentPrintId = id;
            printModalEl.show();
        }

        function deleteTrx(id) {
            Swal.fire({
                title: 'Hapus Transaksi?',
                text: 'Data transaksi akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ route('transactions.pos.delete', ':id') }}`.replace(':id', id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).then(r => r.json()).then(data => {
                        if (data.success) {
                            toast('success', 'Transaksi berhasil dihapus');
                            loadSearchData(document.getElementById('modalSearchInput').value, 1); // Reload current page
                        } else {
                            toast('error', 'Gagal menghapus transaksi');
                        }
                    });
                }
            });
        }

        /* ==========================================================
           PRINT MODAL
        ========================================================== */
        const printModalEl = new bootstrap.Modal(document.getElementById('printModal'));
        const printFrame = document.getElementById('printFrame');
        let currentPrintId = null;

        function openPrintModal() {
            currentPrintId = document.getElementById('trx_id').value;
            if (!currentPrintId) { toast('warning', 'Simpan transaksi terlebih dahulu sebelum mencetak.'); return; }
            printModalEl.show();
        }

        function doPrint(type) {
            const id = currentPrintId;
            if (!id) { toast('warning', 'Simpan transaksi terlebih dahulu sebelum mencetak.'); return; }
            const url = `{{ url('transactions') }}/${id}/print?type=${type}`;
            printFrame.src = url;
            printFrame.onload = () => setTimeout(() => printFrame.contentWindow.print(), 500);
            printModalEl.hide();
        }

        // Reset currentPrintId when modal is closed
        document.getElementById('printModal').addEventListener('hidden.bs.modal', function () {
            currentPrintId = null;
        });

        /* ==========================================================
           KEYBOARD SHORTCUTS
        ========================================================== */

        document.addEventListener('keydown', function (e) {
            // Alt+1–4 untuk pindah step
            if (e.altKey && ['1', '2', '3', '4'].includes(e.key)) {
                e.preventDefault();
                tryGoStep(parseInt(e.key));
            }
            // F9 simpan
            if (e.key === 'F9') { e.preventDefault(); document.getElementById('btn-simpan').click(); }
            // F8 tambah produk (saat di step 3)
            if (e.key === 'F8' && currentStep === 3) { e.preventDefault(); addItemToCart(); }
            // Escape tutup dropdown
            if (e.key === 'Escape') {
                document.querySelectorAll('.ac-dropdown').forEach(d => d.classList.add('d-none'));
            }
        });

        /* ==========================================================
           INIT
        ========================================================== */
        document.addEventListener('DOMContentLoaded', () => {
            updateStepUI(1);
            calculateSisa();
            renderCartInline();
        });

        // Listener to clear patient_id if name is cleared manually
        document.getElementById('nama_pasien').addEventListener('input', function () {
            if (this.value.trim() === '') {
                document.getElementById('patient_id').value = '';
                document.getElementById('patient-selected-badge').classList.add('d-none');
                document.getElementById('btn-load-history').style.display = 'none';
                document.getElementById('patient-history-section').classList.add('d-none');
            }
        });

        // Enter key on product name → add to cart
        document.getElementById('new_item_name').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); addItemToCart(); }
        });

        // Handle form submit (WITH FINAL VALIDATION)
        document.getElementById('pos-form').addEventListener('submit', function (e) {
            e.preventDefault();

            // Final validation before submit
            for (let s = 1; s <= 4; s++) {
                if (!validateStep(s)) {
                    goStep(s);
                    return;
                }
            }

            // =============================================
            // BUILD NESTED JSON STRUCTURE
            // =============================================
            const nested = {
                patient: {
                    id: document.getElementById('patient_id').value || null,
                    nama: document.getElementById('nama_pasien').value || null,
                    telp: document.querySelector('input[name="telp"]')?.value || null,
                    alamat: document.querySelector('textarea[name="alamat"]')?.value || null,
                    no_bpjs: document.getElementById('no_bpjs')?.value || null,
                },
                transaksi: {
                    no_transaksi: document.getElementById('no_transaksi').value,
                    tgl_faktur: document.getElementById('tgl_faktur').value,
                    tgl_order: document.querySelector('input[name="tgl_order"]')?.value,
                    tipe_faktur: parseInt(document.querySelector('input[name="typefaktur"]:checked')?.value || 1),
                    diambil: parseInt(document.querySelector('input[name="diambil"]:checked')?.value || 2),
                },
                resep: {
                    asal: document.getElementById('nama_dokter')?.value || null,
                    dokter: document.getElementById('doctor_id')?.value || null,
                    tgl_resep: document.querySelector('input[name="tgl_resep"]')?.value || null,
                    catatan: document.querySelector('textarea[name="catatan_resep"]')?.value || null,
                    mata: {
                        od: {
                            sph: parseFloat(document.getElementById('od_sph')?.value) || null,
                            cyl: parseFloat(document.getElementById('od_cyl')?.value) || null,
                            axis: parseInt(document.getElementById('od_axis')?.value) || null,
                            add: parseFloat(document.getElementById('od_add')?.value) || null,
                            mpd: parseFloat(document.getElementById('od_mpd')?.value) || null,
                            prism: parseFloat(document.getElementById('od_prism')?.value) || null,
                        },
                        os: {
                            sph: parseFloat(document.getElementById('os_sph')?.value) || null,
                            cyl: parseFloat(document.getElementById('os_cyl')?.value) || null,
                            axis: parseInt(document.getElementById('os_axis')?.value) || null,
                            add: parseFloat(document.getElementById('os_add')?.value) || null,
                            mpd: parseFloat(document.getElementById('os_mpd')?.value) || null,
                            prism: parseFloat(document.getElementById('os_prism')?.value) || null,
                        }
                    }
                },
                items: cart.map(item => ({
                    type: item.type,
                    nama: item.nama,
                    seri: item.seri || null,
                    warna: item.warna || null,
                    harga: item.harga,
                    qty: item.qty,
                    product_id: item.product_id || null,
                    keterangan: item.keterangan || null,
                })),
                pembayaran: {
                    total: cart.reduce((sum, i) => sum + (i.harga * i.qty), 0),
                    harga_jual: parseAngka(document.getElementById('input_harga_jual').value),
                    diskon: parseAngka(document.getElementById('input_potongan').value),
                    dp: parseAngka(document.getElementById('input_dp').value),
                    sisa: parseAngka(document.getElementById('input_sisa').value),
                },
                jadwal: {
                    tgl_selesai_janji: document.querySelector('input[name="tgl_selesai_janji"]')?.value || null,
                    tgl_faset: document.querySelector('input[name="tgl_faset"]')?.value || null,
                    tgl_datang_faset: document.querySelector('input[name="tgl_datang_faset"]')?.value || null,
                    tgl_selesai_faset: document.querySelector('input[name="tgl_selesai_faset"]')?.value || null,
                },
                tambahan: {
                    lab: document.querySelector('input[name="lab"]')?.value || null,
                    tempat_faset: document.querySelector('input[name="tempat_faset"]')?.value || null,
                    no_legalisasi: document.querySelector('input[name="no_legalisasi"]')?.value || null,
                    tgl_legalisasi: document.querySelector('input[name="tgl_legalisasi"]')?.value || null,
                    catatan: document.querySelector('textarea[name="catatan"]')?.value || null,
                    lensa: document.querySelector('input[name="lensa"]')?.value || null,
                    keterangan_frame: document.querySelector('input[name="keterangan_frame"]')?.value || null,
                }
            };

            // Set ke hidden field
            document.getElementById('transaction_data').value = JSON.stringify(nested);

            // Kirim juga cart_data (opsional, untuk backward compat)
            document.getElementById('cart_data').value = JSON.stringify(cart);

            const submitBtn = document.getElementById('btn-simpan');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';

            fetch(this.action, {
                method: 'POST',
                body: JSON.stringify({ transaction_data: nested }),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(r => r.json())
                .then(data => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-save"></i> Simpan Transaksi';
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Data sudah tersimpan!',
                            text: data.message,
                            icon: 'success',
                            showCancelButton: true,
                            showConfirmButton: true,
                            confirmButtonText: 'Buat Pesanan Baru',
                            cancelButtonText: 'Lihat Riwayat',
                            footer: '<button id="btn-edit" class="btn btn-secondary me-2">Edit Pesanan</button><button id="btn-print" class="btn btn-primary">Print</button>',
                            allowOutsideClick: false,
                            customClass: {
                                confirmButton: 'btn btn-success me-2',
                                cancelButton: 'btn btn-outline-primary'
                            },
                            buttonsStyling: false,
                            didOpen: () => {
                                document.getElementById('btn-edit').addEventListener('click', () => {
                                    Swal.close();
                                    window.location.href = `{{ url('admin/transactions') }}/${data.data.id}`;
                                });
                                document.getElementById('btn-print').addEventListener('click', () => {
                                    Swal.close();
                                    openPrintModal();
                                });
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Buat Pesanan Baru
                                resetForm();
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                // Lihat Riwayat
                                window.location.href = '{{ route("transactions.index") }}';
                            }
                        });
                        // Set trx_id for print
                        document.getElementById('trx_id').value = data.data.id;
                    } else {
                        toast('error', 'Gagal menyimpan', data.message);
                    }
                })
                .catch(err => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-save"></i> Simpan Transaksi';
                    toast('error', 'Error', 'Terjadi kesalahan saat menyimpan');
                });
        });

        function resetForm() {
            // Reset form
            document.getElementById('pos-form').reset();
            // Reset hidden fields
            document.getElementById('trx_id').value = '';
            document.getElementById('patient_id').value = '';
            document.getElementById('cart_data').value = '[]';
            // Reset UI
            goStep(1);
            updateStepUI(1);
            calculateSisa();
            renderCartInline();
            // Clear patient badge
            document.getElementById('patient-selected-badge').classList.add('d-none');
            // Generate new no transaksi
            document.getElementById('no_transaksi').value = '{{ \App\Models\Transaction::generateNomor() }}';
        }
    </script>
@endpush