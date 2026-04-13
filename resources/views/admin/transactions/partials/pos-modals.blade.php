<!-- Search Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;backdrop-filter:blur(16px);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title card-header-section mb-0"><i class="bi bi-search"></i> Cari Riwayat Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="modalSearchInput" class="form-control form-control-lg mb-3 shadow-sm rounded-pill" placeholder="Ketik No Transaksi, Nama, atau BPJS..." autocomplete="off">
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
                <div class="d-flex justify-content-center mt-3" id="searchPagination"></div>
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
                    <button class="btn btn-outline-primary btn-action justify-content-center" onclick="doPrint('pesanan_besar')"><i class="bi bi-file-earmark-text"></i> Cetak Bon Pesanan Besar</button>
                    <button class="btn btn-outline-primary btn-action justify-content-center" onclick="doPrint('bon_3_rangkap')"><i class="bi bi-file-earmark-ruled"></i> Cetak Bon (3 Rangkap)</button>
                    <button class="btn btn-outline-primary btn-action justify-content-center" onclick="doPrint('fasetan')"><i class="bi bi-file-earmark-medical"></i> Cetak Bon Fasetan</button>
                    <button class="btn btn-outline-primary btn-action justify-content-center" onclick="doPrint('garansi')"><i class="bi bi-patch-check"></i> Cetak Kartu Garansi</button>
                    <button class="btn btn-outline-primary btn-action justify-content-center" onclick="doPrint('formulir_bpjs')" id="btn-print-bpjs"><i class="bi bi-file-earmark-medical"></i> Cetak Formulir BPJS</button>
                    <button class="btn btn-outline-secondary btn-action justify-content-center mt-1" onclick="doPrint('bon_1_rangkap')"><i class="bi bi-receipt"></i> Cetak Bon Standar</button>
                </div>
            </div>
        </div>
    </div>
</div>
