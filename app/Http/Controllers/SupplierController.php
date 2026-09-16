<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class SupplierController extends Controller
{
    // Menampilkan daftar supplier
    public function index()
    {
        $suppliers = Supplier::latest()->get();
        return view('supplier.index', compact('suppliers'));
    }

    // Menyimpan data supplier baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'sales_person'  => 'nullable|string|max:255',
            'no_wa'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);

        Supplier::create($request->all());

        AuditLog::catat('Tambah Supplier', "Menambahkan supplier baru: {$request->nama_supplier}");

        return redirect()->back()->with('success', 'Data supplier berhasil ditambahkan!');
    }

    // Mengupdate data supplier
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'sales_person'  => 'nullable|string|max:255',
            'no_wa'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->all());

        AuditLog::catat('Edit Supplier', "Memperbarui data supplier: {$request->nama_supplier}");

        return redirect()->back()->with('success', 'Data supplier berhasil diperbarui!');
    }

    // Menghapus data supplier
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $nama = $supplier->nama_supplier;
        $supplier->delete();

        AuditLog::catat('Hapus Supplier', "Menghapus data supplier: {$nama}");

        return redirect()->back()->with('success', 'Data supplier berhasil dihapus!');
    }
}