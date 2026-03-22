<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Stok</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Arial,sans-serif;font-size:11px;color:#1a1a1a;padding:20px}
h1{font-size:16px;font-weight:700;margin-bottom:2px}
.subtitle{color:#666;font-size:11px;margin-bottom:14px}
.summary{display:flex;gap:16px;margin-bottom:16px;flex-wrap:wrap}
.sum-box{background:#f0f2f8;border-radius:6px;padding:10px 14px;flex:1;min-width:120px}
.sum-box label{font-size:9px;text-transform:uppercase;letter-spacing:.06em;color:#888;display:block;margin-bottom:3px}
.sum-box span{font-size:14px;font-weight:700;color:#1e2a5e}
table{width:100%;border-collapse:collapse;font-size:10px}
thead tr{background:#1e2a5e;color:#fff}
thead th{padding:6px 8px;text-align:left}
tbody tr{border-bottom:1px solid #eee}
tbody tr:nth-child(even){background:#f9f9f9}
tbody td{padding:5px 8px}
.st-aman{color:#166534;font-weight:600} .st-menipis{color:#92400e;font-weight:600} .st-habis{color:#991b1b;font-weight:600}
.btn-print{position:fixed;bottom:20px;right:20px;background:#1e2a5e;color:#fff;border:none;border-radius:8px;padding:10px 20px;font-size:13px;cursor:pointer}
@media print{.btn-print{display:none}}
</style>
</head>
<body>
<h1>Laporan Stok — Optik Store</h1>
<div class="subtitle">Dicetak: {{ now()->format('d M Y H:i') }}</div>
<div class="summary">
    <div class="sum-box"><label>Total Produk</label><span>{{ number_format($summary['total_produk']) }}</span></div>
    <div class="sum-box"><label>Stok Menipis</label><span style="color:#b45309">{{ $summary['stok_menipis'] }}</span></div>
    <div class="sum-box"><label>Stok Habis</label><span style="color:#b91c1c">{{ $summary['stok_habis'] }}</span></div>
    <div class="sum-box"><label>Nilai Total Stok</label><span>Rp {{ number_format($summary['nilai_stok'],0,',','.') }}</span></div>
</div>
<table>
    <thead><tr><th>#</th><th>Kode</th><th>Nama Produk</th><th>Kategori</th><th>Stok</th><th>Min.</th><th>Status</th><th>Harga Jual</th><th>Nilai Stok</th></tr></thead>
    <tbody>
        @foreach($products as $i => $p)
        @php
            if($p->stok == 0) { $stClass='st-habis'; $stLabel='Habis'; }
            elseif($p->stok_menipis) { $stClass='st-menipis'; $stLabel='Menipis'; }
            else { $stClass='st-aman'; $stLabel='Aman'; }
        @endphp
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $p->kode_produk }}</td>
            <td><strong>{{ $p->nama }}</strong></td>
            <td>{{ $p->category->nama ?? '-' }}</td>
            <td><strong>{{ $p->stok }}</strong></td>
            <td>{{ $p->stok_minimum }}</td>
            <td class="{{ $stClass }}">{{ $stLabel }}</td>
            <td>Rp {{ number_format($p->harga_jual,0,',','.') }}</td>
            <td>Rp {{ number_format($p->stok * $p->harga_beli,0,',','.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<button class="btn-print" onclick="window.print()">🖨 Cetak</button>
</body>
</html>
