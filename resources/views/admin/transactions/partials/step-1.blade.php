<div class="wizard-panel is-active" id="panel-step-1">
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="glass-card">
                <div class="card-body p-4">
                    <h6 class="card-header-section">
                        <i class="bi bi-receipt"></i> Informasi Faktur
                    </h6>
                    <div id="err-step-1" class="step-error-msg">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span id="err-step-1-msg"></span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">No Faktur <span class="badge-auto ms-1">auto</span></label>
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
                                    <input class="form-check-input" type="radio" name="diambil" id="belum" value="2" checked>
                                    <label class="form-check-label small" for="belum">Belum</label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="diambil" id="sudah" value="1">
                                    <label class="form-check-label small" for="sudah">Sudah</label>
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
                        <i class="bi bi-gear"></i> Tipe & Pengaturan
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipe Pembelian</label>
                            <div class="d-flex flex-column gap-2 mt-1">
                                <div class="form-check card-radio p-0">
                                    <input class="form-check-input d-none" type="radio" name="typefaktur" id="tunai" value="1" checked>
                                    <label class="form-check-label btn-type-select w-100" for="tunai">
                                        <i class="bi bi-wallet2"></i> UMUM / TUNAI
                                    </label>
                                </div>
                                <div class="form-check card-radio p-0">
                                    <input class="form-check-input d-none" type="radio" name="typefaktur" id="bpjs" value="2">
                                    <label class="form-check-label btn-type-select w-100" for="bpjs">
                                        <i class="bi bi-shield-check"></i> BPJS KESEHATAN
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="bpjs-input-container">
                            <label class="form-label">No. BPJS <span class="badge-req ms-1">Wajib</span></label>
                            <input type="text" name="no_bpjs" id="no_bpjs" class="form-control form-control-sm" placeholder="Contoh: 00012345678">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="wizard-nav-bar mt-4">
        <span class="step-hint">Pastikan No Faktur dan Tipe Pembelian sudah benar.</span>
        <button type="button" class="btn btn-action btn-primary shadow-sm px-4" onclick="goNextStep(1)">
            Lanjut ke Pasien <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</div>

<style>
    .btn-type-select {
        border: 2px solid #dee2e6;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        font-weight: 700;
        font-size: 0.8rem;
        color: #6c757d;
        background: #fff;
    }
    .btn-type-select i { font-size: 1.25rem; }
    input[name="typefaktur"]:checked + .btn-type-select {
        border-color: var(--primary);
        background: rgba(13, 110, 253, 0.05);
        color: var(--primary);
    }
</style>
