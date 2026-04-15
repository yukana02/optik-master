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
                                <span class="badge bg-success-subtle text-success border border-success-subtle small py-1 px-2">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    <span id="patient-selected-name">—</span>
                                </span>
                            </div>
                        </div>
                        <div class="col-12" id="no_bpjs_container" style="display: none;">
                            <label class="form-label">No BPJS</label>
                            <input type="text" name="no_bpjs" id="no_bpjs" class="form-control form-control-sm" placeholder="000...">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama_pasien" class="form-control form-control-sm" placeholder="Nama pasien (kosong = UMUM)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="telp" class="form-control form-control-sm" placeholder="08xx-xxxx-xxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Asal Resep / Dokter</label>
                            <input type="text" name="asal_resep" class="form-control form-control-sm" placeholder="dr. Nama / Klinik...">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control form-control-sm" rows="2" placeholder="Alamat pasien..."></textarea>
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
                        <div class="col-md-6">
                            <label class="form-label">Nama Dokter / Klinik <span class="text-danger">*</span></label>
                            <input type="text" name="nama_dokter" id="nama_dokter"
                                class="form-control form-control-sm" placeholder="dr. Nama Dokter..." value="{{ Auth::user()->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Resep</label>
                            <input type="date" name="tgl_resep" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan Resep</label>
                            <textarea name="catatan_resep" class="form-control form-control-sm" rows="2" placeholder="Instruksi / catatan resep..."></textarea>
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
                        <span class="history-tag"><i class="bi bi-info-circle me-1"></i>Data dari histori — bisa diubah langsung</span>
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
                                    <td><input type="text" name="od_sph"  id="od_sph"  placeholder="0.00" inputmode="decimal"></td>
                                    <td><input type="text" name="od_cyl"  id="od_cyl"  placeholder="0.00" inputmode="decimal"></td>
                                    <td><input type="text" name="od_axis" id="od_axis" placeholder="0"    inputmode="numeric"></td>
                                    <td><input type="text" name="od_add"  id="od_add"  placeholder="0.00" inputmode="decimal"></td>
                                    <td><input type="text" name="od_mpd"  id="od_mpd"  placeholder="0.0"  inputmode="decimal"></td>
                                    <td><input type="text" name="od_prism" id="od_prism" placeholder="-"    inputmode="decimal"></td>
                                </tr>
                                <tr>
                                    <td class="text-danger fw-bold">OS</td>
                                    <td><input type="text" name="os_sph"  id="os_sph"  placeholder="0.00" inputmode="decimal"></td>
                                    <td><input type="text" name="os_cyl"  id="os_cyl"  placeholder="0.00" inputmode="decimal"></td>
                                    <td><input type="text" name="os_axis" id="os_axis" placeholder="0"    inputmode="numeric"></td>
                                    <td><input type="text" name="os_add"  id="os_add"  placeholder="0.00" inputmode="decimal"></td>
                                    <td><input type="text" name="os_mpd"  id="os_mpd"  placeholder="0.0"  inputmode="decimal"></td>
                                    <td><input type="text" name="os_prism" id="os_prism" placeholder="-"    inputmode="decimal"></td>
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
                                <input type="text" name="lensa" id="lensa_ket" class="form-control form-control-sm" placeholder="Jenis lensa...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Keterangan Ukuran</label>
                                <input type="text" name="keterangan_frame" class="form-control form-control-sm" placeholder="Catatan ukuran...">
                            </div>
                        </div>
                    </div>

                    {{-- Shortcut refraksi common values --}}
                    <div class="mt-3 pt-2 border-top">
                        <div class="form-label mb-1">Shortcut isi OD = OS:</div>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-action" onclick="copyOdToOs()">
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
            <span class="step-hint">Resep (dokter) &amp; refraksi (min. SPH salah satu mata) wajib diisi</span>
            <button type="button" class="btn btn-action btn-primary shadow-sm px-4" onclick="goNextStep(2)">
                Lanjut ke Produk <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</div>
