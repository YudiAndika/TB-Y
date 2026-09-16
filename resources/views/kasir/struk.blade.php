<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran</title>
    <style>
        /* 1. RESET HALAMAN (Tanpa size: auto) */
        @page {
            margin: 0;
            padding: 0;
            /* Jangan gunakan 'size: 80mm auto;' di sini agar driver Windows yang mengambil kendali pemotongan */
        }

        /* 2. AREA CETAK UTAMA */
        html, body {
            margin: 0;
            padding: 0;
            width: 76mm; /* Lebar aman fisik (dikurangi margin mekanik printer) */
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.2;
            color: #000;
            background: #fff;
        }

        .container {
            width: 100%;
            padding: 2mm 2mm 0 2mm;
            box-sizing: border-box;
            /* Memastikan printer berhenti membaca elemen setelah div ini selesai */
            overflow: hidden; 
        }

        /* 3. STYLING ELEMEN */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .line { border-bottom: 1px dashed #000; margin: 4px 0; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="text-center">
            <strong>TB DUTA PRATAMA</strong><br>
            Jl. KH Umar No. 38, Cileungsi<br>
            Telp: 0812-3456-7890
        </div>
        
        <div class="line"></div>

        <!-- METADATA -->
        <div>
            Nota: {{ $transaksi->no_nota ?? 'INV-20260912' }}<br>
            Tgl : {{ date('d-m-Y H:i') }}<br>
            Pelanggan: {{ $transaksi->pelanggan ?? 'dadas' }}
        </div>

        <div class="line"></div>

        <!-- ITEM BARANG -->
        <table>
            @foreach($items ?? [] as $item)
            <tr>
                <td colspan="2"><strong>{{ $item->nama_barang ?? 'Nama Barang Contoh' }}</strong></td>
            </tr>
            <tr>
                <td>{{ $item->qty ?? 1 }}x @ {{ number_format($item->harga ?? 100000) }}</td>
                <td class="text-right">{{ number_format($item->subtotal ?? 100000) }}</td>
            </tr>
            @endforeach
        </table>

        <div class="line"></div>

        <!-- TOTAL & METODE BAYAR -->
        <table>
            <tr>
                <td><strong>TOTAL</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($total ?? 306000) }}</strong></td>
            </tr>
            <tr>
                <td>Metode</td>
                <td class="text-right">QRIS</td>
            </tr>
            <tr>
                <td>Bayar</td>
                <td class="text-right">Rp {{ number_format($bayar ?? 306000) }}</td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="text-right">Rp 0</td>
            </tr>
        </table>

        <div class="line"></div>

        <!-- FOOTER (Data Selesai) -->
        <div class="text-center" style="margin-top: 5px; margin-bottom: 5mm;">
            Terima Kasih<br>
            Barang yang sudah dibeli<br>
            tidak dapat ditukar/dikembalikan
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>