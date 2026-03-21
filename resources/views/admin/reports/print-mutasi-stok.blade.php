<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Mutasi Stok</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Arial,sans-serif;font-size:11px;color:#1a1a1a;padding:20px}
h1{font-size:16px;font-weight:700;margin-bottom:2px}
.subtitle{color:#666;font-size:11px;margin-bottom:14px}
table{width:100%;border-collapse:collapse;font-size:10px}
thead tr{background:#1e2a5e;color:#fff}
thead th{padding:6px 8px;text-align:left}
tbody tr{border-bottom:1px solid #eee}
tbody tr:nth-child(even){background:#f9f9f9}
tbody td{padding:5px 8px}
.masuk{color:#166534;font-weight:700} .keluar{color:#b91c1c;font-weight:700}
.retur{color:#92400e;font-weight:700} .adjustment{color:#1e40af;font-weight:700}
.btn-print{position:fixed;bottom:20px;right:20px;background:#1e2a5e;color:#fff;border:none;border-radius:8px;padding:10px 20px;font-size:13px;cursor:pointer}
@media print{.btn-print{display:none}}
</style>
</head>
<body>
<h1>Laporan Mutasi Stok — Optik Store</h1>
<div class="subtitle">Dicetak: {{ now()->format('d M Y H:i') }}</div>
<table>
    <thead><tr><th>#</th><th>Tanggal</th><th>Produk</th><th>Tipe</th><th>Qty</th><th>Stok Sebelum</th><th>Stok Sesudah</th><th>Keterangan</th><th>Petugas</th></tr></thead>
    <tbody>
        @foreach($movements as $i => $m)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
            <td><strong>{{ $m->product->nama ?? '-' }}</strong></td>
            <td class="{{ $m->tipe }}">{{ ucfirst($m->tipe) }}</td>
            <td><strong>{{ $m->qty }}</strong></td>
            <td>{{ $m->stok_sebelum }}</td>
            <td>{{ $m->stok_sesudah }}</td>
            <td>{{ $m->keterangan ?? '-' }}</td>
            <td>{{ $m->user->name ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<button class="btn-print" onclick="window.print()">🖨 Cetak</button>
</body>
</html>
