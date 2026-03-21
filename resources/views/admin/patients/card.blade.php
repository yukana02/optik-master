<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kartu Pasien — {{ $patient->nama }}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Arial,sans-serif;background:#f0f2f8;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px}
.wrap{display:flex;flex-direction:column;gap:16px;align-items:center}
.card{width:340px;border-radius:16px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,.15);background:#fff}
.card-header{background:#1e2a5e;color:#fff;padding:18px 20px;display:flex;align-items:center;gap:12px}
.card-header .logo{font-size:28px}
.card-header .brand{font-size:16px;font-weight:700;letter-spacing:.3px}
.card-header .brand-sub{font-size:10px;color:rgba(255,255,255,.7);margin-top:2px}
.card-body{padding:18px 20px}
.rm-badge{display:inline-block;background:#1e2a5e;color:#fff;border-radius:20px;padding:4px 14px;font-size:11px;font-weight:700;letter-spacing:.06em;margin-bottom:12px}
.patient-name{font-size:18px;font-weight:700;color:#1a1a2e;margin-bottom:4px}
.info-row{display:flex;gap:0;margin-bottom:3px}
.info-label{width:90px;font-size:11px;color:#888;flex-shrink:0}
.info-val{font-size:11px;color:#333;font-weight:600}
.bpjs-bar{background:#dbeafe;border-radius:8px;padding:8px 12px;margin-top:10px;display:flex;align-items:center;gap:8px}
.bpjs-bar .bpjs-label{font-size:10px;font-weight:700;color:#1e40af;letter-spacing:.05em}
.bpjs-bar .bpjs-num{font-size:12px;font-weight:700;color:#1e40af;margin-top:1px}
.rx-section{margin-top:10px;background:#f8f9fc;border-radius:8px;padding:10px 12px}
.rx-title{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#888;margin-bottom:8px}
.rx-table{width:100%;border-collapse:collapse}
.rx-table th{font-size:9px;color:#aaa;font-weight:600;padding:0 4px 4px;text-align:center}
.rx-table th:first-child{text-align:left}
.rx-table td{font-size:10px;color:#333;padding:2px 4px;text-align:center;font-weight:600}
.rx-table td:first-child{text-align:left;color:#666}
.card-footer-strip{background:#f0f2f8;padding:8px 20px;display:flex;justify-content:space-between;align-items:center}
.card-footer-strip span{font-size:9px;color:#888}
.btn-print{display:block;background:#1e2a5e;color:#fff;border:none;border-radius:8px;padding:10px 24px;font-size:13px;font-weight:600;cursor:pointer;text-align:center;text-decoration:none}
.btn-back{display:block;background:#e9ecef;color:#444;border:none;border-radius:8px;padding:10px 24px;font-size:13px;font-weight:600;cursor:pointer;text-align:center;text-decoration:none}
@media print{
    body{background:#fff;padding:0;display:block}
    .wrap{align-items:flex-start}
    .btn-print,.btn-back{display:none}
    .card{box-shadow:none}
}
</style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="card-header">
            <div class="logo">🕶</div>
            <div>
                <div class="brand">Optik Store</div>
                <div class="brand-sub">Kartu Pasien</div>
            </div>
        </div>
        <div class="card-body">
            <div class="rm-badge">{{ $patient->no_rm }}</div>
            <div class="patient-name">{{ $patient->nama }}</div>

            <div class="info-row"><span class="info-label">TTL</span>
                <span class="info-val">{{ $patient->tanggal_lahir ? $patient->tanggal_lahir->format('d M Y') : '-' }}
                    {{ $patient->umur ? '('.$patient->umur.' th)' : '' }}</span></div>
            <div class="info-row"><span class="info-label">Jenis Kelamin</span>
                <span class="info-val">{{ $patient->jenis_kelamin === 'L' ? 'Laki-laki' : ($patient->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</span></div>
            <div class="info-row"><span class="info-label">No. HP</span>
                <span class="info-val">{{ $patient->no_hp ?? '-' }}</span></div>

            @if($patient->no_bpjs)
            <div class="bpjs-bar">
                <div>
                    <div class="bpjs-label">BPJS Kesehatan</div>
                    <div class="bpjs-num">{{ $patient->no_bpjs }}</div>
                </div>
            </div>
            @endif

            @if($patient->latestRecord)
            @php $rx = $patient->latestRecord @endphp
            <div class="rx-section">
                <div class="rx-title">Resep Terakhir — {{ $rx->tanggal_kunjungan->format('d M Y') }}</div>
                <table class="rx-table">
                    <thead>
                        <tr><th>Mata</th><th>SPH</th><th>CYL</th><th>AXIS</th><th>ADD</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>OD (Kanan)</td>
                            <td>{{ $rx->formatResep($rx->od_sph) }}</td>
                            <td>{{ $rx->formatResep($rx->od_cyl) }}</td>
                            <td>{{ $rx->od_axis ?? '-' }}°</td>
                            <td>{{ $rx->formatResep($rx->od_add) }}</td>
                        </tr>
                        <tr>
                            <td>OS (Kiri)</td>
                            <td>{{ $rx->formatResep($rx->os_sph) }}</td>
                            <td>{{ $rx->formatResep($rx->os_cyl) }}</td>
                            <td>{{ $rx->os_axis ?? '-' }}°</td>
                            <td>{{ $rx->formatResep($rx->os_add) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        <div class="card-footer-strip">
            <span>Dicetak: {{ now()->format('d M Y') }}</span>
            <span>optikstore.com</span>
        </div>
    </div>

    <div class="d-flex gap-2 no-print" style="display:flex;gap:10px">
        <a href="{{ route('patients.show', $patient) }}" class="btn-back">← Kembali</a>
        <button onclick="window.print()" class="btn-print">🖨 Cetak Kartu</button>
    </div>
</div>
</body>
</html>
