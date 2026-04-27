<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/* ==========================================================
   HELPERS
========================================================== */
function parseAngka(val) {
    if (!val) return 0;
    return parseInt(String(val).replace(/\./g, '').replace(',', '')) || 0;
}
function formatRibuan(val) {
    return new Intl.NumberFormat('id-ID').format(val);
}
function formatDateInput(value) {
    if (!value) return '—';
    const [year, month, day] = value.split('-');
    return day && month && year ? `${day}/${month}/${year}` : value;
}
function formatFieldText(value, fallback = '—') {
    const text = String(value || '').trim();
    return text === '' ? fallback : text;
}
function toast(icon, title, text = '') {
    Swal.fire({ icon, title, text, timer: 1400, showConfirmButton: false, toast: false });
}
function snackbar(msg, type = 'success') {
    Swal.fire({
        icon: type, title: msg, toast: true,
        position: 'bottom-end', showConfirmButton: false, timer: 2200,
        timerProgressBar: true
    });
}

/* ==========================================================
   WIZARD STATE
========================================================== */
let currentStep = 1;
const TOTAL_STEPS = 5;

const STEP_LABELS = {
    1: 'Step 1: Transaksi',
    2: 'Step 2: Pasien & Resep',
    3: 'Step 3: Produk',
    4: 'Step 4: Data',
    5: 'Step 5: Checkout',
};

function updateStepUI(step) {
    for (let i = 1; i <= TOTAL_STEPS; i++) {
        document.getElementById('panel-step-' + i).classList.remove('is-active');
    }
    document.getElementById('panel-step-' + step).classList.add('is-active');

    for (let i = 1; i <= TOTAL_STEPS; i++) {
        const tab = document.getElementById('step-tab-' + i);
        tab.classList.remove('is-active', 'is-done');
        if (i === step) tab.classList.add('is-active');
        else if (i < step) tab.classList.add('is-done');
    }

    for (let i = 1; i < TOTAL_STEPS; i++) {
        const conn = document.getElementById('conn-' + i);
        if (conn) conn.classList.toggle('is-done', i < step);
    }

    document.getElementById('display-step-label').textContent = STEP_LABELS[step];
    document.getElementById('display-no-trx').textContent = 
        document.querySelector('input[name="no_transaksi"]').value || '—';

    const focuses = {
        1: '#no_transaksi',
        2: '#ac_pasien',
        3: '#new_item_name',
        5: '#input_harga_jual',
    };
    const focusEl = document.querySelector(focuses[step]);
    if (focusEl) setTimeout(() => focusEl.focus(), 180);

    currentStep = step;

    if (step === 5) buildCheckoutSummary();
}

function goStep(n) {
    updateStepUI(n);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function tryGoStep(n) {
    if (n < currentStep) { goStep(n); return; }
    if (n === currentStep) return;
    let ok = true;
    for (let i = currentStep; i < n; i++) {
        if (!validateStep(i)) { ok = false; break; }
    }
    if (ok) goStep(n);
}

function goNextStep(fromStep) {
    if (!validateStep(fromStep)) return;
    
    // Auto add BPJS items if moving from Step 3 to 5 and cart is empty
    if (fromStep === 3) {
        if (document.getElementById('bpjs').checked && cart.length === 0) {
            // Cek apakah ada frame/lensa terpilih di ui bpjs
            const frameId = document.getElementById('bpjs_frame_id').value;
            const lensaId = document.getElementById('bpjs_lensa_id').value;
            if (frameId || lensaId) {
                // Not strictly added to cart array, but let's push them to cart so summary works
                if (frameId) {
                    cart.push({
                        type: 'Frame',
                        nama: document.getElementById('bpjs_frame_nama').value,
                        harga: parseAngka(document.getElementById('bpjs_frame_harga').value),
                        qty: 1,
                        product_id: frameId
                    });
                }
                if (lensaId) {
                    cart.push({
                        type: 'Lensa',
                        nama: document.getElementById('bpjs_lensa_nama').value,
                        harga: parseAngka(document.getElementById('bpjs_lensa_harga').value),
                        qty: 1,
                        product_id: lensaId
                    });
                }
                processCart();
            }
        }
    }
    
    goStep(fromStep + 1);
}

/* ==========================================================
   VALIDATION
========================================================== */
function validateStep(step) {
    hideStepError(step);

    if (step === 1) {
        const noTrx = document.querySelector('input[name="no_transaksi"]').value.trim();
        const tgl   = document.getElementById('tgl_faktur').value;
        if (!noTrx) { showStepError(1, 'No Faktur tidak boleh kosong.'); return false; }
        if (!tgl)   { showStepError(1, 'Tanggal faktur tidak boleh kosong.'); return false; }
        return true;
    }

    if (step === 2) {
        const dokter = document.getElementById('nama_dokter').value.trim();
        if (!dokter) {
            showStepError(2, 'Nama dokter / klinik penulis resep wajib diisi.');
            document.getElementById('nama_dokter').focus();
            document.getElementById('nama_dokter').classList.add('is-invalid');
            return false;
        }
        document.getElementById('nama_dokter').classList.remove('is-invalid');

        const refractionFields = ['od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_mpd', 'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_mpd'];
        const hasAnyRefraction = refractionFields.some(fieldId => document.getElementById(fieldId).value.trim() !== '');

        if (!hasAnyRefraction) {
            showStepError(2, 'Refraksi wajib diisi — minimal satu field untuk OD atau OS.');
            document.getElementById('od_sph').focus();
            return false;
        }
        return true;
    }

    if (step === 3) {
        const isBpjs = document.getElementById('bpjs').checked;
        if (isBpjs) {
            const hasFrame = document.getElementById('bpjs_frame_id').value || document.getElementById('bpjs_frame_nama').value;
            const hasLensa = document.getElementById('bpjs_lensa_id').value || document.getElementById('bpjs_lensa_nama').value;
            if (!hasFrame && !hasLensa && cart.length === 0) {
                showStepError(3, 'Pilih minimal 1 Frame atau Lensa (BPJS).');
                return false;
            }
        } else {
            if (cart.length === 0) {
                showStepError(3, 'Tambahkan minimal 1 produk ke keranjang sebelum melanjutkan.');
                return false;
            }
        }
        return true;
    }

    if (step === 5) {
        const dp = parseAngka(document.getElementById('input_dp').value);
        const harga = parseAngka(document.getElementById('input_harga_jual').value);
        if (dp > harga && harga > 0) {
            toast('error', 'DP tidak boleh melebihi harga jual!');
            return false;
        }
        return true;
    }

    return true;
}

function showStepError(step, msg) {
    const el = document.getElementById('err-step-' + step);
    if (!el) return;
    document.getElementById('err-step-' + step + '-msg').textContent = msg;
    el.classList.add('show');
}
function hideStepError(step) {
    const el = document.getElementById('err-step-' + step);
    if (el) el.classList.remove('show');
}

/* ==========================================================
   TANGGAL AMBIL & TIPE FAKTUR LOGIC
========================================================== */
function bindOptionEvents() {
    const rBelum = document.getElementById('belum');
    const rSudah = document.getElementById('sudah');
    const tglJanji = document.getElementById('tgl_selesai_janji');
    const tglOrder = document.getElementById('tgl_order');

    const toggleAmbil = () => {
        if (rSudah.checked) {
            tglJanji.value = tglOrder.value;
            tglJanji.readOnly = true;
            tglJanji.style.backgroundColor = '#e9ecef';
        } else {
            tglJanji.readOnly = false;
            tglJanji.style.backgroundColor = '#fff';
            tglJanji.value = '';
        }
    };

    rBelum.addEventListener('change', toggleAmbil);
    rSudah.addEventListener('change', toggleAmbil);
    tglOrder.addEventListener('change', () => {
        if (rSudah.checked) tglJanji.value = tglOrder.value;
    });

    const rUmum = document.getElementById('tunai');
    const rBpjs = document.getElementById('bpjs');
    
    const uiBpjs = document.getElementById('bpjs-product-container');
    const uiUmum = document.getElementById('umum-product-container');
    const contNoBpjs = document.getElementById('no_bpjs_container');
    const printBpjs = document.getElementById('btn-print-bpjs-container');
    
    // Potongan labels
    const cPotongan = document.getElementById('container_potongan');
    const cPotonganBpjs = document.getElementById('container_potongan_bpjs');
    const lblSelisih = document.getElementById('lbl_selisih_bpjs');

    const toggleFakturType = () => {
        if (rBpjs.checked) {
            uiBpjs.classList.remove('d-none');
            uiUmum.classList.add('d-none');
            contNoBpjs.style.display = 'block';
            printBpjs.classList.remove('d-none');
            
            cPotongan.classList.add('d-none');
            cPotonganBpjs.classList.remove('d-none');
            lblSelisih.classList.remove('d-none');
        } else {
            uiBpjs.classList.add('d-none');
            uiUmum.classList.remove('d-none');
            contNoBpjs.style.display = 'none';
            printBpjs.classList.add('d-none');
            
            cPotongan.classList.remove('d-none');
            cPotonganBpjs.classList.add('d-none');
            lblSelisih.classList.add('d-none');
        }
    };

    let lastFakturType = rBpjs.checked ? '2' : '1';

    const handleFakturChange = (e) => {
        const newVal = e.target.value;
        if (cart.length > 0 && newVal !== lastFakturType) {
            Swal.fire({
                title: 'Ubah Jenis Transaksi?',
                text: 'Pesanan pada produk akan dikosongkan karena formatnya berbeda.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Ya, Ubah & Kosongkan',
                cancelButtonText: 'Batal'
            }).then(r => {
                if (r.isConfirmed) {
                    cart = [];
                    clearBpjs('Frame');
                    clearBpjs('Lensa');
                    processCart();
                    lastFakturType = newVal;
                    toggleFakturType();
                } else {
                    // Revert radio
                    if (lastFakturType === '1') { rUmum.checked = true; }
                    else { rBpjs.checked = true; }
                }
            });
        } else {
            lastFakturType = newVal;
            toggleFakturType();
        }
    };

    rUmum.addEventListener('change', handleFakturChange);
    rBpjs.addEventListener('change', handleFakturChange);

    toggleAmbil();
    toggleFakturType();
}

/* ==========================================================
   AUTOCOMPLETE ENGINE
========================================================== */
function setupAC(inp, dd, url, mapFn, onSelect) {
    let timer = null;
    inp.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 1) { 
            dd.classList.add('d-none'); 
            // Reset product_id if the input is cleared
            if (inp.id === 'ac_produk_new') {
                const pid = document.querySelector('input[name="product_id[]"]');
                if (pid) pid.value = '';
            }
            return; 
        }
        timer = setTimeout(() => {
            const separator = url.includes('?') ? '&' : '?';
            fetch(`${url}${separator}q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.length) {
                        dd.innerHTML = '<div class="ac-item text-muted">Tidak ditemukan</div>';
                    } else {
                        dd.innerHTML = data.slice(0, 8).map(item =>
                            `<div class="ac-item" data-item='${JSON.stringify(item).replace(/'/g, "&#39;")}'>${mapFn(item)}</div>`
                        ).join('');
                        dd.querySelectorAll('.ac-item[data-item]').forEach(el => {
                            el.addEventListener('click', function () {
                                onSelect(JSON.parse(this.dataset.item));
                                dd.classList.add('d-none');
                            });
                        });
                    }
                    dd.classList.remove('d-none');
                })
                .catch(() => dd.classList.add('d-none'));
        }, 280);
    });
    document.addEventListener('click', e => {
        if (!inp.contains(e.target) && !dd.contains(e.target)) dd.classList.add('d-none');
    });
    inp.addEventListener('keydown', e => {
        if (e.key === 'Escape') dd.classList.add('d-none');
    });
}

// Pasien Autocomplete
setupAC(
    document.getElementById('ac_pasien'),
    document.getElementById('dd_pasien'),
    '{{ route('patients.autocomplete') }}',
    p => `<b>${p.nama}</b> <span class="text-muted small">${p.no_bpjs || ''} ${p.no_hp || ''}</span>`,
    p => {
        document.getElementById('patient_id').value  = p.id;
        document.getElementById('nik').value      = p.nik || '';
        document.getElementById('no_bpjs').value      = p.no_bpjs || '';
        document.getElementById('nama_pasien').value  = p.nama    || '';
        document.querySelector('input[name="telp"]').value    = p.no_hp   || '';
        document.querySelector('textarea[name="alamat"]').value = p.alamat || '';
        document.getElementById('ac_pasien').value    = p.nama    || '';
        document.getElementById('patient-selected-name').textContent = p.nama;
        document.getElementById('patient-selected-badge').classList.remove('d-none');
        document.getElementById('btn-load-history').style.display = '';
        document.getElementById('no_bpjs_container').style.display = p.no_bpjs ? '' : 'none';
        
        // AUTO LOAD HISTORY
        loadPatientHistory();
    }
);

function clearPatient() {
    document.getElementById('patient_id').value  = '';
    document.getElementById('ac_pasien').value    = '';
    document.getElementById('nik').value      = '';
    document.getElementById('no_bpjs').value      = '';
    document.getElementById('nama_pasien').value  = '';
    document.querySelector('input[name="telp"]').value    = '';
    document.querySelector('textarea[name="alamat"]').value = '';
    document.getElementById('patient-selected-badge').classList.add('d-none');
    document.getElementById('btn-load-history').style.display = 'none';
    document.getElementById('no_bpjs_container').style.display = 'none';
    document.getElementById('history-tag-container').classList.add('d-none');
}

// Produk UMUM
setupAC(
    document.getElementById('ac_produk_new'),
    document.getElementById('dd_produk_new'),
    '{{ route('transactions.product.search') }}',
    f => `<b>${f.kode_produk || ''}</b> | ${f.nama || ''} <span class="text-muted small">${f.merek || ''} (Stok: ${f.stok})</span>`,
    f => {
        document.getElementById('new_item_name').value = f.nama || '';
        document.getElementById('new_item_seri').value = f.merek || '';
        // document.getElementById('new_item_warna').value = f.warna || ''; // warna tidak diambil di query
        document.getElementById('new_item_keterangan').value = f.keterangan || '';
        document.getElementById('new_item_harga').value = formatRibuan(parseInt(f.harga_jual) || 0);
        document.querySelector('input[name="product_id[]"]').value = f.id || '';
        document.getElementById('ac_produk_new').value = f.kode_produk || f.nama || '';
        document.getElementById('new_item_name').focus();
        
        // Auto set product type if category name is available
        if (f.category && f.category.nama) {
            let catName = f.category.nama.toLowerCase();
            if (catName.includes('frame')) setProductType('Frame');
            else if (catName.includes('lensa') && !catName.includes('kontak')) setProductType('Lensa');
            else if (catName.includes('aksesori') || catName.includes('kontak')) setProductType('Aksesoris');
            else setProductType('Lainnya');
        }
    }
);

function clearProductSearch() {
    document.getElementById('ac_produk_new').value = '';
    document.getElementById('new_item_name').value = '';
    document.getElementById('new_item_seri').value = '';
    document.getElementById('new_item_warna').value = '';
    document.getElementById('new_item_harga').value = '0';
    document.getElementById('new_item_keterangan').value = '';
    document.querySelector('input[name="product_id[]"]').value = '';
}

// BPJS FRAME Autocomplete
setupAC(
    document.getElementById('bpjs_frame_ac'),
    document.getElementById('bpjs_frame_dd'),
    '{{ route('products.frame.autocomplete') }}?type=Frame',
    f => `<b>${f.kode_produk || ''}</b> | ${f.nama || ''}`,
    f => {
        document.getElementById('bpjs_frame_id').value = f.id || '';
        document.getElementById('bpjs_frame_nama').value = f.nama || '';
        document.getElementById('bpjs_frame_harga').value = formatRibuan(parseInt(f.harga_jual) || 0);
        document.getElementById('bpjs_frame_ac').value = f.kode_produk || f.nama;
    }
);

// BPJS LENSA Autocomplete
setupAC(
    document.getElementById('bpjs_lensa_ac'),
    document.getElementById('bpjs_lensa_dd'),
    '{{ route('products.frame.autocomplete') }}?type=Lensa', 
    f => `<b>${f.kode_produk || ''}</b> | ${f.nama || ''}`,
    f => {
        document.getElementById('bpjs_lensa_id').value = f.id || '';
        document.getElementById('bpjs_lensa_nama').value = f.nama || '';
        document.getElementById('bpjs_lensa_harga').value = formatRibuan(parseInt(f.harga_jual) || 0);
        document.getElementById('bpjs_lensa_ac').value = f.kode_produk || f.nama;
        // Juga isikan ke master keterangan lensa jika mau disinkronkan
        document.getElementById('lensa_ket').value = f.nama;
    }
);

function clearBpjs(type) {
    const t = type.toLowerCase();
    document.getElementById(`bpjs_${t}_ac`).value = '';
    document.getElementById(`bpjs_${t}_id`).value = '';
    document.getElementById(`bpjs_${t}_nama`).value = '';
    document.getElementById(`bpjs_${t}_harga`).value = '0';
    document.getElementById(`bpjs_${t}_status`).classList.add('d-none');
    
    // Remove from cart if exists
    cart = cart.filter(i => i.type !== type);
    processCart();
}

function setBpjsItem(type) {
    const t = type.toLowerCase();
    const nama = document.getElementById(`bpjs_${t}_nama`).value;
    const harga = parseAngka(document.getElementById(`bpjs_${t}_harga`).value);
    const id = document.getElementById(`bpjs_${t}_id`).value;
    
    if (!nama) {
        snackbar(`Nama ${type} tidak boleh kosong`, 'warning');
        return;
    }
    
    document.getElementById(`bpjs_${t}_status`).classList.remove('d-none');
    
    // Replace existing type in cart
    cart = cart.filter(i => i.type !== type);
    cart.push({
        type: type,
        nama: nama,
        harga: harga,
        qty: 1,
        product_id: id
    });
    
    processCart();
    snackbar(`${type} BPJS berhasil di-set!`, 'success');
}

/* ==========================================================
   PRODUCT TYPE SELECTOR
========================================================== */
function setProductType(type) {
    document.getElementById('new_item_type').value = type;
    document.querySelectorAll('.ptype-btn').forEach(btn => {
        btn.classList.remove('active-frame', 'active-lensa', 'active-other');
        if (btn.dataset.type === type) {
            if (type === 'Frame') btn.classList.add('active-frame');
            else if (type === 'Lensa') btn.classList.add('active-lensa');
            else btn.classList.add('active-other');
        }
    });
    document.getElementById('new_item_name').focus();
}

/* ==========================================================
   CART
========================================================== */
let cart = [];

function getCartTypeBadge(type) {
    if (type === 'Frame')  return 'ct-frame';
    if (type === 'Lensa')  return 'ct-lensa';
    return 'ct-other';
}

function renderCartInline() {
    const body = document.getElementById('cart-inline-body');
    const emptyMsg = document.getElementById('cart-empty-msg');
    const totalSec = document.getElementById('cart-total-section');
    const badge = document.getElementById('cart-count-badge');

    const totalQty = cart.reduce((s, i) => s + i.qty, 0);
    badge.textContent = totalQty;

    if (cart.length === 0) {
        body.innerHTML = '';
        if (emptyMsg instanceof Node) body.appendChild(emptyMsg);
        totalSec.style.display = 'none';
        return;
    }

    if (emptyMsg) emptyMsg.style.display = 'none';
    totalSec.style.display = '';

    body.innerHTML = '';
    cart.forEach((item, idx) => {
        const row = document.createElement('div');
        row.className = 'cart-item-row';
        row.innerHTML = `
            <span class="cart-type-badge ${getCartTypeBadge(item.type)}">${item.type}</span>
            <div class="cart-item-name-group flex-grow-1" style="min-width: 0;">
                <div class="cart-item-name fw-semibold" title="${item.nama}">${item.nama || '—'}</div>
                <div class="cart-item-details">${item.seri || ''} ${item.warna ? (item.seri ? '| ' : '') + item.warna : ''}</div>
            </div>
            <input type="number" class="cart-item-qty" value="${item.qty}" min="1" data-index="${idx}">
            <span class="cart-item-price">Rp ${formatRibuan(item.qty * item.harga)}</span>
            <span class="cart-item-del" onclick="removeCartItem(${idx})">✕</span>
        `;
        row.querySelector('.cart-item-qty').addEventListener('change', function () {
            const val = Math.max(1, parseInt(this.value) || 1);
            cart[parseInt(this.dataset.index)].qty = val;
            this.value = val;
            processCart();
        });
        body.appendChild(row);
    });

    const total = cart.reduce((s, i) => s + i.qty * i.harga, 0);
    document.getElementById('cart-inline-total').textContent = 'Rp ' + formatRibuan(total);
}

function processCart() {
    const total = cart.reduce((s, i) => s + i.qty * i.harga, 0);
    document.getElementById('cart_data').value = JSON.stringify(cart);

    const inputHarga = document.getElementById('input_harga_jual');
    if (parseAngka(inputHarga.value) === 0 || parseAngka(inputHarga.value) === lastAutoHarga) {
        inputHarga.value = formatRibuan(total);
        lastAutoHarga = total;
    }
    
    checkPriceLow();
    calculateSisa();
    renderCartInline();
}

let lastAutoHarga = 0;

function addItemToCart() {
    const type     = document.getElementById('new_item_type').value;
    const nama     = document.getElementById('new_item_name').value.trim();
    const seri     = document.getElementById('new_item_seri').value.trim();
    const warna    = document.getElementById('new_item_warna').value.trim();
    const ket      = document.getElementById('new_item_keterangan').value.trim();
    const harga    = parseAngka(document.getElementById('new_item_harga').value);
    const qty      = Math.max(1, parseInt(document.getElementById('new_item_qty').value) || 1);
    const prodId   = document.querySelector('input[name="product_id[]"]').value;

    if (!nama) {
        document.getElementById('new_item_name').classList.add('is-invalid');
        document.getElementById('new_item_name').focus();
        snackbar('Nama produk wajib diisi!', 'warning');
        return;
    }
    document.getElementById('new_item_name').classList.remove('is-invalid');
    const existIdx = cart.findIndex(i => 
        i.nama === nama && 
        i.type === type && 
        i.seri === seri && 
        i.warna === warna && 
        i.harga === harga
    );
    if (existIdx >= 0) {
        cart[existIdx].qty += qty;
        snackbar(`Qty diperbarui.`, 'info');
    } else {
        cart.push({ type, nama, seri, warna, keterangan: ket, harga, qty, product_id: prodId });
        snackbar(`Ditambahkan ke keranjang.`, 'success');
    }

    processCart();
    clearProductSearch();
    document.getElementById('new_item_qty').value = '1';

    if (type === 'Frame' && !cart.some(i => i.type === 'Lensa')) {
        setProductType('Lensa');
    } else {
        document.getElementById('new_item_name').focus();
    }
}

function removeCartItem(idx) {
    cart.splice(idx, 1);
    processCart();
}

/* ==========================================================
   FINANCE CALCULATIONS
========================================================== */
const inputHargaJual = document.getElementById('input_harga_jual');
const inputPotongan  = document.getElementById('input_potongan');
const inputPotonganBpjs = document.getElementById('input_potongan_bpjs');
const inputDp        = document.getElementById('input_dp');
const inputSisa      = document.getElementById('input_sisa');

function calculateSisa() {
    const h = parseAngka(inputHargaJual.value);
    const p = document.getElementById('bpjs').checked ? parseAngka(inputPotonganBpjs.value) : parseAngka(inputPotongan.value);
    const d = parseAngka(inputDp.value);
    const sisa = Math.max(0, h - p - d);
    inputSisa.value = formatRibuan(sisa);

    const dpWarn = document.getElementById('dp-warning');
    if (d > h && h > 0) dpWarn.classList.remove('d-none');
    else dpWarn.classList.add('d-none');
}

function checkPriceLow() {
    const cartTotal = cart.reduce((s, i) => s + i.qty * i.harga, 0);
    const hargaJual = parseAngka(inputHargaJual.value);
    const warn = document.getElementById('price-low-warning');
    if (hargaJual > 0 && hargaJual < cartTotal) warn.classList.remove('d-none');
    else warn.classList.add('d-none');
}

[inputHargaJual, inputPotongan, inputPotonganBpjs, inputDp].forEach(el => {
    el.addEventListener('input', function () {
        this.value = formatRibuan(parseAngka(this.value));
        calculateSisa();
        checkPriceLow();
        if (currentStep === 5) buildCheckoutSummary();
    });
});

function buildCheckoutSummary() {
    const noTrx      = formatFieldText(document.querySelector('input[name="no_transaksi"]').value, '—');
    const tglFaktur  = formatDateInput(document.getElementById('tgl_faktur').value);
    const typeFaktur = document.getElementById('bpjs').checked ? 'BPJS' : 'Umum';
    const namaPas    = formatFieldText(document.getElementById('nama_pasien').value, 'UMUM');
    const telp       = formatFieldText(document.querySelector('input[name="telp"]').value, '—');
    const nikEl   = document.getElementById('nik');
    const nik     = nikEl ? formatFieldText(nikEl.value, '—') : '—';
    const noBpjsEl   = document.getElementById('no_bpjs');
    const noBpjs     = noBpjsEl ? formatFieldText(noBpjsEl.value, '—') : '—';
    const alamat     = formatFieldText(document.querySelector('textarea[name="alamat"]').value, '—');
    const namaDokter = formatFieldText(document.getElementById('nama_dokter').value, '—');
    const tglResep   = formatDateInput(document.querySelector('input[name="tgl_resep"]').value);
    const catatanResep = formatFieldText(document.querySelector('textarea[name="catatan_resep"]').value, '—');
    const lensaKet   = formatFieldText(document.getElementById('lensa_ket').value, '—');
    const ketFrame   = formatFieldText(document.querySelector('input[name="keterangan_frame"]').value, '—');

    const odSph  = formatFieldText(document.getElementById('od_sph').value, '—');
    const odCyl  = formatFieldText(document.getElementById('od_cyl').value, '—');
    const odAxis = formatFieldText(document.getElementById('od_axis').value, '—');
    const odAdd  = formatFieldText(document.getElementById('od_add').value, '—');
    const odMpd  = formatFieldText(document.getElementById('od_mpd').value, '—');
    const odPrism = formatFieldText(document.getElementById('od_prism').value, '—');
    const osSph  = formatFieldText(document.getElementById('os_sph').value, '—');
    const osCyl  = formatFieldText(document.getElementById('os_cyl').value, '—');
    const osAxis = formatFieldText(document.getElementById('os_axis').value, '—');
    const osAdd  = formatFieldText(document.getElementById('os_add').value, '—');
    const osMpd  = formatFieldText(document.getElementById('os_mpd').value, '—');
    const osPrism = formatFieldText(document.getElementById('os_prism').value, '—');

    const lab                 = formatFieldText(document.querySelector('input[name="lab"]').value, '—');
    const tglOrder            = formatDateInput(document.querySelector('input[name="tgl_order"]').value);
    const tglLensaDatang      = formatDateInput(document.querySelector('input[name="tgl_lensa_datang"]').value);
    const tglFaset            = formatDateInput(document.querySelector('input[name="tgl_faset"]').value);
    const tempatFaset         = formatFieldText(document.querySelector('input[name="tempat_faset"]').value, '—');
    const tglSelesaiFaset     = formatDateInput(document.querySelector('input[name="tgl_selesai_faset"]').value);
    const tglJanjiCustomer    = formatDateInput(document.querySelector('input[name="tgl_janji_customer"]').value);
    const diambil             = document.querySelector('input[name="diambil"]:checked')?.value === '1' ? 'Sudah' : 'Belum';
    const tglDiambil          = formatDateInput(document.querySelector('input[name="tgl_diambil"]').value);
    const catatan             = formatFieldText(document.querySelector('textarea[name="catatan"]').value, '—');

    document.getElementById('checkout-summary-badges').innerHTML = `
        <div class="col-auto"><span class="badge bg-primary-subtle text-primary py-1">${noTrx}</span></div>
        <div class="col-auto"><span class="badge bg-success-subtle text-success py-1"><i class="bi bi-person me-1"></i>${namaPas}</span></div>
        <div class="col-auto"><span class="badge bg-secondary-subtle text-secondary py-1"><i class="bi bi-calendar-event me-1"></i>${typeFaktur}</span></div>
    `;

    document.getElementById('checkout-preview-invoice').innerHTML = `
        <div class="border rounded-3 p-3 bg-white">
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="text-muted small">No Faktur</div>
                    <div class="fw-semibold">${noTrx}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Tanggal Faktur</div>
                    <div>${tglFaktur}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Tipe Transaksi</div>
                    <div>${typeFaktur}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Total Keranjang</div>
                    <div>Rp ${formatRibuan(cart.reduce((sum, item) => sum + item.qty * item.harga, 0))}</div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('checkout-preview-patient').innerHTML = `
        <div class="border rounded-3 p-3 bg-white">
            <div class="mb-2 text-muted small">Data Pasien & Resep</div>
            <div class="row g-2">
                <div class="col-sm-6"><strong>Nama Pasien</strong><div>${namaPas}</div></div>
                <div class="col-sm-6"><strong>No. Telp</strong><div>${telp}</div></div>
                <div class="col-sm-6"><strong>No. NIK</strong><div>${nik}</div></div>
                <div class="col-sm-6"><strong>No. BPJS</strong><div>${noBpjs}</div></div>
                <div class="col-sm-6"><strong>Alamat</strong><div>${alamat}</div></div>
                <div class="col-sm-6"><strong>Dokter / Klinik</strong><div>${namaDokter}</div></div>
                <div class="col-sm-6"><strong>Tanggal Resep</strong><div>${tglResep}</div></div>
                <div class="col-sm-6"><strong>Catatan Resep</strong><div>${catatanResep}</div></div>
            </div>
        </div>
    `;

    document.getElementById('checkout-preview-resep').innerHTML = `
        <div class="border rounded-3 p-3 bg-white">
            <div class="mb-2 text-muted small">Refraksi & Keterangan Lensa</div>
            <div class="row g-2">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted small">OD</div>
                        <div class="text-muted small">SPH / CYL / AXIS / ADD / MPD / PRISM</div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge bg-light text-dark">${odSph}</span>
                        <span class="badge bg-light text-dark">${odCyl}</span>
                        <span class="badge bg-light text-dark">${odAxis}</span>
                        <span class="badge bg-light text-dark">${odAdd}</span>
                        <span class="badge bg-light text-dark">${odMpd}</span>
                        <span class="badge bg-light text-dark">${odPrism}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted small">OS</div>
                        <div class="text-muted small">SPH / CYL / AXIS / ADD / MPD / PRISM</div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge bg-light text-dark">${osSph}</span>
                        <span class="badge bg-light text-dark">${osCyl}</span>
                        <span class="badge bg-light text-dark">${osAxis}</span>
                        <span class="badge bg-light text-dark">${osAdd}</span>
                        <span class="badge bg-light text-dark">${osMpd}</span>
                        <span class="badge bg-light text-dark">${osPrism}</span>
                    </div>
                </div>
                <div class="col-sm-6"><strong>Lensa</strong><div>${lensaKet}</div></div>
                <div class="col-sm-6"><strong>Keterangan Ukuran</strong><div>${ketFrame}</div></div>
            </div>
        </div>
    `;

    const listEl = document.getElementById('checkout-item-list');
    if (cart.length === 0) {
        listEl.innerHTML = '<div class="text-muted text-center small py-3">Tidak ada item di keranjang.</div>';
    } else {
        listEl.innerHTML = `
            <div class="mb-2 text-muted small">Produk yang dipilih</div>
            ${cart.map(item => `
                <div class="d-flex align-items-start gap-2 py-2 border-bottom" style="font-size:0.88rem">
                    <div class="cart-type-badge ${getCartTypeBadge(item.type)}" style="font-size:0.65rem">${item.type}</div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">${item.nama}</div>
                        <div class="text-muted" style="font-size:0.82rem;">${formatFieldText(item.seri, '')}${item.warna ? ' • ' + item.warna : ''}${item.keterangan ? ' • ' + item.keterangan : ''}</div>
                    </div>
                    <div class="text-end" style="min-width:96px;">
                        <div class="text-muted small">×${item.qty}</div>
                        <strong>Rp ${formatRibuan(item.qty * item.harga)}</strong>
                    </div>
                </div>
            `).join('')}
        `;
    }

    const cartTotal = cart.reduce((s, i) => s + i.qty * i.harga, 0);
    const hargaJual = parseAngka(inputHargaJual.value);
    const potongan = parseAngka(inputPotongan.value);
    const potonganBpjs = parseAngka(inputPotonganBpjs.value);
    const dp = parseAngka(inputDp.value);
    const sisa = parseAngka(inputSisa.value);

    document.getElementById('checkout-preview-payments').innerHTML = `
        <div class="border rounded-3 p-3 bg-white">
            <div class="mb-2 text-muted small">Rincian Pembayaran</div>
            <div class="row g-2">
                <div class="col-sm-6"><strong>Total Keranjang</strong><div>Rp ${formatRibuan(cartTotal)}</div></div>
                <div class="col-sm-6"><strong>Harga Jual</strong><div>Rp ${formatRibuan(hargaJual)}</div></div>
                <div class="col-sm-6"><strong>Potongan</strong><div>Rp ${formatRibuan(potongan)}</div></div>
                <div class="col-sm-6"><strong>Potongan BPJS</strong><div>Rp ${formatRibuan(potonganBpjs)}</div></div>
                <div class="col-sm-6"><strong>DP</strong><div>Rp ${formatRibuan(dp)}</div></div>
                <div class="col-sm-6"><strong>Sisa Tagihan</strong><div>Rp ${formatRibuan(sisa)}</div></div>
            </div>
        </div>
    `;

    document.getElementById('checkout-preview-additional').innerHTML = `
        <div class="border rounded-3 p-3 bg-white">
            <div class="mb-2 text-muted small">Data Tambahan</div>
            <div class="row g-2">
                <div class="col-sm-4"><strong>Lab</strong><div>${lab}</div></div>
                <div class="col-sm-4"><strong>Tgl Order</strong><div>${tglOrder}</div></div>
                <div class="col-sm-4"><strong>Tgl Lensa Datang</strong><div>${tglLensaDatang}</div></div>
                <div class="col-sm-4"><strong>Tgl Faset</strong><div>${tglFaset}</div></div>
                <div class="col-sm-4"><strong>Tempat Faset</strong><div>${tempatFaset}</div></div>
                <div class="col-sm-4"><strong>Tgl Selesai Faset</strong><div>${tglSelesaiFaset}</div></div>
                <div class="col-sm-4"><strong>Tgl Janji Customer</strong><div>${tglJanjiCustomer}</div></div>
                <div class="col-sm-4"><strong>Status Ambil</strong><div>${diambil}</div></div>
                <div class="col-sm-4"><strong>Tgl Diambil</strong><div>${tglDiambil}</div></div>
                <div class="col-12"><strong>Catatan</strong><div>${catatan}</div></div>
            </div>
        </div>
    `;

    document.getElementById('finance-cart-summary').innerHTML = `
        <div class="s-row"><span class="s-label">Total item keranjang</span><span class="s-val">Rp ${formatRibuan(cartTotal)}</span></div>
        <div class="s-divider"></div>
        <div class="s-row"><span class="s-label">${cart.length} item</span><span class="s-val">${cart.map(i => i.type).join(', ') || '—'}</span></div>
    `;

    if (hargaJual === 0) {
        inputHargaJual.value = formatRibuan(cartTotal);
        lastAutoHarga = cartTotal;
    }

    calculateSisa();
    checkPriceLow();
}

/* ==========================================================
   HISTORY REKAM MEDIS
========================================================== */
function loadPatientHistory() {
    const pid = document.getElementById('patient_id').value;
    if (!pid) return;

    fetch(`{{ route('patients.latest-refraction', ':pid') }}`.replace(':pid', pid))
        .then(r => r.json())
        .then(data => {
            if (data.od_sph !== undefined) {
                const fields = ['od_sph','od_cyl','od_axis','od_add','od_mpd','os_sph','os_cyl','os_axis','os_add','os_mpd'];
                fields.forEach(f => {
                    const el = document.getElementById(f);
                    if (el && data[f] !== undefined && data[f] !== null) {
                        el.value = data[f];
                        el.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
                document.getElementById('history-tag-container').classList.remove('d-none');
                
                // Set default doctor from latest history
                if (data.history && data.history.length > 0) {
                    const latestDoc = data.history[0].dokter;
                    if (latestDoc && latestDoc !== '-') {
                        document.getElementById('nama_dokter').value = latestDoc;
                    }
                }
            }

            if (data.history && data.history.length > 0) {
                renderPatientHistory(data.history);
                document.getElementById('history-count').textContent = data.history.length;
                document.getElementById('patient-history-section').classList.remove('d-none');
            } else {
                document.getElementById('patient-history-section').classList.add('d-none');
            }
        })
        .catch(() => {});
}

function renderPatientHistory(history) {
    const container = document.getElementById('patient-history-list');
    container.innerHTML = '';
    history.forEach(rm => {
        const rmDiv = document.createElement('div');
        rmDiv.className = 'p-3 border-bottom';
        rmDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-primary me-2">${rm.tanggal_kunjungan}</span>
                    <small class="text-muted">Dokter: ${rm.dokter}</small>
                </div>
                <button type="button" class="btn btn-xs btn-outline-info" onclick='loadFromHistory("${rm.id}", ${JSON.stringify(rm).replace(/"/g, "&quot;")})'>
                    Gunakan
                </button>
            </div>
            <table class="table table-sm table-bordered mb-0" style="font-size:.8rem">
                <tbody>
                    <tr><td>OD</td><td>${rm.od_sph||'-'}</td><td>${rm.od_cyl||'-'}</td><td>${rm.od_axis||'-'}</td><td>${rm.od_add||'-'}</td><td>${rm.od_pd||'-'}</td></tr>
                    <tr><td>OS</td><td>${rm.os_sph||'-'}</td><td>${rm.os_cyl||'-'}</td><td>${rm.os_axis||'-'}</td><td>${rm.os_add||'-'}</td><td>${rm.os_pd||'-'}</td></tr>
                </tbody>
            </table>
        `;
        container.appendChild(rmDiv);
    });
}
function loadFromHistory(rmId, rmData) {
    ['od_sph','od_cyl','od_axis','od_add','od_mpd', 'od_prism','os_sph','os_cyl','os_axis','os_add','os_mpd', 'os_prism'].forEach(f => {
        const el = document.getElementById(f);
        if (el) {
            let key = f;
            if (f.endsWith('_mpd')) key = f.replace('_mpd', '_pd'); 
            el.value = rmData[key] || '';
        }
    });
    // Fill doctor name
    if (rmData.dokter && rmData.dokter !== '-') {
        document.getElementById('nama_dokter').value = rmData.dokter;
    }

    document.getElementById('history-tag-container').classList.remove('d-none');
    snackbar('Refraksi dimuat', 'info');
}

function copyOdToOs() {
    ['sph','cyl','axis','add','mpd', 'prism'].forEach(f => {
        const od = document.getElementById('od_' + f);
        const os = document.getElementById('os_' + f);
        if (od && os) os.value = od.value;
    });
    snackbar('Disalin ke OS', 'info');
}

/* ==========================================================
   SPA / EDIT LOGIC
========================================================== */
function fillForm(trx) {
    if (!trx) { toast('info', 'Data tidak ditemukan.'); return; }

    document.getElementById('trx_id').value      = trx.id   || '';
    document.getElementById('patient_id').value  = trx.patient_id || '';
    document.getElementById('display-no-trx').textContent = trx.no_transaksi || '—';

    const fm = document.getElementById('pos-form');
    const sv = (sel, val) => { const el = fm.querySelector(sel); if (el) el.value = val; };

    // Step 1
    sv('input[name="no_transaksi"]',  trx.no_transaksi || '');
    sv('input[name="tgl_order"]',     trx.tgl_order    || '{{ date('Y-m-d') }}');
    sv('input[name="tgl_faset"]',     trx.tgl_faset    || '');
    sv('input[name="lab"]',           trx.lab          || '');
    sv('input[name="tempat_faset"]',  trx.tempat_faset || '');
    sv('input[name="tgl_datang_faset"]',  trx.tgl_datang_faset  || '');
    sv('input[name="tgl_selesai_faset"]', trx.tgl_selesai_faset || '');
    sv('input[name="tgl_selesai_janji"]', trx.tgl_selesai_janji || '');
    sv('textarea[name="catatan"]',    trx.catatan      || '');
    if (document.getElementById('tgl_faktur')) {
        document.getElementById('tgl_faktur').value = trx.tgl_faktur || trx.tgl_order || '{{ date('Y-m-d') }}';
    }

    // Step 2 — Pasien
    const p = trx.patient || {};
    const selectedNik = p.nik || trx.nik || '';
    sv('input[name="nik"]',    selectedNik);
    const selectedBpjs = p.no_bpjs || trx.no_bpjs || '';
    sv('input[name="no_bpjs"]',    selectedBpjs);
    sv('input[name="nama"]',       p.nama     || trx.nama_pasien || '');
    sv('textarea[name="alamat"]',  p.alamat   || trx.alamat_pasien || '');
    sv('input[name="telp"]',       p.no_hp    || trx.telp_pasien || '');
    document.getElementById('nama_pasien').value = p.nama || trx.nama_pasien || '';
    document.getElementById('no_bpjs_container').style.display = selectedBpjs ? '' : 'none';
    if (p.nama || trx.nama_pasien) {
        document.getElementById('patient-selected-name').textContent = p.nama || trx.nama_pasien;
        document.getElementById('patient-selected-badge').classList.remove('d-none');
        document.getElementById('btn-load-history').style.display = '';
    }

    // Step 2 — Resep
    sv('input[name="nama_dokter"]', trx.nama_dokter || trx.asal_resep || '');

    // Step 2 — Refraksi
    ['od_sph','od_cyl','od_axis','od_add','od_mpd','od_prism','os_sph','os_cyl','os_axis','os_add','os_mpd', 'os_prism'].forEach(f => {
        const el = document.getElementById(f);
        if (el) el.value = trx[f] || '';
    });
    sv('input[name="lensa"]', trx.lensa || '');
    sv('input[name="keterangan_frame"]', trx.keterangan_frame || '');

    // Step 3 — Produk/Cart
    cart = [];
    if (trx.items && trx.items.length) {
        trx.items.forEach(item => {
            cart.push({
                type: item.type || 'Lainnya',
                nama: item.nama_produk || '',
                seri: item.seri        || trx.seri   || '',
                warna: item.warna      || trx.warna  || '',
                keterangan: item.keterangan || trx.keterangan_frame || '',
                harga: parseFloat(item.harga_satuan) || 0,
                qty: item.qty || 1,
                product_id: item.product_id || '',
            });
        });
    } else if (trx.kode_frame || trx.nama_produk) {
        // Fallback for old single-item format
        cart.push({
            type: 'Lainnya',
            nama: trx.nama_produk || trx.kode_frame || '',
            seri: trx.seri || '',
            warna: trx.warna || '',
            keterangan: trx.keterangan_frame || '',
            harga: trx.items && trx.items[0] ? parseFloat(trx.items[0].harga_satuan) : 0,
            qty: 1,
        });
    }

    // Step 5 — Finance
    const hj = parseFloat(trx.harga_jual || trx.total_harga || 0);
    lastAutoHarga = 0; // reset so it doesn't lock
    inputHargaJual.value = formatRibuan(hj);
    inputDp.value        = formatRibuan(parseFloat(trx.dp  || trx.bayar  || 0));
    
    // Radios (trigger logic first)
    const tf = trx.typefaktur == 2 ? 'bpjs' : 'tunai';
    const db = trx.diambil    == 1 ? 'sudah' : 'belum';
    document.getElementById(tf).checked = true;
    document.getElementById(db).checked = true;
    
    // Potongan based on tipe faktur
    if (trx.typefaktur == 2) {
        document.getElementById('input_potongan_bpjs').value = formatRibuan(parseFloat(trx.potongan || trx.diskon_nominal || 0));
        document.getElementById('input_potongan').value = 0;
        
        // Auto fill ui bpjs if found
        clearBpjs('Frame');
        clearBpjs('Lensa');
        cart.forEach(item => {
            if (item.type === 'Frame') {
                document.getElementById('bpjs_frame_id').value = item.product_id;
                document.getElementById('bpjs_frame_nama').value = item.nama;
                document.getElementById('bpjs_frame_harga').value = formatRibuan(item.harga);
                document.getElementById('bpjs_frame_status').classList.remove('d-none');
            } else if (item.type === 'Lensa') {
                document.getElementById('bpjs_lensa_id').value = item.product_id;
                document.getElementById('bpjs_lensa_nama').value = item.nama;
                document.getElementById('bpjs_lensa_harga').value = formatRibuan(item.harga);
                document.getElementById('bpjs_lensa_status').classList.remove('d-none');
            }
        });
    } else {
        document.getElementById('input_potongan').value = formatRibuan(parseFloat(trx.potongan || trx.diskon_nominal || 0));
        document.getElementById('input_potongan_bpjs').value = 0;
    }

    // Call bindOptionEvents listeners indirectly
    const evt = new Event('change');
    document.getElementById(tf).dispatchEvent(evt);
    document.getElementById(db).dispatchEvent(evt);

    calculateSisa();
    processCart();
    
    document.getElementById('btn-simpan').innerHTML = '<i class="bi bi-save"></i> Update Transaksi';

    // Go to step 1 after load
    goStep(1);
    
    // Fix navigation URL
    window.history.pushState(null, '', `{{ url('transactions') }}/${trx.id}`);
}

/* ==========================================================
   SEARCH ENGINE & MODAL
========================================================== */
function loadSearchData(q, page = 1) {
    const tbody = document.querySelector('#searchTable tbody');
    const paginationEl = document.getElementById('searchPagination');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3"><div class="spinner-border text-primary spinner-border-sm"></div></td></tr>';
    paginationEl.innerHTML = '';
    fetch(`{{ route('transactions.pos.search') }}?q=${encodeURIComponent(q)}&page=${page}`)
        .then(r => r.json())
        .then(data => {
            if (!data.data || !data.data.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted small py-3">Tidak ada data</td></tr>';
                return;
            }
            tbody.innerHTML = data.data.map(d => `
                <tr>
                    <td class="small">${d.tanggal}</td>
                    <td class="fw-bold text-primary" style="cursor:pointer" onclick="selectSearchTrx(${d.id})">${d.no_transaksi}</td>
                    <td>${d.pasien}</td>
                    <td class="text-end fw-semibold">${d.total}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="selectSearchTrx(${d.id})"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                <li><a class="dropdown-item" href="#" onclick="openPrintModal(${d.id}, ${d.is_bpjs})"><i class="bi bi-printer me-2"></i>Cetak</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            `).join('');
            
            if (data.last_page > 1) {
                let paginationHtml = '<nav><ul class="pagination pagination-sm justify-content-center">';
                if (data.current_page > 1) {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadSearchData('${q}', ${data.current_page - 1})">«</a></li>`;
                }
                for (let i = Math.max(1, data.current_page - 2); i <= Math.min(data.last_page, data.current_page + 2); i++) {
                    paginationHtml += `<li class="page-item ${i === data.current_page ? 'active' : ''}"><a class="page-link" href="#" onclick="loadSearchData('${q}', ${i})">${i}</a></li>`;
                }
                if (data.current_page < data.last_page) {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadSearchData('${q}', ${data.current_page + 1})">»</a></li>`;
                }
                paginationHtml += '</ul></nav>';
                paginationEl.innerHTML = paginationHtml;
            }
        });
}

function selectSearchTrx(id) {
    const searchModalEl = bootstrap.Modal.getInstance(document.getElementById('searchModal'));
    if(searchModalEl) searchModalEl.hide();
    
    // Instead of redirecting to old controller layout, we can fetch JSON and populate here
    // or we can redirect to the view which will populate.
    // The previous implementation redirected:
    window.location.href = `{{ url('transactions/create') }}/${id}`;
}

let searchTimer;
document.getElementById('modalSearchInput').addEventListener('input', function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadSearchData(this.value, 1), 300);
});

function openSearchModal() {
    const searchModalEl = new bootstrap.Modal(document.getElementById('searchModal'));
    searchModalEl.show();
    loadSearchData('', 1);
    setTimeout(() => document.getElementById('modalSearchInput').focus(), 300);
}

// On Page Load: Fill Form if ID is present in URL
document.addEventListener('DOMContentLoaded', () => {
    // If the backend passes $transaction variable, we fill it.
    @if(isset($transaction))
        const trxData = @json($transaction);
        fillForm(trxData);
    @else
        updateStepUI(1);
        bindOptionEvents();
    @endif
});

/* ==========================================================
   FORM SUBMIT / DELETE / ETC
========================================================== */
function printBpjsForm() {
    const originalId = document.getElementById('trx_id').value;
    if(!originalId) {
        toast('warning', 'Simpan transaksi dulu agar bisa cetak.');
        return;
    }
    doPrint('formulir_bpjs');
}

document.getElementById('pos-form').addEventListener('submit', function (e) {
    e.preventDefault();
    for (let s = 1; s <= 5; s++) {
        if (!validateStep(s)) { goStep(s); return; }
    }

    const btn = document.getElementById('btn-simpan');
    const oldHtml = btn.innerHTML;
    btn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Menyimpan...';
    btn.disabled = true;

    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('trx_id').value = data.data.id;
            Swal.fire({
                title: 'Transaksi Berhasil!',
                text: 'Mengalihkan ke riwayat transaksi...',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '{{ route("transactions.index") }}?just_saved=' + data.data.id;
            });
        } else { Swal.fire('Error', data.message, 'error'); }
    })
    .catch(err => Swal.fire('Error', err.toString(), 'error'))
    .finally(() => { btn.innerHTML = oldHtml; btn.disabled = false; });
});

document.addEventListener('DOMContentLoaded', () => {
    updateStepUI(1);
    bindOptionEvents();
});
</script>
