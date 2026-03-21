<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Penjualan</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Arial,sans-serif;font-size:11px;color:#1a1a1a;padding:20px}
h1{font-size:16px;font-weight:700;margin-bottom:2px}
.subtitle{color:#666;font-size:11px;margin-bottom:14px}
.summary{display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap}
.sum-box{background:#f0f2f8;border-radius:6px;padding:10px 14px;flex:1;min-width:110px}
.sum-box label{font-size:9px;text-transform:uppercase;letter-spacing:.06em;color:#888;display:block;margin-bottom:3px}
.sum-box span{font-size:14px;font-weight:700;color:#1e2a5e}
.badge{display:inline-block;padding:2px 8px;border-radius:12px;font-size:9px;font-weight:700}
.badge-bpjs{background:#dcfce7;color:#166534}
.badge-umum{background:#f1f5f9;color:#475569}
table{width:100%;border-collapse:collapse;font-size:10px}
thead tr{background:#1e2a5e;color:#fff}
thead th{padding:6px 8px;text-align:left}
tbody tr{border-bottom:1px solid #eee}
tbody tr:nth-child(even){background:#f9f9f9}
tbody td{padding:5px 8px;vertical-align:top}
tfoot td{font-weight:700;border-top:2px solid #1e2a5e;padding:6px 8px;background:#f0f2f8}
.footer{margin-top:20px;text-align:center;color:#999;font-size:9px}
.btn-print{position:fixed;bottom:20px;right:20px;background:#1e2a5e;color:#fff;border:none;
            border-radius:8px;padding:10px 20px;font-size:13px;cursor:pointer;font-weight:600}
@media print{.btn-print{display:none}}
</style>
</head>
<body>
<h1>Laporan Penjualan — Optik Store</h1>
<div class="subtitle">
    Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
    @if(!empty($tipePasien))
        &nbsp;|&nbsp; Filter:
        @if($tipePasien === 'bpjs')
            <span class="badge badge-bpjs">BPJS</span>
        @else
            <span class="badge badge-umum">Umum</span>
        @endif
    @endif
    &nbsp;|&nbsp; Dicetak: {{ now()->format('d M Y H:i') }}
</div>

<div class="summary">
    <div class="sum-box"><label>Total Transaksi</label><span>{{ number_format($summary['total_transaksi']) }}</span></div>
    <div class="sum-box"><label>Total Omzet</label><span>Rp {{ number_format($summary['total_omzet'],0,',','.') }}</span></div>
    <div class="sum-box"><label>Total Diskon</label><span>Rp {{ number_format($summary['total_diskon'],0,',','.') }}</span></div>
    <div class="sum-box"><label>Subsidi BPJS</label><span>Rp {{ number_format($summary['total_subsidi'],0,',','.') }}</span></div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th><th>No. Transaksi</th><th>Tanggal</th><th>Pasien</th>
            <th>Tipe</th><th>Items</th><th>Diskon</th><th>Subsidi BPJS</th>
            <th>Total Bayar</th><th>Metode</th><th>Kasir</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $i => $trx)
        <tr>
            <td>{{ $i+1 }}</td>
            <td><strong>{{ $trx->no_transaksi }}</strong></td>
            <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $trx->patient->nama ?? 'Umum' }}</td>
            <td>
                @if(($trx->tipe_pasien ?? 'umum') === 'bpjs')
                    <span class="badge badge-bpjs">BPJS</span>
                @else
                    <span class="badge badge-umum">Umum</span>
                @endif
            </td>
            <td>{{ $trx->items->sum('qty') }}</td>
            <td>{{ $trx->diskon_nominal > 0 ? 'Rp '.number_format($trx->diskon_nominal,0,',','.') : '-' }}</td>
            <td>{{ ($trx->subsidi_bpjs??0) > 0 ? 'Rp '.number_format($trx->subsidi_bpjs,0,',','.') : '-' }}</td>
            <td><strong>Rp {{ number_format($trx->total_bayar,0,',','.') }}</strong></td>
            <td>{{ ucfirst($trx->metode_bayar) }}</td>
            <td>{{ $trx->kasir->name ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8" style="text-align:right">TOTAL OMZET</td>
            <td>Rp {{ number_format($summary['total_omzet'],0,',','.') }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
<div class="footer">Optik Store · Dicetak {{ now()->format('d M Y H:i') }}</div>
<button class="btn-print" onclick="window.print()">🖨 Cetak</button>
</body>
</html>
