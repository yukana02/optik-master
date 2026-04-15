<div class="wizard-panel" id="panel-step-4">
    <div class="row g-3">
        {{-- KIRI: Pembayaran --}}
        <div class="col-lg-7">
            {{-- Summary --}}
            <div class="glass-card mb-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-header-section mb-0"><i class="bi bi-clipboard-check"></i> Ringkasan Transaksi</h6>
                        
                        {{-- BPJS Form Print Button (Hidden default) --}}
                        <div id="btn-print-bpjs-container" class="d-none">
                            <button type="button" class="btn btn-sm btn-outline-info shadow-sm btn-action" onclick="printBpjsForm()">
                                <i class="bi bi-printer"></i> Cetak Form BPJS
                            </button>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-2" id="checkout-summary-badges">
                        {{-- diisi JS --}}
                    </div>
                    <div id="checkout-item-list" class="border rounded-3 p-2" style="max-height:140px;overflow-y:auto;background:#f8f9fa;">
                        {{-- diisi JS --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: Finance --}}
        <div class="col-lg-5">
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
    </div>

    <div class="wizard-nav-bar mt-4">
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
