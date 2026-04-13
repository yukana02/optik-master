<div class="wizard-panel" id="panel-step-3">
    <div id="err-step-3" class="step-error-msg">
        <i class="bi bi-exclamation-triangle"></i>
        <span id="err-step-3-msg"></span>
    </div>

    <div class="row g-3">
        {{-- AREA INPUT PRODUK (DINAMIS) --}}
        <div class="col-lg-8">
            {{-- LAYOUT UMUM (Standard Cart) --}}
            <div id="layout-umum">
                <div class="glass-card mb-3">
                    <div class="card-body p-4">
                        <h6 class="card-header-section"><i class="bi bi-plus-circle"></i> Tambah Produk</h6>
                        <div class="product-type-tabs">
                            <span class="ptype-btn active-frame" id="btn-type-frame" onclick="setProductType('Frame')">Frame</span>
                            <span class="ptype-btn" id="btn-type-lensa" onclick="setProductType('Lensa')">Lensa</span>
                            <span class="ptype-btn" id="btn-type-other" onclick="setProductType('Lainnya')">Lainnya</span>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-5">
                                <label class="form-label">Cari Produk Database</label>
                                <div class="position-relative">
                                    <input type="text" id="ac_produk_new" class="form-control form-control-sm" placeholder="Ketik kode, nama, atau merek..." autocomplete="off">
                                    <div id="dd_produk_new" class="ac-dropdown d-none"></div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label">Nama Produk (Deskripsi)</label>
                                <input type="text" id="new_item_name" class="form-control form-control-sm" placeholder="Nama produk yang akan tampil di faktur">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Seri/Merek</label>
                                <input type="text" id="new_item_seri" class="form-control form-control-sm" placeholder="Contoh: Rayban">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Warna</label>
                                <input type="text" id="new_item_warna" class="form-control form-control-sm" placeholder="Contoh: Black Gold">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Harga @ Rp</label>
                                <input type="text" id="new_item_harga" class="form-control form-control-sm" value="0" onkeyup="this.value = formatRibuan(parseAngka(this.value))">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">QTY</label>
                                <input type="number" id="new_item_qty" class="form-control form-control-sm" value="1" min="1">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Keterangan Tambahan</label>
                                <textarea id="new_item_keterangan" class="form-control form-control-sm" rows="1" placeholder="Catatan kecil..."></textarea>
                            </div>
                            <input type="hidden" name="product_id[]" id="new_item_id">
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary btn-sm w-100 shadow-sm fw-bold" onclick="addItemToCart()">
                                <i class="bi bi-cart-plus"></i> Tambah ke Keranjang (F8)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LAYOUT BPJS (Split Card Input) --}}
            <div id="layout-bpjs" class="d-none">
                <div class="row g-3">
                    {{-- INPUT FRAME --}}
                    <div class="col-md-6">
                        <div class="glass-card h-100 border-primary" style="background: rgba(13, 110, 253, 0.03);">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-header-section mb-0 text-primary"><i class="bi bi-sunglasses"></i> Frame BPJS</h6>
                                    <span class="badge rounded-pill bg-primary d-none" id="bpjs-frame-selected-badge">Terpilih</span>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label">Cari Frame</label>
                                        <div class="position-relative">
                                            <input type="text" id="ac_produk_bpjs_frame" class="form-control form-control-sm" placeholder="Ketik kode/merek..." autocomplete="off">
                                            <div id="dd_produk_bpjs_frame" class="ac-dropdown d-none"></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Nama Frame</label>
                                        <input type="text" id="bpjs_frame_nama" class="form-control form-control-sm" placeholder="Nama frame...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Merek</label>
                                        <input type="text" id="bpjs_frame_seri" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Warna</label>
                                        <input type="text" id="bpjs_frame_warna" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Harga Frame</label>
                                        <input type="text" id="bpjs_frame_harga" class="form-control form-control-sm fw-bold text-primary" value="0" onkeyup="this.value = formatRibuan(parseAngka(this.value))" onchange="setItemBPJS('Frame')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- INPUT LENSA --}}
                    <div class="col-md-6">
                        <div class="glass-card h-100 border-success" style="background: rgba(25, 135, 84, 0.03);">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-header-section mb-0 text-success"><i class="bi bi-eye"></i> Lensa BPJS</h6>
                                    <span class="badge rounded-pill bg-success d-none" id="bpjs-lensa-selected-badge">Terpilih</span>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label">Cari Lensa</label>
                                        <div class="position-relative">
                                            <input type="text" id="ac_produk_bpjs_lensa" class="form-control form-control-sm" placeholder="Ketik jenis lensa..." autocomplete="off">
                                            <div id="dd_produk_bpjs_lensa" class="ac-dropdown d-none"></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Nama Lensa</label>
                                        <input type="text" id="bpjs_lensa_nama" class="form-control form-control-sm" placeholder="Deskripsi lensa...">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Keterangan</label>
                                        <input type="text" id="bpjs_lensa_ket" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Harga Lensa</label>
                                        <input type="text" id="bpjs_lensa_harga" class="form-control form-control-sm fw-bold text-success" value="0" onkeyup="this.value = formatRibuan(parseAngka(this.value))" onchange="setItemBPJS('Lensa')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KERANJANG BELANJA (INLINE) --}}
        <div class="col-lg-4">
            <div class="cart-panel p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-header-section mb-0 text-dark"><i class="bi bi-cart3"></i> Pesanan</h6>
                    <span class="badge rounded-pill bg-dark" id="cart-count-badge">0</span>
                </div>
                
                <div id="cart-inline-body" style="min-height: 200px;">
                    <!-- Cart items rendered via JS -->
                    <div class="cart-empty text-center py-5">
                        <i class="bi bi-cart-x d-block mb-2" style="font-size:2rem; opacity:0.2"></i>
                        <span class="text-muted small">Keranjang kosong</span>
                    </div>
                </div>

                <div id="cart-total-section" class="cart-total-section mt-3 pt-3 border-top" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold small">Subtotal</span>
                        <span class="fs-5 fw-bold text-primary" id="cart-inline-total">Rp 0</span>
                    </div>
                    <button type="button" class="btn btn-primary w-100 shadow-sm fw-bold rounded-pill" onclick="goNextStep(3)">
                        Lanjut Checkout <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="wizard-nav-bar mt-4">
        <button type="button" class="btn btn-action btn-light border shadow-sm" onclick="goStep(2)">
            <i class="bi bi-arrow-left"></i> Kembali ke Pasien
        </button>
        <span class="step-hint text-muted d-none d-md-inline">Sistem akan otomatis menghitung total belanja Anda.</span>
    </div>
</div>
