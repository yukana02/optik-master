<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    /* ==========================================================
       HELPERS & STATE
    ========================================================== */
    let currentStep = 1;
    let cart = [];
    let lastAutoHarga = 0;
    const TOTAL_STEPS = 4;
    const STEP_LABELS = { 1:'Step 1: Transaksi', 2:'Step 2: Pasien & Resep', 3:'Step 3: Produk', 4:'Step 4: Checkout' };

    function parseAngka(val) { return !val ? 0 : parseInt(String(val).replace(/\./g, '').replace(',', '')) || 0; }
    function formatRibuan(val) { return new Intl.NumberFormat('id-ID').format(val); }
    function toast(icon, title, text = '') { Swal.fire({ icon, title, text, timer: 1400, showConfirmButton: false }); }
    function snackbar(msg, type = 'success') {
        Swal.fire({ icon: type, title: msg, toast: true, position: 'bottom-end', showConfirmButton: false, timer: 2200, timerProgressBar: true });
    }

    /* ==========================================================
       WIZARD NAVIGATION
    ========================================================== */
    function updateStepUI(step) {
        for (let i = 1; i <= TOTAL_STEPS; i++) {
            const panel = document.getElementById('panel-step-' + i);
            const tab = document.getElementById('step-tab-' + i);
            if (panel) panel.classList.toggle('is-active', i === step);
            if (tab) {
                tab.classList.remove('is-active', 'is-done');
                if (i === step) tab.classList.add('is-active');
                else if (i < step) tab.classList.add('is-done');
            }
            const conn = document.getElementById('conn-' + i);
            if (conn) conn.classList.toggle('is-done', i < step);
        }
        document.getElementById('display-step-label').textContent = STEP_LABELS[step];
        const noTrx = document.querySelector('input[name="no_transaksi"]').value || '—';
        document.getElementById('display-no-trx').textContent = noTrx;

        const focuses = { 1: '#no_transaksi', 2: '#ac_pasien', 3: '#new_item_name', 4: '#input_harga_jual' };
        const focusEl = document.querySelector(focuses[step]);
        if (focusEl) setTimeout(() => focusEl.focus(), 180);
        currentStep = step;
        if (step === 4) buildCheckoutSummary();
    }

    function goStep(n) { updateStepUI(n); window.scrollTo({ top: 0, behavior: 'smooth' }); }
    function tryGoStep(n) {
        if (n < currentStep) { goStep(n); return; }
        if (n === currentStep) return;
        let ok = true;
        for (let i = currentStep; i < n; i++) if (!validateStep(i)) { ok = false; break; }
        if (ok) goStep(n);
    }
    function goNextStep(fromStep) { if (validateStep(fromStep)) goStep(fromStep + 1); }

    /* ==========================================================
       VALIDATION ENGINE
    ========================================================== */
    function validateStep(step) {
        hideStepError(step);
        if (step === 1) {
            const noTrx = document.querySelector('input[name="no_transaksi"]').value.trim();
            const tgl = document.getElementById('tgl_faktur')?.value;
            const tipe = document.querySelector('input[name="typefaktur"]:checked')?.value;
            const noBpjs = document.getElementById('no_bpjs')?.value.trim();

            if (!noTrx) { showStepError(1, 'No Faktur tidak boleh kosong.'); return false; }
            if (!tgl) { showStepError(1, 'Tanggal faktur tidak boleh kosong.'); return false; }
            if (tipe === '2' && !noBpjs) {
                showStepError(1, 'No. BPJS wajib diisi untuk transaksi tipe BPJS.');
                document.getElementById('no_bpjs').focus();
                return false;
            }
            return true;
        }
        if (step === 2) {
            const dokter = document.getElementById('nama_dokter').value.trim();
            if (!dokter) { showStepError(2, 'Nama penulis resep wajib diisi.'); return false; }
            return true;
        }
        if (step === 3) {
            if (cart.length === 0) { showStepError(3, 'Tambahkan minimal 1 produk.'); return false; }
            return true;
        }
        return true;
    }

    function showStepError(step, msg) {
        const el = document.getElementById('err-step-' + step);
        const msgEl = document.getElementById('err-step-' + step + '-msg');
        if (el && msgEl) { msgEl.textContent = msg; el.classList.add('show'); }
    }
    function hideStepError(step) {
        const el = document.getElementById('err-step-' + step);
        if (el) el.classList.remove('show');
    }

    /* ==========================================================
       TIPE TRANSAKSI (BPJS vs UMUM)
    ========================================================== */
    function updateTipeTransaksiUI(isManualSwitch = false) {
        const tipe = document.querySelector('input[name="typefaktur"]:checked')?.value;
        const containerBpjs = document.getElementById('bpjs-input-container');
        const layoutUmum = document.getElementById('layout-umum');
        const layoutBpjs = document.getElementById('layout-bpjs');
        const btnPrintBpjs = document.getElementById('btn-print-bpjs');

        if (isManualSwitch && cart.length > 0) {
            Swal.fire({
                title: 'Ganti Tipe?', text: 'Keranjang akan dikosongkan. Lanjutkan?',
                icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya', cancelButtonText: 'Batal'
            }).then(r => {
                if (r.isConfirmed) { cart = []; processCart(); applyUI(tipe, containerBpjs, layoutUmum, layoutBpjs, btnPrintBpjs); }
                else { document.querySelector(`input[name="typefaktur"][value="${tipe==='2'?'1':'2'}"]`).checked = true; }
            });
            return;
        }
        applyUI(tipe, containerBpjs, layoutUmum, layoutBpjs, btnPrintBpjs);
    }

    function applyUI(tipe, containerBpjs, layoutUmum, layoutBpjs, btnPrintBpjs) {
        if (tipe === '2') {
            containerBpjs?.classList.remove('d-none');
            layoutUmum?.classList.add('d-none');
            layoutBpjs?.classList.remove('d-none');
            if (btnPrintBpjs) { btnPrintBpjs.style.display = 'inline-flex'; btnPrintBpjs.disabled = false; }
        } else {
            containerBpjs?.classList.add('d-none');
            layoutUmum?.classList.remove('d-none');
            layoutBpjs?.classList.add('d-none');
            if (btnPrintBpjs) { btnPrintBpjs.style.display = 'none'; btnPrintBpjs.disabled = true; }
        }
        syncBPJSUI();
    }

    /* ==========================================================
       CART & BPJS LOGIC
    ========================================================== */
    function addItemToCart() {
        const id = document.getElementById('new_item_id').value;
        const nama = document.getElementById('new_item_name').value;
        const harga = parseAngka(document.getElementById('new_item_harga').value);
        const qty = parseInt(document.getElementById('new_item_qty').value) || 1;
        if (!nama) { showStepError(3, 'Nama produk wajib diisi.'); return; }
        
        cart.push({
            product_id: id,
            nama: nama,
            seri: document.getElementById('new_item_seri').value,
            warna: document.getElementById('new_item_warna').value,
            harga: harga,
            qty: qty,
            type: document.getElementById('btn-type-frame').classList.contains('active-frame') ? 'Frame' : 
                  document.getElementById('btn-type-lensa').classList.contains('active-lensa') ? 'Lensa' : 'Lainnya',
            keterangan: document.getElementById('new_item_keterangan').value
        });
        
        document.getElementById('new_item_name').value = '';
        document.getElementById('new_item_harga').value = '0';
        document.getElementById('ac_produk_new').value = '';
        processCart();
        toast('success', 'Produk ditambahkan');
    }

    function setItemBPJS(type) {
        const nama = document.getElementById(`bpjs_${type.toLowerCase()}_nama`).value;
        const harga = parseAngka(document.getElementById(`bpjs_${type.toLowerCase()}_harga`).value);
        if (!nama || harga === 0) return;
        
        cart = cart.filter(i => i.type !== type);
        cart.push({ type, nama, harga, qty: 1, seri: '', warna: '', keterangan: '' });
        processCart();
    }

    function syncBPJSUI() {
        const frame = cart.find(i => i.type === 'Frame');
        const lensa = cart.find(i => i.type === 'Lensa');
        document.getElementById('bpjs-frame-selected-badge')?.classList.toggle('d-none', !frame);
        document.getElementById('bpjs-lensa-selected-badge')?.classList.toggle('d-none', !lensa);
    }

    function processCart() {
        renderCartInline();
        const total = cart.reduce((s, i) => s + (i.harga * i.qty), 0);
        const inputHarga = document.getElementById('input_harga_jual');
        if (parseAngka(inputHarga.value) === 0 || parseAngka(inputHarga.value) === lastAutoHarga) {
            inputHarga.value = formatRibuan(total);
            lastAutoHarga = total;
        }
        calculateSisa();
        syncBPJSUI();
    }

    function renderCartInline() {
        const body = document.getElementById('cart-inline-body');
        const badge = document.getElementById('cart-count-badge');
        badge.textContent = cart.length;
        if (cart.length === 0) {
            body.innerHTML = '<div class="cart-empty text-center py-5">Keranjang kosong</div>';
            document.getElementById('cart-total-section').style.display = 'none';
            return;
        }
        document.getElementById('cart-total-section').style.display = 'block';
        body.innerHTML = cart.map((item, idx) => `
            <div class="cart-item-row">
                <span class="cart-type-badge ct-${item.type.toLowerCase()}">${item.type}</span>
                <span class="cart-item-name">${item.nama}</span>
                <span class="fw-bold fs-7">Rp ${formatRibuan(item.harga)}</span>
                <i class="bi bi-trash text-danger" onclick="removeCartItem(${idx})"></i>
            </div>
        `).join('');
        const total = cart.reduce((s, i) => s + (i.harga * i.qty), 0);
        document.getElementById('cart-inline-total').textContent = 'Rp ' + formatRibuan(total);
    }

    function removeCartItem(idx) { cart.splice(idx, 1); processCart(); }

    function calculateSisa() {
        const harga = parseAngka(document.getElementById('input_harga_jual').value);
        const pot = parseAngka(document.getElementById('input_potongan').value);
        const dp = parseAngka(document.getElementById('input_dp').value);
        const sisa = harga - pot - dp;
        document.getElementById('input_sisa').value = formatRibuan(sisa);
        document.getElementById('summary-total').textContent = 'Rp ' + formatRibuan(harga - pot);
    }

    /* ==========================================================
       AUTOCOMPLETE ENGINE
    ========================================================== */
    function setupAC(inpEl, ddEl, url, mapFn, onSelect) {
        if (!inpEl || !ddEl) return;
        let T;
        inpEl.addEventListener('input', function() {
            clearTimeout(T);
            const q = this.value;
            if (q.length < 1) { ddEl.classList.add('d-none'); return; }
            T = setTimeout(() => {
                fetch(`${url}?q=${q}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.length === 0) { ddEl.classList.add('d-none'); return; }
                        ddEl.innerHTML = data.map(i => `<div class="ac-item" data-i='${JSON.stringify(i).replace(/'/g, "&apos;")}'>${mapFn(i)}</div>`).join('');
                        ddEl.classList.remove('d-none');
                        ddEl.querySelectorAll('.ac-item').forEach(el => {
                            el.onclick = () => {
                                const item = JSON.parse(el.dataset.i);
                                onSelect(item);
                                ddEl.classList.add('d-none');
                            };
                        });
                    });
            }, 300);
        });
        document.addEventListener('click', (e) => { if (!inpEl.contains(e.target)) ddEl.classList.add('d-none'); });
    }

    function loadPatientHistory() {
        const id = document.getElementById('patient_id').value;
        if (!id) return;
        fetch(`/admin/patients/${id}/history`)
            .then(r => r.json())
            .then(data => {
                const list = document.getElementById('history-content-list');
                const tag = document.getElementById('history-tag-container');
                if (data.length > 0) {
                    const last = data[0];
                    ['sph', 'cyl', 'axis', 'add', 'mpd', 'prism'].forEach(k => {
                        document.getElementById('od_' + k).value = last['od_' + k] || '';
                        document.getElementById('os_' + k).value = last['os_' + k] || '';
                    });
                    tag.classList.remove('d-none');
                    snackbar('Data refraksi dimuat', 'info');
                } else {
                    snackbar('Tidak ada riwayat', 'warning');
                }
            });
    }

    function openSearchModal() {
        const modal = new bootstrap.Modal(document.getElementById('searchModal'));
        modal.show();
        searchTrx('');
    }

    function searchTrx(q) {
        fetch(`/admin/transactions/search?q=${q}`)
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('#searchTable tbody');
                tbody.innerHTML = data.map(t => `
                    <tr>
                        <td class="small">${t.tgl_faktur}</td>
                        <td class="fw-bold text-primary">${t.no_transaksi}</td>
                        <td>${t.nama_pasien}</td>
                        <td class="text-end fw-bold">Rp ${formatRibuan(t.total_biaya)}</td>
                        <td><button type="button" class="btn btn-sm btn-light" onclick="loadTransaction(${t.id})">Pilih</button></td>
                    </tr>
                `).join('');
            });
    }

    function loadTransaction(id) {
        location.href = `/admin/transactions/${id}/edit`;
    }

    function resetWizard() {
        Swal.fire({
            title: 'Reset Form?', text: 'Seluruh data yang diinput akan hilang.',
            icon: 'warning', showCancelButton: true, confirmButtonText: 'Reset'
        }).then(r => { if(r.isConfirmed) location.reload(); });
    }

    function doPrint(type) {
        const id = document.getElementById('trx_id').value;
        if(!id) return snackbar('Simpan transaksi dahulu', 'error');
        const frame = document.getElementById('printFrame');
        frame.src = `/admin/transactions/${id}/print?type=${type}`;
        frame.onload = () => { frame.contentWindow.print(); };
    }

    /* ==========================================================
       INIT & EVENTS
    ========================================================== */
    document.addEventListener('DOMContentLoaded', () => {
        updateStepUI(1);
        updateTipeTransaksiUI();

        // Autocomplete Pasien
        setupAC(document.getElementById('ac_pasien'), document.getElementById('dd_pasien'), '/admin/patients/autocomplete', i => `${i.nama} (${i.no_rm})`, i => {
            document.getElementById('nama_pasien').value = i.nama;
            document.getElementById('ac_pasien').value = i.nama;
            document.getElementById('patient_id').value = i.id;
            document.querySelector('[name="telp"]').value = i.telp || '';
            document.querySelector('[name="alamat"]').value = i.alamat || '';
            document.getElementById('btn-load-history').style.display = 'block';
            document.getElementById('patient-selected-badge').classList.remove('d-none');
            document.getElementById('patient-selected-name').textContent = i.nama;
        });

        // Autocomplete Dokter
        setupAC(document.getElementById('nama_dokter'), document.getElementById('dd_dokter'), '/admin/doctors/autocomplete', i => i.nama, i => {
            document.getElementById('nama_dokter').value = i.nama;
            document.getElementById('doctor_id').value = i.id;
        });

        // Autocomplete Produk (Standard)
        setupAC(document.getElementById('ac_produk_new'), document.getElementById('dd_produk_new'), '/admin/products/autocomplete', i => `${i.kode} - ${i.nama}`, i => {
            document.getElementById('new_item_id').value = i.id;
            document.getElementById('new_item_name').value = i.nama;
            document.getElementById('new_item_harga').value = formatRibuan(i.harga_jual);
            document.getElementById('new_item_seri').value = i.seri || '';
            document.getElementById('new_item_warna').value = i.warna || '';
        });

        // Autocomplete BPJS Frame
        setupAC(document.getElementById('ac_produk_bpjs_frame'), document.getElementById('dd_produk_bpjs_frame'), '/admin/products/autocomplete/frame', i => i.nama, i => {
            document.getElementById('bpjs_frame_nama').value = i.nama;
            document.getElementById('bpjs_frame_seri').value = i.seri || '';
            document.getElementById('bpjs_frame_harga').value = formatRibuan(i.harga_jual);
            setItemBPJS('Frame');
        });

        // Autocomplete BPJS Lensa
        setupAC(document.getElementById('ac_produk_bpjs_lensa'), document.getElementById('dd_produk_bpjs_lensa'), '/admin/products/autocomplete/lensa', i => i.nama, i => {
            document.getElementById('bpjs_lensa_nama').value = i.nama;
            document.getElementById('bpjs_lensa_harga').value = formatRibuan(i.harga_jual);
            setItemBPJS('Lensa');
        });

        document.querySelectorAll('input[name="typefaktur"]').forEach(el => el.onchange = () => updateTipeTransaksiUI(true));
        ['input_harga_jual', 'input_potongan', 'input_dp'].forEach(id => {
            const el = document.getElementById(id);
            if(el) el.onkeyup = () => { el.value = formatRibuan(parseAngka(el.value)); calculateSisa(); };
        });

        document.getElementById('modalSearchInput').onkeyup = (e) => searchTrx(e.target.value);
    });

    function buildCheckoutSummary() {
        const container = document.getElementById('summary-items');
        container.innerHTML = cart.map(i => `<div>• ${i.nama} (x${i.qty})</div>`).join('');
    }

    /* ==========================================================
       FORM SUBMISSION
    ========================================================== */
    document.getElementById('pos-form').onsubmit = function(e) {
        e.preventDefault();
        const data = {
            patient: { nama: document.getElementById('nama_pasien').value, no_bpjs: document.getElementById('no_bpjs').value },
            items: cart,
            pembayaran: { harga_jual: parseAngka(document.getElementById('input_harga_jual').value), dp: parseAngka(document.getElementById('input_dp').value) }
        };
        document.getElementById('transaction_data').value = JSON.stringify(data);
        
        const btn = document.getElementById('btn-simpan');
        btn.disabled = true; btn.textContent = 'Menyimpan...';
        
        fetch(this.action, {
            method: 'POST', body: new FormData(this), headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r=>r.json()).then(res => {
            if(res.status==='success') Swal.fire('Sukses', res.message, 'success').then(()=>location.reload());
            else { btn.disabled = false; btn.textContent = 'Simpan'; toast('error', 'Gagal', res.message); }
        });
    };
</script>
