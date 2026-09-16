<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang - Toko Bangunan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f4f9; }
        .form-container { background: #fff; padding: 20px; border-radius: 8px; max-width: 500px; margin: auto; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #45a049; }
        .btn-back { display: inline-block; margin-top: 15px; color: #555; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

    <div class="form-container">
        <h3>Edit Data Barang</h3>
        <form action="/barang/{{ $barang->id }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama Barang:</label>
                <input type="text" name="nama_barang" value="{{ $barang->nama_barang }}" required>
            </div>
            
            <!-- INI ADALAH TAMBAHAN UNTUK BARCODE -->
            <div class="form-group">
                <label>Barcode (Opsional):</label>
                <input type="text" name="barcode" value="{{ $barang->barcode }}" placeholder="Scan barcode di sini...">
            </div>
            <!-- BATAS TAMBAHAN BARCODE -->

            <div class="form-group">
                <label>Kategori:</label>
                <input type="text" name="kategori" value="{{ $barang->kategori }}" required>
            </div>
            <div class="form-group">
                <label>Stok:</label>
                <input type="number" name="stok" value="{{ $barang->stok }}" required>
            </div>
            <div class="form-group">
                <label>Harga Beli (Rp):</label>
                <input type="number" name="harga_beli" value="{{ $barang->harga_beli }}" required>
            </div>
            <div class="form-group">
                <label>Harga Jual (Rp):</label>
                <input type="number" name="harga_jual" value="{{ $barang->harga_jual }}" required>
            </div>
            <button type="submit">Simpan Perubahan</button>
            <br>
            <a href="/barang" class="btn-back">&larr; Batal dan Kembali</a>
        </form>
    </div>

</body>
</html>