@php
    $copies = $copies ?? 1;
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bon Fasetan - {{ $transaction->no_transaksi }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 8pt;
            color: #000;
            background: #fff;
            width: 80mm;
            padding: 3mm 4mm 6mm;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
            margin-bottom: 3mm;
            border-bottom: 1px dashed #000;
            padding-bottom: 3mm;
        }

        .header .brand {
            font-size: 16pt;
            font-weight: 900;
            font-family: Arial, Helvetica, sans-serif;
            letter-spacing: 1px;
            line-height: 1;
        }

        .header .brand .optik {
            font-size: 10pt;
            font-weight: 400;
            display: block;
            letter-spacing: 0;
        }

        .header .tagline {
            font-family: 'Georgia', serif;
            font-style: italic;
            font-size: 7pt;
            margin-top: 0.5mm;
        }

        .header .address {
            font-size: 6.5pt;
            margin-top: 1.5mm;
            line-height: 1.4;
        }

        /* ===== DATA ROWS ===== */
        .info-section {
            margin-bottom: 3mm;
            border-bottom: 1px dashed #000;
            padding-bottom: 3mm;
        }

        .info-row {
            display: flex;
            line-height: 1.6;
        }

        .info-label {
            width: 24mm;
            flex-shrink: 0;
        }

        .info-sep {
            width: 3mm;
            text-align: center;
            flex-shrink: 0;
        }

        .info-val {
            flex: 1;
        }

        /* ===== PRODUK ===== */
        .product-section {
            margin-bottom: 3mm;
            border-bottom: 1px dashed #000;
            padding-bottom: 3mm;
        }

        /* ===== HARGA TABLE ===== */
        .price-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
            border-bottom: 1px dashed #000;
            padding-bottom: 2mm;
        }

        .price-table td {
            padding: 1pt 0;
            vertical-align: top;
        }

        .price-table .lbl {
            width: auto;
        }

        .price-table .amt {
            text-align: right;
            width: 22mm;
            font-weight: 700;
        }

        .price-table .total-row td {
            border-top: 1px solid #000;
            font-weight: 900;
            padding-top: 2pt;
        }

        /* ===== REFRAKSI ===== */
        .ref-section {
            margin-bottom: 3mm;
            border-bottom: 1px dashed #000;
            padding-bottom: 3mm;
        }

        .ref-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }

        .ref-table th {
            text-align: center;
            font-weight: 700;
            padding: 1pt 2pt;
            border-bottom: 1px solid #000;
        }

        .ref-table th:first-child {
            text-align: left;
            width: 10mm;
        }

        .ref-table td {
            text-align: center;
            padding: 2pt 2pt;
        }

        .ref-table td:first-child {
            text-align: left;
            font-weight: 700;
        }

        /* ===== FOOTER ===== */
        .thanks {
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14pt;
            font-weight: 900;
            margin: 4mm 0 3mm;
            letter-spacing: 1px;
        }

        .notes {
            font-size: 6pt;
            line-height: 1.5;
            color: #333;
        }

        .notes b {
            font-weight: 700;
            color: #000;
        }

        .notes ol {
            margin: 0;
            padding-left: 3mm;
        }

        .notes ol li {
            margin-bottom: 1pt;
        }

        @media screen {
            body {
                margin: 10mm auto;
                box-shadow: 0 2px 12px rgba(0, 0, 0, .2);
            }
        }
    </style>
</head>

<body onload="window.print()">
    @for($i = 0; $i < $copies; $i++)
        <div class="print-container">
            {{-- ===== HEADER ===== --}}
            <div class="header">
        <div class="brand">
            <span class="optik">OPTIK</span>
            PERKASA
        </div>
        <div class="tagline">Solusi Penglihatan Anda</div>
        <div class="address">
            Jl KH Agus Salim No 42 Bekasi<br>
            Hp / WA : 0621-80000-590
        </div>
    </div>

    {{-- ===== INFO PESANAN ===== --}}
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">No Pesanan</span>
            <span class="info-sep">:</span>
            <span class="info-val">{{ $transaction->no_transaksi }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal</span>
            <span class="info-sep">:</span><br>
            <span class="info-val">
                {{ $transaction->created_at->format('d/m/y') }}
                @if($transaction->tgl_selesai_janji)
                    &nbsp;&nbsp;&nbsp;Selesai :
                    {{ \Carbon\Carbon::parse($transaction->tgl_selesai_janji)->format('d/m/y') }}
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Pelanggan</span>
            <span class="info-sep">:</span>
            <span class="info-val">{{ $transaction->patient->nama ?? ($transaction->nama_pasien ?? 'UMUM') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">No Hp</span>
            <span class="info-sep">:</span>
            <span class="info-val">{{ $transaction->patient->no_hp ?? ($transaction->telp_pasien ?? '-') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">No Bpjs</span>
            <span class="info-sep">:</span>
            <span class="info-val">{{ $transaction->no_bpjs ?? ($transaction->patient->no_bpjs ?? '-') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Resep</span>
            <span class="info-sep">:</span>
            <span class="info-val">{{ $transaction->asal_resep ?? '-' }}</span>
        </div>
    </div>

    {{-- ===== PRODUK ===== --}}
    <div class="product-section">
        @php
            $items = $transaction->items;
        @endphp
        @foreach($items as $item)
            <div class="info-row">
                <span class="info-label">
                    @if($item->product && $item->product->category)
                        {{ $item->product->category->name }}
                    @else
                        {{ stripos($item->nama_produk, 'Lensa') !== false ? 'Lensa' : (stripos($item->nama_produk, 'Frame') !== false ? 'Frame' : 'Item') }}
                    @endif
                </span>
                <span class="info-sep">:</span>
                <span class="info-val">{{ $item->nama_produk }}</span>
            </div>
        @endforeach
    </div>

    {{-- ===== HARGA ===== --}}
    @php
        $harga = $transaction->harga_jual ?? $transaction->total_harga ?? 0;
        $potongan = $transaction->potongan ?? $transaction->diskon_nominal ?? 0;
        $dp = $transaction->dp ?? $transaction->bayar ?? 0;
        $sisa = max(0, $harga - $potongan - $dp);
    @endphp
    <table class="price-table">
        <tr>
            <td class="lbl">Harga Jual</td>
            <td class="amt">{{ number_format($harga, 0, ',', '.') }}</td>
        </tr>
        @if($potongan > 0)
            <tr>
                <td class="lbl">Potongan BPJS</td>
                <td class="amt">{{ number_format($potongan, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr>
            <td class="lbl">Uang Muka</td>
            <td class="amt">{{ number_format($dp, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td class="lbl">Sisa</td>
            <td class="amt">{{ number_format($sisa, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- ===== REFRAKSI ===== --}}
    <div class="ref-section">
        <table class="ref-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Sph</th>
                    <th>Cyl</th>
                    <th>Axis</th>
                    <th>Add</th>
                    <th>Mpd</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>OD</td>
                    <td>{{ $transaction->od_sph ?: '-' }}</td>
                    <td>{{ $transaction->od_cyl ?: '-' }}</td>
                    <td>{{ $transaction->od_axis ?: '-' }}</td>
                    <td>{{ $transaction->od_add ?: '-' }}</td>
                    <td>{{ $transaction->od_mpd ?: '-' }}</td>
                </tr>
                <tr>
                    <td>OS</td>
                    <td>{{ $transaction->os_sph ?: '-' }}</td>
                    <td>{{ $transaction->os_cyl ?: '-' }}</td>
                    <td>{{ $transaction->os_axis ?: '-' }}</td>
                    <td>{{ $transaction->os_add ?: '-' }}</td>
                    <td>{{ $transaction->os_mpd ?: '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ===== TERIMA KASIH ===== --}}
    <div class="thanks">TERIMA KASIH</div>

    {{-- ===== CATATAN ===== --}}
    <div class="notes">
        <b>Note :</b>
        <ol>
            <li>Barang yg sudah dibeli/dipesan tidak dapat dikembalikan atau dibatalkan</li>
            <li>Barang yg dibeli/dipesan jika tidak diambil dalam rentang 2 bulan tidak lagi menjadi tanggung jawab toko
            </li>
            <li>Pemasangan lensa pada frame milik Pelanggan, Segala resiko kerusakan (cacat, patah dll) pada kacamata
                saat pemasangan lensa Menjadi tanggung jawab pelanggan dan diluar tanggung jawab optik</li>
        </ol>
    </div>
        </div>
        @if($i < $copies - 1)
            <div style="page-break-after: always; height:1px;"></div>
        @endif
    @endfor
</body>

</html>