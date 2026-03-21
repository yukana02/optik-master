<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice {{ $transaction->no_transaksi }}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Arial,sans-serif;font-size:12px;color:#1a1a1a;background:#fff;padding:20px}
.invoice-wrap{max-width:720px;margin:0 auto;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden}
.header{background:#1e2a5e;color:#fff;padding:24px 28px;display:flex;justify-content:space-between;align-items:flex-start}
.brand{font-size:20px;font-weight:700;letter-spacing:.5px}
.brand-sub{font-size:11px;color:rgba(255,255,255,.7);margin-top:3px}
.invoice-no{text-align:right}
.invoice-no .label{font-size:10px;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.08em}
.invoice-no .value{font-size:16px;font-weight:700;margin-top:2px}
.invoice-no .date{font-size:11px;color:rgba(255,255,255,.7);margin-top:2px}
.body{padding:24px 28px}
.info-row{display:flex;gap:24px;margin-bottom:20px}
.info-box{flex:1;background:#f8f9fc;border-radius:6px;padding:12px 14px}
.info-box label{font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:#888;display:block;margin-bottom:4px}
.info-box span{font-weight:600;font-size:13px;color:#1a1a1a}
.info-box small{display:block;color:#666;font-size:11px;margin-top:1px}
table{width:100%;border-collapse:collapse;margin-bottom:16px}
thead tr{background:#f0f2f8}
thead th{padding:8px 10px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:#666;font-weight:600}
thead th:last-child,thead th:nth-child(3),thead th:nth-child(4){text-align:right}
tbody tr{border-bottom:1px solid #f0f0f0}
tbody td{padding:9px 10px;font-size:12px;vertical-align:top}
tbody td:last-child,tbody td:nth-child(3),tbody td:nth-child(4){text-align:right}
tbody tr:last-child{border-bottom:none}
.tfoot td{padding:6px 10px;font-size:12px}
.tfoot td:last-child{text-align:right;font-weight:600}
.total-row td{border-top:2px solid #1e2a5e;padding-top:10px;font-size:14px;font-weight:700;color:#1e2a5e}
.badge{display:inline-block;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:600}
.badge-bpjs{background:#dbeafe;color:#1e40af}
.badge-umum{background:#f0fdf4;color:#166534}
.payment-box{background:#f0f8f0;border:1px solid #b6e3b6;border-radius:6px;padding:12px 14px;margin-top:12px;display:flex;justify-content:space-between;align-items:center}
.payment-box .pay-label{font-size:11px;color:#555}
.payment-box .pay-val{font-size:15px;font-weight:700;color:#166534}
.footer{text-align:center;padding:14px;border-top:1px solid #eee;color:#888;font-size:11px;background:#fafafa}
.print-btn{display:block;text-align:center;margin:20px auto 0;padding:10px 32px;background:#1e2a5e;color:#fff;border:none;border-radius:6px;font-size:14px;cursor:pointer;font-weight:600;border-radius:8px}
@media print{
    body{padding:0}
    .print-btn,.back-btn{display:none!important}
    .invoice-wrap{border:none;border-radius:0}
}
</style>
</head>
<body>
<div style="max-width:720px;margin:0 auto 16px;display:flex;gap:10px">
    <a href="{{ route('transactions.show', $transaction) }}" class="back-btn" style="padding:8px 18px;background:#f0f0f0;color:#333;text-decoration:none;border-radius:6px;font-size:13px">← Kembali</a>
    <button onclick="window.print()" class="print-btn" style="display:inline-block;margin:0">🖨 Cetak Invoice</button>
</div>

<div class="invoice-wrap">
    <div class="header">
        <div>
            <div class="brand">🕶 Optik Store</div>
            <div class="brand-sub">Jl. Contoh Alamat No. 1, Gresik, Jawa Timur</div>
            <div class="brand-sub">Telp: (031) 000-0000</div>
        </div>
        <div class="invoice-no">
            <div class="label">Invoice</div>
            <div class="value">{{ $transaction->no_transaksi }}</div>
            <div class="date">{{ $transaction->created_at->format('d M Y, H:i') }} WIB</div>
        </div>
    </div>

    <div class="body">
        <div class="info-row">
            <div class="info-box">
                <label>Pasien</label>
                @if($transaction->patient)
                    <span>{{ $transaction->patient->nama }}</span>
                    <small>No. RM: {{ $transaction->patient->no_rm }}</small>
                    @if($transaction->tipe_pasien === 'bpjs')
                        <small><span class="badge badge-bpjs">BPJS: {{ $transaction->no_bpjs }}</span></small>
                    @else
                        <small><span class="badge badge-umum">Pasien Umum</span></small>
                    @endif
                @else
                    <span>Pasien Umum</span>
                    <small>Tanpa data pasien</small>
                @endif
            </div>
            <div class="info-box">
                <label>Kasir</label>
                <span>{{ $transaction->kasir->name ?? '-' }}</span>
                <small>Metode: {{ ucfirst($transaction->metode_bayar) }}</small>
                <small>Status: <strong>{{ strtoupper($transaction->status) }}</strong></small>
            </div>
            @if($transaction->medicalRecord)
            <div class="info-box">
                <label>Rekam Medis</label>
                <span>Kunjungan {{ $transaction->medicalRecord->tanggal_kunjungan->format('d M Y') }}</span>
                <small>OD: {{ $transaction->medicalRecord->formatResep($transaction->medicalRecord->od_sph) }}</small>
                <small>OS: {{ $transaction->medicalRecord->formatResep($transaction->medicalRecord->os_sph) }}</small>
            </div>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Diskon</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        {{ $item->nama_produk }}
                        @if($item->product)
                            <br><small style="color:#888">{{ $item->product->kode_produk }}</small>
                        @endif
                    </td>
                    <td>{{ $item->qty }}</td>
                    <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td>{{ $item->diskon > 0 ? 'Rp '.number_format($item->diskon,0,',','.') : '-' }}</td>
                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="tfoot">
                <tr><td colspan="5">Subtotal</td><td>Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td></tr>
                @if($transaction->diskon_nominal > 0)
                <tr><td colspan="5">Diskon ({{ $transaction->diskon_persen > 0 ? $transaction->diskon_persen.'%' : 'nominal' }})</td>
                    <td>- Rp {{ number_format($transaction->diskon_nominal, 0, ',', '.') }}</td></tr>
                @endif
                @if($transaction->subsidi_bpjs > 0)
                <tr><td colspan="5">Subsidi BPJS</td>
                    <td>- Rp {{ number_format($transaction->subsidi_bpjs, 0, ',', '.') }}</td></tr>
                @endif
                <tr class="total-row"><td colspan="5">TOTAL</td><td>Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</td></tr>
            </tfoot>
        </table>

        <div class="payment-box">
            <div>
                <div class="pay-label">Dibayar ({{ ucfirst($transaction->metode_bayar) }})</div>
                <div class="pay-label" style="margin-top:3px">Kembalian</div>
            </div>
            <div style="text-align:right">
                <div class="pay-val">Rp {{ number_format($transaction->bayar, 0, ',', '.') }}</div>
                <div style="font-size:13px;color:#555;font-weight:600;margin-top:3px">
                    Rp {{ number_format($transaction->kembalian, 0, ',', '.') }}
                </div>
            </div>
        </div>

        @if($transaction->catatan)
        <div style="margin-top:14px;padding:10px 14px;background:#fffbeb;border-left:3px solid #f59e0b;border-radius:0 6px 6px 0;font-size:11px;color:#92400e">
            <strong>Catatan:</strong> {{ $transaction->catatan }}
        </div>
        @endif
    </div>

    <div class="footer">
        Terima kasih telah berbelanja di Optik Store · Barang yang sudah dibeli tidak dapat dikembalikan
    </div>
</div>
</body>
</html>
