<div class="wizard-panel" id="panel-step-3">
    <div class="step-error-msg" id="err-step-3">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span id="err-step-3-msg">Tambahkan minimal 1 produk ke keranjang sebelum melanjutkan.</span>
    </div>

    {{-- CONTAINER UMUM --}}
    <div class="row g-3" id="umum-product-container">
        {{-- KIRI: Input Produk --}}
        <div class="col-lg-7">
            <div class="glass-card h-100">
                <div class="card-body p-4">
                    <h6 class="card-header-section">
                        <i class="bi bi-box-seam"></i> Tambah Produk (UMUM)
                    </h6>

                    

                    <input type="hidden" id="new_item_type" value="Frame">

                    <div id="product-entry">
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Cari Produk <span class="badge-opt ms-1">opsional</span></label>
                                <div class="input-group input-group-sm position-relative">
                                    <input type="text" id="ac_produk_new" class="form-control" autocomplete="off" placeholder="Ketik kode / nama produk...">
                                    <button class="btn btn-outline-secondary" type="button" onclick="clearProductSearch()">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <div id="dd_produk_new" class="ac-dropdown d-none"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" id="new_item_name" class="form-control form-control-sm" placeholder="Nama frame / lensa / item...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Seri / Merek</label>
                                <input type="text" id="new_item_seri" class="form-control form-control-sm" placeholder="Merek / seri...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Warna</label>
                                <input type="text" id="new_item_warna" class="form-control form-control-sm" placeholder="Warna...">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Harga Satuan</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="text" id="new_item_harga" class="form-control text-end" placeholder="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Qty</label>
                                <input type="number" id="new_item_qty" class="form-control form-control-sm text-center" value="1" min="1">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-success btn-action w-100" id="btn-add-item" onclick="addItemToCart()">
                                    <i class="bi bi-cart-plus"></i> Tambah
                                </button>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <input type="text" id="new_item_keterangan" class="form-control form-control-sm" placeholder="Keterangan tambahan...">
                            </div>
                        </div>
                    </div>

                    {{-- Hidden fields lama --}}
                    <div id="product-items" style="display:none">
                        <div class="product-item">
                            <input type="hidden" name="product_id[]">
                            <input type="text"   name="kode_frame[]"          value="">
                            <input type="text"   name="kode_produk_display[]" value="">
                            <input type="text"   name="nama_produk[]"          value="">
                            <input type="text"   name="seri[]"                value="">
                            <input type="text"   name="warna[]"               value="">
                            <input type="text"   name="keterangan[]"           value="">
                            <input type="text"   name="harga_satuan[]"         value="0" class="harga-satuan">
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
                    @if(isset($transaction) && count($transaction->items) > 0)
                        @foreach($transaction->items as $item)
                            <div class="cart-item-row">
                                <span class="cart-type-badge {{ $item->type === 'Frame' ? 'ct-frame' : ($item->type === 'Lensa' ? 'ct-lensa' : 'ct-other') }}">{{ $item->type ?? 'Product' }}</span>
                                <div class="cart-item-name-group flex-grow-1" style="min-width: 0;">
                                    <div class="cart-item-name fw-semibold" title="{{ $item->nama_produk }}">{{ $item->nama_produk }}</div>
                                    <div class="cart-item-details">{{ $item->seri ?? '' }} {{ $item->warna ? ($item->seri ? '| ' : '') . $item->warna : '' }}</div>
                                </div>
                                <input type="number" class="cart-item-qty" value="{{ $item->qty }}" min="1" readonly>
                                <span class="cart-item-price">Rp {{ number_format($item->harga_satuan * $item->qty, 0, ',', '.') }}</span>
                                <span class="cart-item-del">✕</span>
                            </div>
                        @endforeach
                    @else
                        <div class="cart-empty" id="cart-empty-msg">
                            <i class="bi bi-cart-x d-block mb-2" style="font-size:2rem"></i>
                            Keranjang kosong<br>
                            <small>Tambahkan minimal 1 item</small>
                        </div>
                    @endif
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

    {{-- CONTAINER BPJS --}}
    <div class="row g-3 d-none" id="bpjs-product-container">
        {{-- KIRI: FRAME --}}
        <div class="col-md-6">
            <div class="glass-card h-100" style="border-left: 4px solid #7c3aed;">
                <div class="card-body p-4">
                    <h6 class="card-header-section" style="color: #7c3aed;">
                        <i class="bi bi-eyeglasses"></i> Pilih Frame (Maks 1)
                    </h6>
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="input-group input-group-sm position-relative">
                                <input type="text" id="bpjs_frame_ac" class="form-control" autocomplete="off" placeholder="Cari Frame...">
                                <button class="btn btn-outline-secondary" type="button" onclick="clearBpjs('Frame')"><i class="bi bi-x"></i></button>
                                <div id="bpjs_frame_dd" class="ac-dropdown d-none"></div>
                            </div>
                        </div>
                        <input type="hidden" id="bpjs_frame_id">
                        <div class="col-12">
                            <label class="form-label">Nama Frame <span class="text-danger">*</span></label>
                            <input type="text" id="bpjs_frame_nama" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Harga Beli</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="text" id="bpjs_frame_harga" class="form-control text-end" value="0">
                            </div>
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="setBpjsItem('Frame')">Set Frame</button>
                        </div>
                    </div>
                    {{-- Status Indicator --}}
                    <div id="bpjs_frame_status" class="mt-3 p-2 rounded bg-light border d-none small text-center text-success fw-bold">
                        <i class="bi bi-check-circle-fill me-1"></i> Frame terpilih
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: LENSA --}}
        <div class="col-md-6">
            <div class="glass-card h-100" style="border-left: 4px solid #059669;">
                <div class="card-body p-4">
                    <h6 class="card-header-section" style="color: #059669;">
                        <i class="bi bi-circle"></i> Pilih Lensa (Maks 1)
                    </h6>
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="input-group input-group-sm position-relative">
                                <input type="text" id="bpjs_lensa_ac" class="form-control" autocomplete="off" placeholder="Cari Lensa...">
                                <button class="btn btn-outline-secondary" type="button" onclick="clearBpjs('Lensa')"><i class="bi bi-x"></i></button>
                                <div id="bpjs_lensa_dd" class="ac-dropdown d-none"></div>
                            </div>
                        </div>
                        <input type="hidden" id="bpjs_lensa_id">
                        <div class="col-12">
                            <label class="form-label">Nama Lensa <span class="text-danger">*</span></label>
                            <input type="text" id="bpjs_lensa_nama" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Harga Beli</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="text" id="bpjs_lensa_harga" class="form-control text-end" value="0">
                            </div>
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <button type="button" class="btn btn-success btn-sm w-100" onclick="setBpjsItem('Lensa')">Set Lensa</button>
                        </div>
                    </div>
                    {{-- Status Indicator --}}
                    <div id="bpjs_lensa_status" class="mt-3 p-2 rounded bg-light border d-none small text-center text-success fw-bold">
                        <i class="bi bi-check-circle-fill me-1"></i> Lensa terpilih
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <div class="finance-card">
            <h6 class="card-header-section text-white mb-3"><i class="bi bi-cash-stack"></i> Rincian Pembayaran</h6>

            <div class="summary-box" id="finance-cart-summary">
                {{-- diisi JS --}}
            </div>

            <div class="mb-3">
                <span class="finance-label">Harga Jual <small style="font-size:0.65rem;opacity:0.7">(bisa diubah)</small></span>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-0 text-white fw-bold">Rp</span>
                    <input type="text" name="harga_jual" id="input_harga_jual" class="form-control text-end fs-6" value="0">
                </div>
            </div>
            
            {{-- Potongan UMUM --}}
            <div class="mb-3" id="container_potongan">
                <span class="finance-label">Potongan Biasa / Diskon (−)</span>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-0 text-white fw-bold">Rp</span>
                    <input type="text" name="potongan" id="input_potongan" class="form-control text-end" value="0">
                </div>
            </div>

            {{-- Potongan BPJS --}}
            <div class="mb-3 d-none" id="container_potongan_bpjs">
                <span class="finance-label">Yang Diganti BPJS Kesehatan (−)</span>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-0 text-white fw-bold" style="color: #0dcaf0 !important;">Rp</span>
                    <input type="text" name="potongan_bpjs" id="input_potongan_bpjs" class="form-control text-end" style="border-color: #0dcaf0;" value="0">
                </div>
            </div>

            <div class="mb-3">
                <span class="finance-label">DP / Bayar Pertama <small id="lbl_selisih_bpjs" class="d-none text-info">(Selisih Yang Dibayar)</small></span>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-0 text-white fw-bold">Rp</span>
                    <input type="text" name="dp" id="input_dp" class="form-control text-end" value="0">
                </div>
            </div>
            <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                <span class="finance-label opacity-75 d-block mb-1">Sisa Tagihan</span>
                <div class="input-group input-group-lg">
                    <span class="input-group-text fw-bold border-0" style="background:rgba(220,53,69,0.3);color:#fff">Rp</span>
                    <input type="text" name="sisa" id="input_sisa" class="form-control text-end fw-bold sisa-field" readonly value="0">
                </div>
            </div>

            {{-- DP warning --}}
            <div id="dp-warning" class="mt-2 d-none" style="background:rgba(220,53,69,0.25);border:1px solid rgba(220,53,69,0.4);border-radius:8px;padding:8px 12px;font-size:0.75rem;color:#fff;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>DP tidak boleh melebihi harga jual!
            </div>
            {{-- Price below cart warning --}}
            <div id="price-low-warning" class="mt-2 d-none" style="background:rgba(255,193,7,0.25);border:1px solid rgba(255,193,7,0.4);border-radius:8px;padding:8px 12px;font-size:0.75rem;color:#fff;">
                <i class="bi bi-info-circle-fill me-1"></i>Harga jual di bawah total item keranjang.
            </div>
        </div>
    </div>


    <div class="wizard-nav-bar mt-4">
        <button type="button" class="btn btn-action btn-light border shadow-sm" onclick="goStep(2)">
            <i class="bi bi-arrow-left"></i> Kembali
        </button>
        <div class="nav-right">
            <span class="step-hint">Minimal 1 item di keranjang sebelum checkout</span>
            <button type="button" class="btn btn-action btn-primary shadow-sm px-4" onclick="goNextStep(3)">
                Lanjut ke Data Tambahan <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</div>
