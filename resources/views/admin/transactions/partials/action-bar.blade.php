<div class="global-action-bar">
    <div class="trx-info">
        <strong id="display-no-trx">—</strong>
        <span class="mx-2">|</span>
        <span id="display-step-label">Step 1: Transaksi</span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-action btn-secondary shadow-sm" onclick="openSearchModal()">
            <i class="bi bi-search"></i> Cari
        </button>
        <button type="button" class="btn btn-action btn-warning shadow-sm" onclick="resetWizard()">
            <i class="bi bi-arrow-clockwise"></i> Reset
        </button>
        <div class="btn-group shadow-sm">
            <button type="button" class="btn btn-action btn-dark border-0" onclick="navTransaction('prev')"><i class="bi bi-chevron-left"></i></button>
            <button type="button" class="btn btn-action btn-dark border-0" onclick="navTransaction('next')"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
</div>
