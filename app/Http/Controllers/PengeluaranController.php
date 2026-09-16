<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class PengeluaranController extends Controller
{
    // Menampilkan daftar biaya operasional
    public function index(Request $request)
    {
        $dariTanggal = $request->input('dari_tanggal');
        $sampaiTanggal = $request->input('sampai_tanggal');

        $query = Pengeluaran::latest();

        if ($dariTanggal && $sampaiTanggal) {
            $query->whereBetween('tanggal_pengeluaran', [$dariTanggal, $sampaiTanggal]);
        }

        $pengeluarans = $query->get();
        $totalPengeluaran = $pengeluarans->sum('jumlah_biaya');

        return view('pengeluaran.index', compact('pengeluarans', 'totalPengeluaran', 'dariTanggal', 'sampaiTanggal'));
    }

    // Menyimpan pengeluaran baru
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pengeluaran' => 'required|date',
            'kategori'            => 'required|string|max:255',
            'jumlah_biaya'        => 'required|numeric|min:0',
            'keterangan'          => 'nullable|string',
        ]);

        Pengeluaran::create($request->all());

        AuditLog::catat('Tambah Pengeluaran', "Mencatat biaya operasional: {$request->kategori} sebesar Rp " . number_format($request->jumlah_biaya, 0, ',', '.'));

        return redirect()->back()->with('success', 'Biaya operasional berhasil dicatat!');
    }

    // Mengupdate pengeluaran
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_pengeluaran' => 'required|date',
            'kategori'            => 'required|string|max:255',
            'jumlah_biaya'        => 'required|numeric|min:0',
            'keterangan'          => 'nullable|string',
        ]);

        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->update($request->all());

        AuditLog::catat('Edit Pengeluaran', "Memperbarui biaya operasional: {$request->kategori}");

        return redirect()->back()->with('success', 'Biaya operasional berhasil diperbarui!');
    }

    // Menghapus pengeluaran
    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->delete();

        AuditLog::catat('Hapus Pengeluaran', "Menghapus catatan biaya operasional ID: {$id}");

        return redirect()->back()->with('success', 'Catatan pengeluaran berhasil dihapus!');
    }
}