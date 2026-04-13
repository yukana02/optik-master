<style>
    /* =============================================
       ROOT & BASE
    ============================================= */
    :root {
        --primary: #0d6efd;
        --primary-dark: #0b5ed7;
        --success: #198754;
        --danger: #dc3545;
        --warning: #ffc107;
        --glass-bg: rgba(255, 255, 255, 0.75);
        --glass-border: rgba(255, 255, 255, 0.35);
        --gradient-blue: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
        --shadow-soft: 0 8px 32px rgba(31, 38, 135, 0.08);
        --radius-card: 18px;
        --radius-input: 10px;
    }

    body {
        background: #f0f2f5;
        font-family: 'Inter', system-ui, sans-serif;
    }

    /* WIZARD STEP INDICATOR */
    .wizard-steps {
        display: flex;
        align-items: center;
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-card);
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-soft);
        overflow-x: auto;
        gap: 0;
    }

    .wizard-step {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 130px;
        padding: 8px 10px;
        border-radius: 12px;
        cursor: default;
        transition: background 0.2s;
        user-select: none;
    }

    .wizard-step.is-done { cursor: pointer; }
    .wizard-step.is-done:hover { background: rgba(13, 110, 253, 0.06); }

    .ws-num {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 700; flex-shrink: 0;
        transition: all 0.3s; border: 2px solid #dee2e6;
        background: #fff; color: #adb5bd;
    }

    .ws-title { font-size: 13px; font-weight: 600; color: #6c757d; }
    .ws-sub { font-size: 11px; color: #adb5bd; }

    .wizard-step.is-active .ws-num {
        background: var(--primary); border-color: var(--primary); color: #fff;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.35);
    }
    .wizard-step.is-active .ws-title { color: #212529; }
    .wizard-step.is-active .ws-sub { color: #6c757d; }

    .wizard-step.is-done .ws-num { background: var(--success); border-color: var(--success); color: #fff; }
    .wizard-step.is-done .ws-title { color: var(--success); }

    .ws-connector {
        flex: 0 0 28px; height: 2px; background: #dee2e6;
        transition: background 0.3s; border-radius: 2px; margin: 0 2px;
    }
    .ws-connector.is-done { background: var(--success); }

    /* STEP PANELS */
    .wizard-panel { display: none; animation: fadeSlide 0.25s ease; }
    .wizard-panel.is-active { display: block; }
    @keyframes fadeSlide {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* CARDS */
    .glass-card {
        background: var(--glass-bg); backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border); border-radius: var(--radius-card);
        box-shadow: var(--shadow-soft); margin-bottom: 1.25rem;
    }
    .card-header-section {
        font-size: 0.78rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.8px; color: var(--primary); margin-bottom: 1rem;
        display: flex; align-items: center; gap: 8px;
    }

    /* FORM ELEMENTS */
    .form-label { font-size: 0.72rem; font-weight: 600; color: #6c757d; margin-bottom: 4px; }
    .form-control-sm, .form-select-sm {
        border-radius: var(--radius-input); border: 1px solid #dee2e6;
        padding: 0.45rem 0.7rem; background: rgba(255, 255, 255, 0.9);
        font-size: 0.82rem; transition: border-color 0.15s, box-shadow 0.15s;
    }

    /* CART */
    .cart-panel {
        background: var(--glass-bg); backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border); border-radius: var(--radius-card);
        box-shadow: var(--shadow-soft); position: sticky; top: 1rem;
    }
    .cart-item-row { display: flex; align-items: center; gap: 8px; padding: 8px 0; border-bottom: 1px solid rgba(0,0,0,0.05); }
    .cart-type-badge { font-size: 0.62rem; padding: 2px 7px; border-radius: 20px; font-weight: 600; }
    .ct-frame { background: #ede9fe; color: #5b21b6; }
    .ct-lensa { background: #d1fae5; color: #065f46; }

    /* FINANCE */
    .finance-card { background: var(--gradient-blue); border-radius: var(--radius-card); padding: 1.5rem; color: #fff; }
    .sisa-field { background: rgba(220, 53, 69, 0.25) !important; border-color: rgba(220, 53, 69, 0.5) !important; }

    /* ACTION BAR */
    .global-action-bar {
        background: var(--glass-bg); backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border); border-radius: var(--radius-card);
        padding: 0.75rem 1.25rem; box-shadow: var(--shadow-soft); margin-bottom: 1.5rem;
        display: flex; flex-wrap: wrap; gap: 8px; align-items: center; justify-content: space-between;
    }
    .btn-action {
        border-radius: 10px; font-weight: 600; font-size: 0.78rem; padding: 0.5rem 1rem;
        display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s;
    }

    /* AUTOCOMPLETE */
    .ac-dropdown {
        position: absolute; top: 100%; left: 0; right: 0; z-index: 1050;
        background: #fff; border: 1px solid #dee2e6; border-radius: 0 0 10px 10px;
        max-height: 200px; overflow-y: auto; box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    .ac-item { padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f8f8f8; font-size: 0.78rem; }
    .ac-item:hover { background: #f0f4ff; color: var(--primary); }

    /* ERROR */
    .step-error-msg {
        background: #fff3cd; border: 1px solid #ffc107; border-radius: 10px;
        padding: 10px 14px; font-size: 0.78rem; color: #664d03; margin-bottom: 12px;
        display: none; align-items: flex-start; gap: 8px;
    }
    .step-error-msg.show { display: flex; }

    /* PRINT */
    #printFrame { display: none; }
</style>
