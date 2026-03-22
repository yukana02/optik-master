<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Optik Store</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #1e2a5e;
            --sidebar-hover: rgba(255,255,255,.1);
            --sidebar-active: rgba(255,255,255,.18);
            --topbar-height: 60px;
        }
        body { background: #f4f6fb; font-size: .9rem; }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-width); height: 100vh;
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            z-index: 1040; overflow-y: auto;
            transition: transform .3s ease;
        }
        .sidebar-brand {
            padding: 18px 20px; color: #fff; font-size: 1.1rem; font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,.1);
            text-decoration: none; display: flex; align-items: center; gap: 10px;
            flex-shrink: 0;
        }
        .sidebar-brand:hover { color: #fff; }
        .nav-section {
            padding: 14px 20px 4px; font-size: .7rem; letter-spacing: .08em;
            text-transform: uppercase; color: rgba(255,255,255,.4);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75); padding: 10px 20px;
            display: flex; align-items: center; gap: 10px;
            border-radius: 8px; margin: 2px 10px; font-size: .85rem;
            transition: background .15s, color .15s;
            /* Pastikan tap area cukup besar di mobile */
            min-height: 44px;
        }
        .sidebar .nav-link:hover  { background: var(--sidebar-hover); color: #fff; }
        .sidebar .nav-link.active { background: var(--sidebar-active); color: #fff; font-weight: 500; }
        .sidebar .nav-link .bi    { font-size: 1rem; flex-shrink: 0; }
        .sidebar-footer {
            margin-top: auto; padding: 16px;
            border-top: 1px solid rgba(255,255,255,.1); flex-shrink: 0;
        }

        /* ── Sidebar overlay (mobile) ── */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 1035;
        }
        .sidebar-overlay.show { display: block; }

        /* ── Main layout ── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh; display: flex; flex-direction: column;
        }
        .topbar {
            height: var(--topbar-height); background: #fff;
            border-bottom: 1px solid #e9ecef;
            display: flex; align-items: center;
            padding: 0 16px; gap: 10px;
            position: sticky; top: 0; z-index: 1030;
        }
        .topbar-title { font-weight: 600; font-size: 1rem; flex: 1; min-width: 0; }
        .content { padding: 20px 16px; flex: 1; overflow-x: hidden; }

        /* ── Cards ── */
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 8px rgba(0,0,0,.07); }
        .card-header { background: transparent; border-bottom: 1px solid #f0f0f0; font-weight: 600; }

        /* ── Stat cards ── */
        .stat-card { border-left: 4px solid; }
        .stat-card .stat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
            flex-shrink: 0;
        }
        .stat-val   { font-size: 1.5rem; font-weight: 700; line-height: 1.1; }
        .stat-label { font-size: .76rem; color: #6c757d; margin-bottom: 2px; }

        /* ── Table ── */
        .table th {
            font-size: .75rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: .04em; color: #6c757d; border-top: none;
        }
        .table td { vertical-align: middle; }

        /* ── Reusable btn-xs ── */
        .btn-xs { padding: 3px 8px; font-size: .75rem; }

        /* ── Status badges ── */
        .badge-lunas   { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-batal   { background: #fee2e2; color: #991b1b; }

        /* ── Notification bell ── */
        .notif-btn { position: relative; }
        .notif-dot {
            position: absolute; top: 4px; right: 4px;
            width: 8px; height: 8px; border-radius: 50%;
            background: #ef4444; border: 1.5px solid #fff;
        }

        /* ── Session timeout warning ── */
        #session-warning {
            position: fixed; bottom: 24px; right: 16px; z-index: 9999;
            min-width: 280px; max-width: calc(100vw - 32px); display: none;
        }

        /* ═══════════════════════════════════════
           MOBILE — breakpoint 992px (konsisten
           dengan Bootstrap lg)
        ═══════════════════════════════════════ */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .content { padding: 14px 12px; }
            .topbar { padding: 0 12px; }
        }

        /* Mobile — tabel horizontal scroll */
        @media (max-width: 767.98px) {
            /* Filter forms — stack vertically */
            .filter-form-wrap { flex-direction: column !important; align-items: stretch !important; }
            .filter-form-wrap .form-control,
            .filter-form-wrap .form-select { width: 100% !important; }
            /* Card header — stack on mobile */
            .card-header-mobile { flex-direction: column !important; align-items: flex-start !important; gap: 10px !important; }
            /* Stat val smaller on tiny screens */
            .stat-val { font-size: 1.25rem; }
        }

        /* ── Print ── */
        @media print {
            .sidebar, .sidebar-overlay, .topbar, .no-print { display: none !important; }
            .main-wrapper { margin-left: 0 !important; }
            .content { padding: 0 !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR OVERLAY (mobile) --}}
<div class="sidebar-overlay" id="sidebar-overlay"></div>

{{-- SIDEBAR --}}
<nav class="sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <i class="bi bi-eyeglasses fs-4"></i>
        <span>Optik Store</span>
    </a>

    <div class="nav-section">Menu</div>
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="nav-section">Pasien</div>
    <a href="{{ route('patients.index') }}" class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Data Pasien
    </a>
    @canany(['medical_record.view'])
    <a href="{{ route('medical-records.index') }}" class="nav-link {{ request()->routeIs('medical-records.*') ? 'active' : '' }}">
        <i class="bi bi-clipboard2-pulse"></i> Rekam Medis
    </a>
    @endcanany

    <div class="nav-section">Toko</div>
    <a href="{{ route('transactions.create') }}" class="nav-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}"
       onclick="if(window.innerWidth<992) closeSidebar()">
        <i class="bi bi-cart-plus"></i> POS / Kasir
    </a>
    <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.index','transactions.show') ? 'active' : '' }}">
        <i class="bi bi-receipt-cutoff"></i> Riwayat Transaksi
    </a>
    @canany(['product.view'])
    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i> Produk
    </a>
    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
        <i class="bi bi-tags"></i> Kategori
    </a>
    @endcanany

    @canany(['report.view'])
    <div class="nav-section">Laporan</div>
    <a href="{{ route('reports.penjualan') }}" class="nav-link {{ request()->routeIs('reports.penjualan') ? 'active' : '' }}">
        <i class="bi bi-graph-up-arrow"></i> Lap. Penjualan
    </a>
    <a href="{{ route('reports.produk-terlaris') }}" class="nav-link {{ request()->routeIs('reports.produk-terlaris') ? 'active' : '' }}">
        <i class="bi bi-trophy"></i> Produk Terlaris
    </a>
    <a href="{{ route('reports.stok') }}" class="nav-link {{ request()->routeIs('reports.stok') ? 'active' : '' }}">
        <i class="bi bi-boxes"></i> Lap. Stok
    </a>
    <a href="{{ route('reports.mutasi-stok') }}" class="nav-link {{ request()->routeIs('reports.mutasi-stok') ? 'active' : '' }}">
        <i class="bi bi-arrow-left-right"></i> Mutasi Stok
    </a>
    @endcanany

    @canany(['supplier.view'])
    <div class="nav-section">Pengadaan</div>
    <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
        <i class="bi bi-truck"></i> Supplier
    </a>
    <a href="{{ route('purchase-orders.index') }}" class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
        <i class="bi bi-bag-check"></i> Purchase Order
    </a>
    @endcanany

    @if(auth()->user()->hasRole('super_admin'))
    <div class="nav-section">Sistem</div>
    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
        <i class="bi bi-person-gear"></i> Manajemen User
    </a>
    <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
        <i class="bi bi-journal-text"></i> Activity Log
    </a>
    @endif

    <div class="sidebar-footer">
        <div class="text-white-50 small mb-2">
            <i class="bi bi-person-circle me-1"></i>
            {{ auth()->user()->name }}
            <span class="badge bg-secondary ms-1" style="font-size:.65rem">
                {{ auth()->user()->getRoleNames()->first() }}
            </span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-light btn-sm w-100">
                <i class="bi bi-box-arrow-left me-1"></i> Logout
            </button>
        </form>
    </div>
</nav>

{{-- MAIN --}}
<div class="main-wrapper">
    {{-- TOPBAR --}}
    <div class="topbar">
        {{-- Hamburger — tampil di <992px --}}
        <button class="btn btn-sm btn-light d-lg-none flex-shrink-0" id="btn-sidebar-toggle"
                aria-label="Buka menu" style="min-width:36px;min-height:36px">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="topbar-title text-truncate">@yield('page-title', 'Dashboard')</div>

        {{-- Notifikasi stok menipis --}}
        @php
            $stokAlert = \App\Models\Product::where('is_active', true)
                ->whereColumn('stok', '<=', 'stok_minimum')
                ->count();
        @endphp
        @if($stokAlert > 0)
        <a href="{{ route('reports.stok') }}" class="btn btn-sm btn-light notif-btn flex-shrink-0"
           title="{{ $stokAlert }} produk stok menipis">
            <i class="bi bi-bell"></i>
            <span class="notif-dot"></span>
        </a>
        @endif

        <div class="text-muted small d-none d-md-block flex-shrink-0" id="session-countdown"></div>
    </div>

    {{-- Toast container --}}
    <div id="toast-container" aria-live="polite" aria-atomic="true"
         style="position:fixed;top:70px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:8px;max-width:360px;width:calc(100% - 32px)"></div>

    {{-- Flash messages --}}
    @if(session('success'))
    <script>document.addEventListener('DOMContentLoaded',function(){ showToast('success', "{{ addslashes(session('success')) }}", 5000); });</script>
    @endif
    @if(session('error'))
    <script>document.addEventListener('DOMContentLoaded',function(){ showToast('error', "{{ addslashes(session('error')) }}", 7000); });</script>
    @endif
    @if(session('warning'))
    <script>document.addEventListener('DOMContentLoaded',function(){ showToast('warning', "{{ addslashes(session('warning')) }}", 6000); });</script>
    @endif
    @if(session('info'))
    <script>document.addEventListener('DOMContentLoaded',function(){ showToast('info', "{{ addslashes(session('info')) }}", 5000); });</script>
    @endif
    @if($errors->any())
    <script>
    document.addEventListener('DOMContentLoaded',function(){
        var msgs = @json($errors->all());
        var html = '<ul style="margin:4px 0 0;padding-left:18px">' + msgs.map(function(m){ return '<li>'+m+'</li>'; }).join('') + '</ul>';
        showToast('error', '<strong>Terdapat kesalahan input:</strong>' + html, 8000);
    });
    </script>
    @endif

    {{-- CONTENT --}}
    <div class="content">
        @yield('content')
    </div>
</div>

{{-- Session Timeout Warning --}}
<div id="session-warning" class="toast align-items-center text-bg-warning border-0 shadow-lg" role="alert">
    <div class="d-flex flex-wrap">
        <div class="toast-body fw-semibold flex-grow-1">
            <i class="bi bi-clock-history me-2"></i>
            <span id="warning-text">Sesi akan berakhir.</span>
        </div>
        <button type="button" class="btn btn-sm btn-warning me-2 my-auto flex-shrink-0" onclick="resetActivity()">
            Tetap Login
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* ══════════════════════════════════════════
   SIDEBAR — mobile toggle (breakpoint 992px)
   ══════════════════════════════════════════ */
const sidebar        = document.getElementById('sidebar');
const overlay        = document.getElementById('sidebar-overlay');
const toggleBtn      = document.getElementById('btn-sidebar-toggle');

function openSidebar() {
    sidebar.classList.add('show');
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';   // prevent body scroll saat sidebar terbuka
}
function closeSidebar() {
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
    document.body.style.overflow = '';
}

toggleBtn.addEventListener('click', function() {
    sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
});

// Tutup saat klik overlay
overlay.addEventListener('click', closeSidebar);

// Tutup sidebar saat navigasi link (mobile only)
sidebar.querySelectorAll('.nav-link').forEach(function(link) {
    link.addEventListener('click', function() {
        if (window.innerWidth < 992) closeSidebar();
    });
});

// Tutup saat swipe ke kiri di sidebar
(function() {
    var startX, startY;
    sidebar.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    }, { passive: true });
    sidebar.addEventListener('touchend', function(e) {
        if (!startX) return;
        var dx = e.changedTouches[0].clientX - startX;
        var dy = Math.abs(e.changedTouches[0].clientY - startY);
        if (dx < -50 && dy < 50) closeSidebar();
        startX = startY = null;
    }, { passive: true });
})();

/* ══════════════════════════════════════════
   SESSION / AUTO-LOGOUT
   ══════════════════════════════════════════ */
const TIMEOUT_SEC   = 30 * 60;
const WARNING_SEC   = 5  * 60;
const HEARTBEAT_URL = '{{ route("heartbeat") }}';
const CSRF          = document.querySelector('meta[name=csrf-token]').content;

let idleTimer = TIMEOUT_SEC;

function formatTime(s) {
    const m = Math.floor(s / 60), sec = s % 60;
    return `${m}:${sec.toString().padStart(2,'0')}`;
}

function updateCountdown() {
    const el = document.getElementById('session-countdown');
    const wn = document.getElementById('session-warning');
    const wt = document.getElementById('warning-text');
    idleTimer--;
    if (idleTimer <= 0) { window.location.href = '{{ route("login") }}'; return; }
    if (idleTimer <= WARNING_SEC) {
        if (el) el.textContent = `⏱ ${formatTime(idleTimer)}`;
        if (wn) { wn.style.display = 'flex'; if (wt) wt.textContent = `Sesi berakhir dalam ${formatTime(idleTimer)}.`; }
    } else {
        if (el) el.textContent = '';
        if (wn) wn.style.display = 'none';
    }
}

function resetActivity() {
    idleTimer = TIMEOUT_SEC;
    document.getElementById('session-warning').style.display = 'none';
    sendHeartbeat();
}

async function sendHeartbeat() {
    try { await fetch(HEARTBEAT_URL, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } }); } catch(e) {}
}

['mousemove','keydown','click','touchstart','scroll'].forEach(evt =>
    document.addEventListener(evt, () => { idleTimer = TIMEOUT_SEC; }, { passive: true })
);
setInterval(updateCountdown, 1000);
setInterval(sendHeartbeat, 4 * 60 * 1000);
</script>

{{-- ══════════════════════════════════════
     GLOBAL TOAST
══════════════════════════════════════ --}}
<style>
.ntf-toast {
    display:flex; align-items:flex-start; gap:10px; padding:12px 14px;
    border-radius:12px; box-shadow:0 8px 28px rgba(0,0,0,.15);
    font-size:.86rem; line-height:1.5; color:#fff; position:relative;
    overflow:hidden; animation:ntfSlideIn .35s cubic-bezier(.34,1.56,.64,1) forwards;
    min-width:0; word-break:break-word;
}
.ntf-toast.ntf-hiding { animation:ntfSlideOut .3s ease-in forwards; }
.ntf-toast.ntf-success { background:linear-gradient(135deg,#059669,#047857); }
.ntf-toast.ntf-error   { background:linear-gradient(135deg,#dc2626,#b91c1c); }
.ntf-toast.ntf-warning { background:linear-gradient(135deg,#d97706,#b45309); }
.ntf-toast.ntf-info    { background:linear-gradient(135deg,#2563eb,#1d4ed8); }
.ntf-toast::after {
    content:''; position:absolute; bottom:0; left:0; height:3px;
    background:rgba(255,255,255,.4); border-radius:0 0 12px 12px;
    animation:ntfProgress var(--ntf-dur,5000ms) linear forwards;
}
.ntf-icon  { font-size:1.2rem; flex-shrink:0; line-height:1.4; }
.ntf-body  { flex:1; min-width:0; }
.ntf-title { font-weight:700; margin-bottom:2px; }
.ntf-close { flex-shrink:0; background:none; border:none; color:rgba(255,255,255,.7); cursor:pointer; padding:0 2px; font-size:1rem; line-height:1; transition:color .15s; }
.ntf-close:hover { color:#fff; }
@keyframes ntfSlideIn  { from{opacity:0;transform:translateX(60px) scale(.9)} to{opacity:1;transform:none} }
@keyframes ntfSlideOut { from{opacity:1;transform:none;max-height:100px} to{opacity:0;transform:translateX(60px) scale(.9);max-height:0} }
@keyframes ntfProgress { from{width:100%} to{width:0} }
@media (max-width:576px) {
    #toast-container { top:auto!important; bottom:16px; right:12px; left:12px; max-width:none!important; width:auto!important; }
    .ntf-toast { animation-name:ntfSlideUp; }
    @keyframes ntfSlideUp { from{opacity:0;transform:translateY(40px) scale(.9)} to{opacity:1;transform:none} }
}
</style>

<script>
function showToast(type, message, duration) {
    duration = duration || 4500;
    var icons  = { success:'bi-check-circle-fill', error:'bi-x-circle-fill', warning:'bi-exclamation-triangle-fill', info:'bi-info-circle-fill' };
    var titles = { success:'Berhasil', error:'Terjadi Kesalahan', warning:'Perhatian', info:'Informasi' };
    var c = document.getElementById('toast-container'); if (!c) return;
    var t = document.createElement('div');
    t.className = 'ntf-toast ntf-' + type;
    t.style.setProperty('--ntf-dur', duration + 'ms');
    t.innerHTML = '<i class="bi ' + icons[type] + ' ntf-icon"></i><div class="ntf-body"><div class="ntf-title">' + titles[type] + '</div><div>' + message + '</div></div><button class="ntf-close" aria-label="Tutup"><i class="bi bi-x-lg"></i></button>';
    t.querySelector('.ntf-close').onclick = function() { hideToast(t); };
    c.appendChild(t);
    var timer = setTimeout(function(){ hideToast(t); }, duration);
    t._timer = timer;
    t.addEventListener('mouseenter', function(){ clearTimeout(t._timer); });
    t.addEventListener('mouseleave', function(){ t._timer = setTimeout(function(){ hideToast(t); }, 1500); });
}
function hideToast(t) {
    if (!t || t._hidden) return; t._hidden = true;
    t.classList.add('ntf-hiding');
    setTimeout(function(){ t.parentNode && t.parentNode.removeChild(t); }, 350);
}

/* Confirm dialog */
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('submit', function(e) {
        var f = e.target; if (!f.hasAttribute('data-confirm')) return;
        e.preventDefault(); showConfirm(f.getAttribute('data-confirm'), function(){ f.submit(); });
    });
    document.querySelectorAll('a[data-confirm], button[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault(); showConfirm(el.getAttribute('data-confirm'), function(){
                el.tagName==='A' ? (window.location=el.href) : (el.form && el.form.submit());
            });
        });
    });
});

function showConfirm(message, onOk) {
    var old = document.getElementById('ntf-confirm-modal'); if (old) old.remove();
    var ov = document.createElement('div');
    ov.id = 'ntf-confirm-modal';
    ov.style.cssText = 'position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center;padding:20px';
    ov.innerHTML = '<div style="background:#fff;border-radius:16px;padding:24px;max-width:380px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.25)">' +
        '<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">' +
        '<div style="width:44px;height:44px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0">' +
        '<i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;font-size:1.2rem"></i></div>' +
        '<div><div style="font-weight:700;font-size:1rem;color:#1e293b">Konfirmasi</div>' +
        '<div style="font-size:.85rem;color:#64748b;margin-top:2px">' + message + '</div></div></div>' +
        '<div style="display:flex;gap:10px;justify-content:flex-end">' +
        '<button id="ntf-cancel-btn" style="padding:9px 20px;border-radius:8px;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;font-weight:600;cursor:pointer;font-size:.88rem">Batal</button>' +
        '<button id="ntf-ok-btn" style="padding:9px 20px;border-radius:8px;border:none;background:#dc2626;color:#fff;font-weight:600;cursor:pointer;font-size:.88rem">Ya, Hapus</button>' +
        '</div></div>';
    document.body.appendChild(ov);
    ov.querySelector('#ntf-ok-btn').onclick = function(){ ov.remove(); if(typeof onOk==='function') onOk(); };
    ov.querySelector('#ntf-cancel-btn').onclick = function(){ ov.remove(); };
    ov.addEventListener('click', function(e){ if(e.target===ov) ov.remove(); });
}

(function(){
    var s = document.createElement('style');
    s.textContent = '@keyframes ntfFadeIn{from{opacity:0}to{opacity:1}}@keyframes ntfPopIn{from{opacity:0;transform:scale(.85)}to{opacity:1;transform:scale(1)}}';
    document.head.appendChild(s);
})();
</script>

@stack('scripts')
</body>
</html>
