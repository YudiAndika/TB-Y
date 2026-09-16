<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Models\AuditLog; // Pastikan ini ada

class BarangController extends Controller
{
    // 1. Menampilkan daftar barang & Fitur Pencarian (Search + Barcode)
    public function index(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $barangs = Barang::where('nama_barang', 'LIKE', "%{$search}%")
                             ->orWhere('kategori', 'LIKE', "%{$search}%")
                             ->orWhere('barcode', 'LIKE', "%{$search}%") 
                             ->get();
        } else {
            $barangs = Barang::all();
        }

        return view('barang.index', compact('barangs'));
    }

    // 2. Menyimpan data barang baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'barcode'     => 'nullable|string|unique:barangs,barcode',
            'kategori'    => 'required|string|max:255',                
            'stok'        => 'required|integer',
            'harga_beli'  => 'required|numeric',
            'harga_jual'  => 'required|numeric',
            'diskon'      => 'nullable|numeric|min:0', 
        ]);

        Barang::create($request->all());

        // ---> PASANG CCTV (AUDIT LOG) DI SINI <---
        AuditLog::catat('Menambah Barang', "Menambahkan stok baru: {$request->nama_barang} (Barcode: {$request->barcode})");

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan!');
    }

    // 3. Menampilkan halaman form edit
    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    // 4. Menyimpan perubahan data ke database (Update)
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'barcode'     => 'nullable|string|unique:barangs,barcode,' . $id,
            'kategori'    => 'required|string|max:255',                       
            'stok'        => 'required|integer',
            'harga_beli'  => 'required|numeric',
            'harga_jual'  => 'required|numeric',
            'diskon'      => 'nullable|numeric|min:0', 
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($request->all());

        // ---> PASANG CCTV (AUDIT LOG) DI SINI <---
        AuditLog::catat('Mengedit Barang', "Memperbarui data barang: {$request->nama_barang}");

        return redirect('/barang')->with('success', 'Data barang berhasil diperbarui!');
    }

    // 5. Menghapus data barang
    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        
        // ---> PASANG CCTV (AUDIT LOG) DI SINI <---
        // Catat dulu sebelum datanya dihapus
        AuditLog::catat('Menghapus Barang', "Menghapus barang: {$barang->nama_barang}");
        
        $barang->delete();

        return redirect()->back()->with('success', 'Barang berhasil dihapus!');
    }
    
    // Fungsi Baru: Cetak Stiker Barcode
    public function cetakBarcode($id)
    {
        $barang = Barang::findOrFail($id);
        
        // Cek jika barang belum punya angka barcode
        if(empty($barang->barcode)) {
            return redirect()->back()->with('error', 'Barang ini belum memiliki nomor barcode. Silakan edit dan isi barcode-nya terlebih dahulu.');
        }

        return view('barang.barcode', compact('barang'));
    }
}