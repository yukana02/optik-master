<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir BPJS - {{ $transaction->no_transaksi }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 15mm 15mm 20mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ===== HEADER BPJS ===== */
        .bpjs-header {
            text-align: center;
            margin-bottom: 6pt;
        }

        .bpjs-header .bpjs-title {
            font-size: 18pt;
            font-weight: 900;
            letter-spacing: 2px;
            color: #007b3b;
            font-style: italic;
        }

        .bpjs-header .bpjs-full {
            font-size: 9pt;
            font-weight: 700;
            letter-spacing: 1px;
            color: #000;
            margin-top: 1pt;
        }

        .bpjs-header .bpjs-kantor {
            font-size: 9pt;
            font-weight: 600;
            color: #000;
        }

        .bpjs-separator {
            border-top: 2.5pt solid #000;
            border-bottom: 1pt solid #000;
            height: 4pt;
            margin: 6pt 0;
        }

        /* ===== NAMA OPTIK ===== */
        .nama-optik {
            font-size: 12pt;
            font-weight: 700;
            border: 1.5pt solid #000;
            padding: 4pt 8pt;
            display: inline-block;
            margin-bottom: 10pt;
        }

        /* ===== JUDUL SURAT ===== */
        .judul-surat {
            text-align: center;
            font-size: 14pt;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 8pt;
            text-decoration: underline;
        }

        /* ===== INTRO TEXT ===== */
        .intro-text {
            font-size: 10pt;
            margin-bottom: 12pt;
            line-height: 1.5;
        }

        /* ===== DATA ROWS ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
        }

        .data-table tr td {
            padding: 3pt 2pt;
            vertical-align: top;
        }

        .data-table tr td:first-child {
            width: 38%;
            font-weight: 400;
        }

        .data-table tr td.colon {
            width: 10pt;
            font-weight: 700;
            text-align: center;
        }

        .data-table tr td.value {
            border-bottom: 1pt solid #000;
            min-height: 14pt;
            padding-bottom: 1pt;
        }

        /* Refraction sub-rows */
        .refraction-inline {
            display: inline-flex;
            gap: 4pt;
            flex-wrap: wrap;
            align-items: baseline;
        }

        .refraction-inline .rf-part {
            display: inline-flex;
            align-items: baseline;
            gap: 2pt;
            font-size: 10pt;
        }

        .refraction-inline .rf-label {
            font-weight: 700;
            font-size: 9pt;
        }

        .underline-field {
            display: inline-block;
            border-bottom: 1pt solid #000;
            min-width: 45pt;
            padding-bottom: 1pt;
            text-align: center;
        }

        .underline-field-wide {
            display: inline-block;
            border-bottom: 1pt solid #000;
            min-width: 80pt;
            padding-bottom: 1pt;
        }

        /* ===== DISCLAIMER ===== */
        .disclaimer {
            margin-top: 14pt;
            font-size: 10pt;
        }

        /* ===== TTD SECTION ===== */
        .ttd-section {
            margin-top: 20pt;
            display: flex;
            justify-content: space-between;
        }

        .ttd-block {
            width: 45%;
            font-size: 10pt;
        }

        .ttd-block .kota-tanggal {
            margin-bottom: 4pt;
        }

        .ttd-block .ttd-label {
            font-weight: 700;
            margin-bottom: 30pt;
        }

        .ttd-block .ttd-name {
            font-weight: 700;
            border-top: 1pt solid #000;
            padding-top: 2pt;
        }

        /* Screen preview */
        @media screen {
            body {
                background: #ccc;
                padding: 10mm;
                max-width: 210mm;
                margin: 0 auto;
            }

            .page {
                background: #fff;
                padding: 15mm 15mm 15mm 20mm;
                box-shadow: 0 2px 12px rgba(0, 0, 0, .3);
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="page">

        {{-- ===== HEADER ===== --}}
        <div class="bpjs-header">
            <div class="bpjs-title">BPJS KESEHATAN</div>
            <div class="bpjs-full">BADAN PENYELENGGARA JAMINAN SOSIAL</div>
            <div class="bpjs-kantor">KANTOR CABANG BEKASI</div>
        </div>

        <div class="bpjs-separator"></div>

        {{-- ===== NAMA OPTIK ===== --}}
        <div>
            <span class="nama-optik">NAMA OPTIK : PERKASA OPTIKAL</span>
        </div>

        {{-- ===== JUDUL ===== --}}
        <div class="judul-surat">SURAT KETERANGAN</div>

        {{-- ===== INTRO ===== --}}
        @php
            $namaPasien  = $transaction->nama_pasien ?? ($transaction->patient->nama ?? '-');
            $noBpjs      = $transaction->no_bpjs ?? ($transaction->patient->no_bpjs ?? '-');
            $alamat      = $transaction->alamat_pasien ?? ($transaction->patient->alamat ?? '-');
            $tglFaktur   = $transaction->tgl_faktur
                ? \Carbon\Carbon::parse($transaction->tgl_faktur)->translatedFormat('d F Y')
                : \Carbon\Carbon::now()->translatedFormat('d F Y');

            // Refraction
            $odSph  = $transaction->od_sph  ?: '-';
            $odCyl  = $transaction->od_cyl  ?: '-';
            $odAxis = $transaction->od_axis ?: '-';
            $odAdd  = $transaction->od_add  ?: '-';
            $odMpd  = $transaction->od_mpd  ?: '-';
            $osSph  = $transaction->os_sph  ?: '-';
            $osCyl  = $transaction->os_cyl  ?: '-';
            $osAxis = $transaction->os_axis ?: '-';
            $osAdd  = $transaction->os_add  ?: '-';
            $osMpd  = $transaction->os_mpd  ?: '-';

            // Items
            $lensa  = $transaction->lensa ?: null;
            $frame  = null;
            $hargaLensa  = 0;
            $hargaFrame  = 0;

            foreach ($transaction->items as $item) {
                $namaItem = strtolower($item->nama_produk);
                if (str_contains($namaItem, 'lensa') || str_contains($namaItem, 'lens')) {
                    if (!$lensa) $lensa = $item->nama_produk;
                    $hargaLensa += $item->subtotal;
                } elseif (str_contains($namaItem, 'frame') || str_contains($namaItem, 'kacamata') || str_contains($namaItem, 'gelas')) {
                    $frame = ($transaction->kode_frame ? $transaction->kode_frame . ' ' : '') . $item->nama_produk;
                    $hargaFrame += $item->subtotal;
                }
            }

            // Fallback jika tidak terdeteksi dari kata kunci
            if (!$lensa && $transaction->lensa) $lensa = $transaction->lensa;
            if ($hargaLensa == 0 && $hargaFrame == 0) {
                // Split harga_jual 50/50 jika tidak bisa dibedakan
                $hargaLensa = $transaction->harga_jual / 2;
                $hargaFrame = $transaction->harga_jual / 2;
            }

            $totalBiaya   = $transaction->harga_jual ?: ($hargaLensa + $hargaFrame);
            $potonganBpjs = $transaction->potongan_bpjs ?: $transaction->potongan ?: 0;
            $sisaBayar    = max(0, $totalBiaya - $potonganBpjs);
        @endphp

        <div class="intro-text">
            Telah menerima resep kacamata yang telah dilegalisir oleh BPJS Kesehatan Kantor Cabang
            Bekasi.
        </div>

        {{-- ===== DATA TABLE ===== --}}
        <table class="data-table">
            <tbody>
                <tr>
                    <td>Nama Penderita</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $namaPasien }}
                        @if($transaction->patient && $transaction->patient->jenis_kelamin)
                            &nbsp;&nbsp;&nbsp; P/VS/A
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Untuk dibuat kacamata</td>
                    <td class="colon">:</td>
                    <td class="value">
                        <div class="refraction-inline">
                            <span class="rf-part">
                                <span class="rf-label">OD.SPH</span>
                                <span class="underline-field">{{ $odSph }}</span>
                            </span>
                            <span class="rf-part">
                                <span class="rf-label">OD.CYL</span>
                                <span class="underline-field">{{ $odCyl }}</span>
                            </span>
                            <span class="rf-part">
                                <span>X</span>
                                <span class="underline-field">{{ $odAxis }}</span>
                            </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Dengan Ukuran</td>
                    <td class="colon">:</td>
                    <td class="value">
                        <div class="refraction-inline">
                            <span class="rf-part">
                                <span class="rf-label">OS.SPH</span>
                                <span class="underline-field">{{ $osSph }}</span>
                            </span>
                            <span class="rf-part">
                                <span class="rf-label">OS.CYL</span>
                                <span class="underline-field">{{ $osCyl }}</span>
                            </span>
                            <span class="rf-part">
                                <span>X</span>
                                <span class="underline-field">{{ $osAxis }}</span>
                            </span>
                        </div>
                        <div class="refraction-inline" style="margin-top: 3pt;">
                            <span class="rf-part">
                                <span class="rf-label">ADD +</span>
                                <span class="underline-field">{{ $odAdd !== '-' ? $odAdd : $osAdd }}</span>
                            </span>
                            <span class="rf-part">
                                <span class="rf-label" style="margin-left: 8pt;">DV</span>
                                <span class="underline-field">{{ $odMpd !== '-' ? $odMpd : $osMpd }}</span>
                            </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Lensa</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $lensa ?: '-' }}</td>
                </tr>
                <tr>
                    <td>Frame</td>
                    <td class="colon">:</td>
                    <td class="value">
                        @if($frame)
                            {{ $frame }}
                        @elseif($transaction->kode_frame)
                            {{ $transaction->kode_frame }}
                        @elseif($transaction->nama_produk)
                            {{ $transaction->nama_produk }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Harga Lensa</td>
                    <td class="colon">:</td>
                    <td class="value">Rp. &nbsp; {{ number_format($hargaLensa, 0, ',', '.') }} *</td>
                </tr>
                <tr>
                    <td>Harga Frame</td>
                    <td class="colon">:</td>
                    <td class="value">Rp. &nbsp; {{ number_format($hargaFrame, 0, ',', '.') }} *</td>
                </tr>
                <tr>
                    <td>Total Biaya</td>
                    <td class="colon">:</td>
                    <td class="value">Rp. &nbsp; {{ number_format($totalBiaya, 0, ',', '.') }} *</td>
                </tr>
                <tr>
                    <td>Yang diganti BPJS Kesehatan</td>
                    <td class="colon">:</td>
                    <td class="value">Rp. &nbsp; {{ number_format($potonganBpjs, 0, ',', '.') }} *</td>
                </tr>
                <tr>
                    <td>Selisih yang dibayar</td>
                    <td class="colon">:</td>
                    <td class="value">Rp. &nbsp; {{ number_format($sisaBayar, 0, ',', '.') }} *</td>
                </tr>
            </tbody>
        </table>

        {{-- ===== BIODATA ===== --}}
        <table class="data-table" style="margin-top: 10pt;">
            <tbody>
                <tr>
                    <td>Nama Pemegang Kartu</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $namaPasien }}</td>
                </tr>
                <tr>
                    <td>Nomor Kartu BPJS Kesehatan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $noBpjs }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $alamat }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ===== DISCLAIMER ===== --}}
        <div class="disclaimer">
            Telah menerima kacamata sesuai dengan ukurannya.
        </div>

        {{-- ===== TTD ===== --}}
        <div class="ttd-section">
            <div class="ttd-block">
                <div class="kota-tanggal">&nbsp;</div>
                <div class="ttd-label">Yang membuat Kacamata</div>
                <div class="ttd-label">Perkasa Optikal</div>
                <br><br><br>
                <div class="ttd-name">Al Sahlah, Amd.RO</div>
            </div>

            <div class="ttd-block" style="text-align: right;">
                <div class="kota-tanggal">Bekasi, {{ $tglFaktur }}</div>
                <div class="ttd-label">Tanda Tangan</div>
                <div class="ttd-label">Pemegang Kartu</div>
                <br><br><br>
                <div class="ttd-name">{{ $namaPasien }}</div>
            </div>
        </div>

    </div>
</body>

</html>
