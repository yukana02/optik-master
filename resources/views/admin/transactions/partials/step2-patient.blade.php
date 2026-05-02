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
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2">
                                <div id="patient-selected-badge" class="d-none">
                                    <span class="badge bg-info-subtle text-info py-1">
                                        <i class="bi bi-person-check me-1"></i>
                                        <span id="patient-selected-name"></span>
                                    </span>
                                </div>
                                <button type="button" id="btn-load-history" class="btn btn-sm btn-outline-secondary d-none"
                                    onclick="loadPatientHistory()">
                                    <i class="bi bi-clock-history me-1"></i>Lihat Riwayat
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6" id="nik_container">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" id="nik" class="form-control form-control-sm" placeholder="000...">
                        </div>
                        <div class="col-md-6" id="no_bpjs_container">
                            <label class="form-label">No BPJS</label>
                            <input type="text" name="no_bpjs" id="no_bpjs" class="form-control form-control-sm" placeholder="000...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Potongan BPJS</label>
                            <select name="potongan_bpjs" id="potongan_bpjs" class="form-select">
                                <option value="0">-- Pilih --</option>
                                <option value="330.000">Kelas 1 - Rp 330.000</option>
                                <option value="220.000">Kelas 2 - Rp 220.000</option>
                                <option value="165.000">Kelas 3 - Rp 165.000</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <input type="hidden" name="nama" id="nama_pasien" class="form-control form-control-sm" placeholder="Nama pasien (kosong = UMUM)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="telp" class="form-control form-control-sm" placeholder="08xx-xxxx-xxxx">
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
                        
                    </div>

                    <div id="history-tag-container" class="mb-2 d-none">
                        <span class="history-tag"><i class="bi bi-info-circle me-1"></i>Data dari histori — bisa diubah langsung</span>
                    </div>

                    <div id="patient-history-section" class="mb-3 d-none">
                        <div class="glass-card">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <div>
                                        <h6 class="card-header-section mb-0">Riwayat Pemeriksaan</h6>
                                        <small class="text-muted">Tersedia <span id="history-count">0</span> catatan terakhir.</small>
                                    </div>
                                </div>
                                <div id="patient-history-list" style="max-height: 300px; overflow-y: auto;"></div>
                            </div>
                        </div>
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
                                    <th>Visus</th>
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
                                    <td><input type="text" name="od_vis"  id="od_vis"  placeholder="6/6" inputmode="text"></td>
                                </tr>
                                <tr>
                                    <td class="text-danger fw-bold">OS</td>
                                    <td><input type="text" name="os_sph"  id="os_sph"  placeholder="0.00" inputmode="decimal"></td>
                                    <td><input type="text" name="os_cyl"  id="os_cyl"  placeholder="0.00" inputmode="decimal"></td>
                                    <td><input type="text" name="os_axis" id="os_axis" placeholder="0"    inputmode="numeric"></td>
                                    <td><input type="text" name="os_add"  id="os_add"  placeholder="0.00" inputmode="decimal"></td>
                                    <td><input type="text" name="os_mpd"  id="os_mpd"  placeholder="0.0"  inputmode="decimal"></td>
                                    <td><input type="text" name="os_vis"  id="os_vis"  placeholder="6/6" inputmode="text"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 pt-3 border-top">
                        <div class="row g-2">
                            {{-- diagnosis --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Diagnosis</label>
                                <select name="diagnosis" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="Myopia Astigmatism" {{ old('diagnosis') == 'Myopia Astigmatism' ? 'selected' : '' }}>
                                        Myopia Astigmatism</option>
                                    <option value="Myopia + Presbyopia" {{ old('diagnosis') == 'Myopia + Presbyopia' ? 'selected' : '' }}>Myopia + Presbyopia
                                    </option>
                                    <option value="Myopia Astigmatism + Presbyopia" {{ old('diagnosis') == 'Myopia Astigmatism + Presbyopia' ? 'selected' : '' }}>
                                        Myopia Astigmatism + Presbyopia</option>
                                    <option value="Hypermetropia + Astigmatism" {{ old('diagnosis') == 'Hypermetropia + Astigmatism' ? 'selected' : '' }}>
                                        Hypermetropia + Astigmatism</option>
                                    <option value="Hypermetropia + Presbyopia" {{ old('diagnosis') == 'Hypermetropia + Presbyopia' ? 'selected' : '' }}>
                                        Hypermetropia + Presbyopia</option>
                                    <option value="Hypermetropia Astigmatism + Presbyopia" {{ old('diagnosis') == 'Hypermetropia Astigmatism + Presbyopia' ? 'selected' : '' }}>
                                        Hypermetropia Astigmatism + Presbyopia</option>
                                    <option value="Astigmatism + Presbyopia" {{ old('diagnosis') == 'Astigmatism + Presbyopia' ? 'selected' : '' }}>
                                        Astigmatism + Presbyopia</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">PD Total / Binokular</label>
                                <input type="number" name="pd_total" class="form-control" min="40" max="80"
                                    value="{{ old('pd_total') }}" placeholder="60">
                            </div>

                            <div class="col-md-6">
                                {{-- <label class="form-label">Lensa (keterangan)</label> --}}
                                <input type="hidden" name="lensa" id="lensa_ket" class="form-control form-control-sm" placeholder="Jenis lensa...">
                            </div>
                            <div class="col-md-6">
                                {{-- <label class="form-label">Keterangan Ukuran</label> --}}
                                <input type="hidden" name="keterangan_frame" class="form-control form-control-sm" placeholder="Catatan ukuran...">
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
