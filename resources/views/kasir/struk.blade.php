<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $kode }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, monospace;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.35;
            color: #000;
            background: #fff;
            -webkit-print-color-adjust: exact;
        }

        .struk-wrap {
            width: 72mm;
            max-width: 100%;
            margin: 0 auto;
            padding: 2mm 1mm;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: 800; }

        .divider {
            border: none;
            border-top: 1.5px dashed #000;
            margin: 4px 0;
        }

        .tbl-items, .tbl-summary {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tbl-items td, .tbl-summary td {
            vertical-align: top;
            padding: 2px 0;
            word-wrap: break-word;
        }

        .tbl-items .col-desc  { width: 60%; font-weight: 600; }
        .tbl-items .col-price { width: 40%; text-align: right; font-weight: 600; }

        .tbl-summary .col-label { width: 40%; font-weight: 600; }
        .tbl-summary .col-value { width: 60%; text-align: right; font-weight: 600; }

        /* Jarak sobek kertas dihemat jadi 10mm agar tidak terbuang banyak tapi aman dari pisau */
        .paper-feed { height: 10mm; }

        /* Tombol hanya di layar, hilang saat print */
        .btn-area { text-align: center; padding: 10px; }
        .btn-print { background:#1e1b4b; color:#fff; border:none; padding:8px 18px; font-size:13px; font-family:sans-serif; border-radius:5px; cursor:pointer; margin:2px; }
        .btn-close  { background:#6b7280; color:#fff; border:none; padding:8px 18px; font-size:13px; font-family:sans-serif; border-radius:5px; cursor:pointer; margin:2px; }

        @media print {
            .btn-area { display: none !important; }
            html, body {
                width: 100%;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }
            .struk-wrap {
                width: 72mm !important;
                max-width: 72mm !important;
                margin: 0 auto !important;
                padding: 2mm 2mm !important;
            }
            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="btn-area">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Ulang</button>
        <button class="btn-close" onclick="window.close()">✕ Tutup</button>
    </div>

    <div class="struk-wrap">

        {{-- HEADER --}}
        <div class="text-center">
            <div class="bold" style="font-size: 14px;">TB DUTA PRATAMA</div>
            <div>Jl. KH Umar No. 38, Cileungsi</div>
            <div>Telp: 0812-3456-7890</div>
        </div>

        <hr class="divider">

        {{-- METADATA --}}
        <div>
            <div>Nota  : {{ $kode }}</div>
            <div>Tgl   : {{ isset($transaksi) && $transaksi->created_at ? $transaksi->created_at->format('d-m-Y H:i') : date('d-m-Y H:i') }}</div>
            <div>Plg   : {{ $transaksi->nama_pelanggan ?? 'Umum' }}</div>
            <div>Kasir : {{ auth()->user()->name ?? 'Kasir' }}</div>
        </div>

        <hr class="divider">

        {{-- DAFTAR BARANG --}}
        <table class="tbl-items">
            @foreach($penjualans as $item)
            @php
                $namaBarang  = $item->barang->nama_barang ?? 'Barang Dihapus';
                $hargaSatuan = $item->barang
                    ? $item->barang->harga_jual
                    : ($item->jumlah > 0 ? round($item->total_harga / $item->jumlah) : $item->total_harga);
            @endphp
            <tr>
                <td colspan="2" class="bold">{{ $namaBarang }}</td>
            </tr>
            <tr>
                <td class="col-desc">
                    {{ $item->jumlah }} x {{ number_format($hargaSatuan, 0, ',', '.') }}
                    @if(!empty($item->diskon) && $item->diskon > 0)
                        <br><small>(Disc: -Rp {{ number_format($item->diskon, 0, ',', '.') }})</small>
                    @endif
                </td>
                <td class="col-price">{{ number_format($item->total_harga, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>

        <hr class="divider">

        {{-- RINGKASAN PEMBAYARAN --}}
        <table class="tbl-summary">
            <tr>
                <td class="col-label bold">TOTAL</td>
                <td class="col-value bold">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="col-label">Metode</td>
                <td class="col-value">{{ $transaksi->metode_pembayaran ?? 'Tunai' }}</td>
            </tr>
            <tr>
                <td class="col-label">Bayar</td>
                <td class="col-value">Rp {{ number_format($transaksi->uang_bayar ?? $totalBelanja, 0, ',', '.') }}</td>
            </tr>
            @if(isset($transaksi->sisa_piutang) && $transaksi->sisa_piutang > 0)
            <tr>
                <td class="col-label bold">PIUTANG</td>
                <td class="col-value bold">Rp {{ number_format($transaksi->sisa_piutang, 0, ',', '.') }}</td>
            </tr>
            @elseif(($transaksi->uang_kembali ?? 0) > 0)
            <tr>
                <td class="col-label">Kembali</td>
                <td class="col-value">Rp {{ number_format($transaksi->uang_kembali, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>

        <hr class="divider">

        {{-- FOOTER --}}
        <div class="text-center" style="margin-top: 4px; font-size: 11px;">
            <div>Terima kasih atas kunjungan Anda!</div>
            <div>Barang yang sudah dibeli</div>
            <div>tidak dapat ditukar/dikembalikan.</div>
        </div>

        <div class="paper-feed"></div>

    </div>

    <script>
        window.onload = function () {
            if (!sessionStorage.getItem('sudah_print')) {
                sessionStorage.setItem('sudah_print', '1');
                window.print();
            }
        };
        window.addEventListener('afterprint', function () {
            sessionStorage.removeItem('sudah_print');
        });
    </script>

</body>
</html>