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
                            <label class="form-label">No Faktur <span class="badge-auto ms-1">auto</span></label>
                            <input type="text" name="no_transaksi" id="no_transaksi"
                                class="form-control form-control-sm bg-light fw-semibold text-primary"
                                value="{{ \App\Models\Transaction::generateNomor() }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Faktur</label>
                            <input type="date" name="tgl_faktur" id="tgl_faktur"
                                class="form-control form-control-sm"
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tgl Order</label>
                            <input type="date" name="tgl_order" id="tgl_order"
                                class="form-control form-control-sm"
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
                        <div class="col-md-6">
                            <label class="form-label">Tgl Ambil</label>
                            <input type="date" name="tgl_selesai_janji" id="tgl_selesai_janji"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipe Transaksi</label>
                            <div class="d-flex gap-3 align-items-center mt-1">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="typefaktur" id="tunai" value="1" checked>
                                    <label class="form-check-label small" for="tunai">Umum</label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="typefaktur" id="bpjs" value="2">
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
                            <input type="text" name="lab" class="form-control form-control-sm" placeholder="Nama lab lensa...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tempat Faset</label>
                            <input type="text" name="tempat_faset" class="form-control form-control-sm" placeholder="Lokasi faset...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No Legalisasi</label>
                            <input type="text" name="no_legalisasi" class="form-control form-control-sm" placeholder="No. legalisasi...">
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
                            <input type="date" name="tgl_selesai_faset" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Keterangan untuk lab / faset..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wizard-nav-bar">
        <span class="step-hint"><i class="bi bi-info-circle me-1"></i>No faktur &amp; tanggal wajib diisi</span>
        <div class="nav-right">
            <button type="button" class="btn btn-action btn-primary shadow-sm px-4" onclick="goNextStep(1)">
                Lanjut ke Pasien &amp; Resep <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</div>
