<div class="wizard-panel" id="panel-step-4">
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="finance-card shadow-lg h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-wallet2 me-2"></i> Pembayaran</h5>
                    <span class="badge border border-white-50 text-white fw-light" style="font-size:0.7rem">FINANCING ENGINE 2.0</span>
                </div>

                <div id="err-step-4" class="step-error-msg" style="background: rgba(255,255,255,0.2); color:#fff; border: 1.5px solid rgba(255,255,255,0.4);">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span id="err-step-4-msg"></span>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="finance-label">Harga Jual / Total Biaya</label>
                        <input type="text" id="input_harga_jual" class="form-control bg-white-10" value="0">
                    </div>
                    <div class="col-md-6">
                        <label class="finance-label">Diskon / Potongan</label>
                        <input type="text" id="input_potongan" class="form-control bg-white-10" value="0">
                    </div>
                    <div class="col-md-6">
                        <label class="finance-label">DP / Bayar Sekarang</label>
                        <input type="text" id="input_dp" class="form-control bg-white-10" value="0">
                    </div>
                    <div class="col-md-6">
                        <label class="finance-label">Sisa Pembayaran / Piutang</label>
                        <input type="text" id="input_sisa" class="form-control bg-white-10 sisa-field" value="0" readonly>
                    </div>
                </div>

                <div class="mt-4 p-3 rounded-3" style="background: rgba(0,0,0,0.1);">
                    <label class="form-label text-white-50 small mb-2 text-uppercase">Informasi Legalisasi & Lab</label>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="no_legalisasi" class="form-control form-control-sm border-0 bg-white-10 text-white" placeholder="No Legalisasi">
                        </div>
                        <div class="col-md-6">
                            <input type="date" name="tgl_legalisasi" class="form-control form-control-sm border-0 bg-white-10 text-white">
                        </div>
                        <div class="col-md-12">
                            <input type="text" name="lab" class="form-control form-control-sm border-0 bg-white-10 text-white" placeholder="Nama Lab">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="glass-card h-100">
                <div class="card-body p-4">
                    <h6 class="card-header-section mb-3"><i class="bi bi-calendar-check"></i> Jadwal & Pengambilan</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Tgl Selesai Janji</label>
                            <input type="date" name="tgl_selesai_janji" id="tgl_selesai_janji" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tgl Faset</label>
                            <input type="date" name="tgl_faset" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tgl Selesai Faset</label>
                            <input type="date" name="tgl_selesai_faset" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label">Catatan Tambahan</label>
                            <textarea name="catatan" class="form-control form-control-sm" rows="4" placeholder="Keterangan faset, tempat faset, atau instruksi khusus lainnya..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUMMARY WIDGET --}}
        <div class="col-12 mt-2">
            <div class="glass-card border-success" style="background: rgba(25, 135, 84, 0.05);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-4 border-end">
                            <h2 class="fw-bold mb-0 text-success" id="summary-total">Rp 0</h2>
                            <small class="text-muted text-uppercase fw-bold">Grand Total</small>
                        </div>
                        <div class="col-md-8 ps-md-4">
                            <div id="summary-items" class="small text-muted">
                                <!-- Items summary will be here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wizard-nav-bar mt-4">
        <button type="button" class="btn btn-action btn-light border shadow-sm" onclick="goStep(3)">
            <i class="bi bi-arrow-left"></i> Kembali ke Produk
        </button>
        <div class="nav-right">
            <span class="step-hint d-none d-md-inline">Review semua data sebelum simpan</span>
            <button type="submit" class="btn btn-action btn-success shadow-sm px-4" id="btn-simpan">
                <i class="bi bi-save"></i> Simpan Transaksi (F9)
            </button>
        </div>
    </div>
</div>

<style>
    .bg-white-10 { background: rgba(255,255,255,0.1) !important; color: #fff !important; border: 1.5px solid rgba(255,255,255,0.2) !important; }
    .bg-white-10:focus { background: rgba(255,255,255,0.2) !important; border-color: rgba(255,255,255,0.4) !important; }
    .bg-white-10::placeholder { color: rgba(255,255,255,0.4); }
</style>
