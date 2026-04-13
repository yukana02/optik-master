<div class="wizard-panel" id="panel-step-2">
    <div class="row g-3">
        {{-- PASIEN --}}
        <div class="col-lg-12">
            <div class="glass-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-header-section mb-0"><i class="bi bi-person-circle"></i> Data Pasien</h6>
                        <span class="badge rounded-pill bg-primary d-none shadow-sm" id="patient-selected-badge" style="font-size: 0.75rem;">
                            <i class="bi bi-check2-circle"></i> Terpilih: <span id="patient-selected-name"></span>
                            <i class="bi bi-x-circle ms-2 cursor-pointer" onclick="clearPatient()"></i>
                        </span>
                    </div>

                    <div id="err-step-2" class="step-error-msg">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span id="err-step-2-msg"></span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Cari Pasien / Nama Baru</label>
                            <div class="position-relative">
                                <input type="text" id="ac_pasien" class="form-control form-control-sm" placeholder="Ketik nama atau No RM..." autocomplete="off">
                                <div id="dd_pasien" class="ac-dropdown d-none"></div>
                            </div>
                            <input type="hidden" name="nama" id="nama_pasien">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">No. Telepon / HP</label>
                            <input type="text" name="telp" class="form-control form-control-sm" placeholder="08xxxxxxxx">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control form-control-sm" rows="1" placeholder="Alamat lengkap..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RESEP --}}
        <div class="col-lg-4">
            <div class="glass-card h-100">
                <div class="card-body p-4">
                    <h6 class="card-header-section mb-3"><i class="bi bi-file-earmark-medical"></i> Sumber Resep</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Penulis Resep <span class="badge-req">Wajib</span></label>
                            <div class="position-relative">
                                <input type="text" name="nama_dokter" id="nama_dokter" class="form-control form-control-sm" placeholder="Nama Dokter / Optik asal..." autocomplete="off">
                                <div id="dd_dokter" class="ac-dropdown d-none"></div>
                                <input type="hidden" name="doctor_id" id="doctor_id">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tgl Resep</label>
                            <input type="date" name="tgl_resep" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan Resep <span class="badge-opt">Opsional</span></label>
                            <textarea name="catatan_resep" class="form-control form-control-sm" rows="3" placeholder="Contoh: Kacamata progresif, lensa photocromic..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- REFRAKSI --}}
        <div class="col-lg-8">
            <div class="glass-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-header-section mb-0"><i class="bi bi-eye"></i> Data Refraksi</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" id="btn-load-history" style="display: none;" onclick="loadPatientHistory()">
                            <i class="bi bi-clock-history"></i> Load Histori
                        </button>
                    </div>

                    <div id="history-tag-container" class="mb-2 d-none">
                        <span class="history-tag"><i class="bi bi-info-circle"></i> Menampilkan data pemeriksaan terakhir</span>
                    </div>

                    <div class="refraction-grid shadow-sm">
                        <table class="table table-sm table-borderless align-middle">
                            <thead>
                                <tr>
                                    <th width="80">Mata</th>
                                    <th>SPH</th>
                                    <th>CYL</th>
                                    <th>AXIS</th>
                                    <th>ADD</th>
                                    <th>MPD</th>
                                    <th>PRISM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>OD (R)</td>
                                    <td><input type="text" id="od_sph" placeholder="0.00"></td>
                                    <td><input type="text" id="od_cyl" placeholder="0.00"></td>
                                    <td><input type="text" id="od_axis" placeholder="0"></td>
                                    <td><input type="text" id="od_add" placeholder="0.00"></td>
                                    <td><input type="text" id="od_mpd" placeholder="0"></td>
                                    <td><input type="text" id="od_prism" placeholder="0"></td>
                                </tr>
                                <tr>
                                    <td>OS (L)</td>
                                    <td><input type="text" id="os_sph" placeholder="0.00"></td>
                                    <td><input type="text" id="os_cyl" placeholder="0.00"></td>
                                    <td><input type="text" id="os_axis" placeholder="0"></td>
                                    <td><input type="text" id="os_add" placeholder="0.00"></td>
                                    <td><input type="text" id="os_mpd" placeholder="0"></td>
                                    <td><input type="text" id="os_prism" placeholder="0"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3 p-3 bg-light rounded-3 border">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Lensa (Snapshot)</label>
                                <input type="text" name="lensa" class="form-control form-control-sm" placeholder="Contoh: Essilor 1.56 Anti Radiasi">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Keterangan Frame (Snapshot)</label>
                                <input type="text" name="keterangan_frame" class="form-control form-control-sm" placeholder="Warna, Ukuran, atau Catatan Frame">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wizard-nav-bar mt-4">
        <button type="button" class="btn btn-action btn-light border shadow-sm" onclick="goStep(1)">
            <i class="bi bi-arrow-left"></i> Kembali h
        </button>
        <button type="button" class="btn btn-action btn-primary shadow-sm px-4" onclick="goNextStep(2)">
            Lanjut ke Produk <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</div>

<div id="patient-history-section" class="mt-3 d-none">
    <div class="glass-card">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="fw-bold text-muted text-uppercase small">Histori Pemeriksaan</small>
                <button type="button" class="btn-close btn-sm" onclick="hidePatientHistory()"></button>
            </div>
            <div id="history-content-list" class="small"></div>
        </div>
    </div>
</div>
