<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode - {{ $barang->nama_barang }}</title>
    <!-- Memanggil alat pembuat barcode otomatis -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        body { text-align: center; font-family: Arial, sans-serif; padding-top: 20px; }
        .stiker { 
            display: inline-block; 
            border: 1px dashed #000; /* Garis potong stiker */
            padding: 10px 15px; 
            margin: 10px;
            background: #fff;
        }
        @media print {
            @page { margin: 0; }
            body { margin: 0.5cm; }
            .stiker { border: none; } /* Hilangkan garis putus-putus saat diprint */
        }
    </style>
</head>
<body onload="window.print()"> <!-- Otomatis muncul dialog print -->

    <!-- Mengulangi stiker sebanyak 5 kali agar bisa dipotong-potong -->
    @for ($i = 0; $i < 5; $i++)
    <div class="stiker">
        <p style="margin: 0 0 3px 0; font-weight: bold; font-size: 14px; text-transform: uppercase;">{{ $barang->nama_barang }}</p>
        <p style="margin: 0 0 5px 0; font-size: 12px; color: #333;">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</p>
        
        <!-- Di sinilah gambar barcode akan otomatis digambar -->
        <svg class="barcode"></svg>
    </div>
    @endfor

    <script>
        // Memerintahkan sistem untuk menggambar barcode berdasarkan angka dari database
        JsBarcode(".barcode", "{{ $barang->barcode }}", {
            format: "CODE128", // Format standar universal
            width: 2,
            height: 40,
            displayValue: true,
            fontSize: 14
        });
    </script>
</body>
</html>