<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class PelangganController extends Controller
{
    // Menampilkan daftar pelanggan
    public function index()
    {
        $pelanggans = Pelanggan::latest()->get();
        return view('pelanggan.index', compact('pelanggans'));
    }

    // Menyimpan data pelanggan baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'no_wa'          => 'nullable|string|max:20',
            'alamat'         => 'nullable|string',
            'kategori'       => 'required|string',
        ]);

        Pelanggan::create($request->all());

        AuditLog::catat('Tambah Pelanggan', "Menambahkan pelanggan baru: {$request->nama_pelanggan}");

        return redirect()->back()->with('success', 'Data pelanggan berhasil ditambahkan!');
    }

    // Menampilkan Detail Histori Belanja Pelanggan
    public function show($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        // Ambil transaksi berdasarkan nama pelanggan, 
        // pastikan relasi ke detail/item barang ikut dimuat (with) jika ada,
        // atau jika tabel Penjualan menyimpan per item barang:
        $historiBelanja = Penjualan::where('nama_pelanggan', $pelanggan->nama_pelanggan)
            ->with('barang') // Pastikan relasi ke tabel barang dimuat
            ->latest()
            ->get()
            ->groupBy('kode_transaksi');

        return view('pelanggan.show', compact('pelanggan', 'historiBelanja'));
    }

    // Menghapus data pelanggan
    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $nama = $pelanggan->nama_pelanggan;
        $pelanggan->delete();

        AuditLog::catat('Hapus Pelanggan', "Menghapus data pelanggan: {$nama}");

        return redirect()->back()->with('success', 'Data pelanggan berhasil dihapus!');
    }
}