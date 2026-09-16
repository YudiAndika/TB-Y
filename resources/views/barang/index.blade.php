@extends('layouts.app')
@section('title', 'Gudang')
@section('content')

<!-- Notifikasi -->
@if(session('success'))
    <div class="alert alert-success shadow-sm"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger shadow-sm"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
@endif

<div class="row">
    <!-- Form Tambah -->
    <div class="col-md-4">
        <div class="card shadow-sm p-3">
            <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle me-1"></i> Tambah Barang</h5>
            <form action="/barang" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Semen Gresik" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Barcode</label>
                    <input type="text" name="barcode" class="form-control" placeholder="Scan / Kosongkan">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Kategori</label>
                    <input type="text" name="kategori" class="form-control" placeholder="Contoh: Semen, Besi, Cat" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label small fw-semibold">Stok Awal</label>
                        <input type="number" name="stok" class="form-control" placeholder="0" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label small fw-semibold">Harga Modal (Beli)</label>
                        <input type="number" name="harga_beli" class="form-control" placeholder="0" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Harga Jual</label>
                    <input type="number" name="harga_jual" class="form-control" placeholder="0" required>
                </div>
                <!-- INPUT DISKON GUDANG TAMBAHAN -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-success"><i class="bi bi-tag-fill me-1"></i>Diskon Promo (Opsional)</label>
                    <input type="number" name="diskon" class="form-control border-success" placeholder="0" value="0">
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Barang</button>
            </form>
        </div>
    </div>

    <!-- Tabel Data Gudang -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Barang</th>
                                <th>Barcode</th>
                                <th>Stok</th>
                                <th>Harga Jual</th>
                                <th class="text-success">Diskon</th> <!-- KOLOM DISKON -->
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangs as $barang)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">{{ $barang->nama_barang }}</div>
                                    <span class="badge bg-info text-dark" style="font-size: 0.7rem;">{{ $barang->kategori }}</span>
                                </td>
                                <td>
                                    @if($barang->barcode)
                                        <span class="badge bg-light text-dark border">{{ $barang->barcode }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $barang->stok <= 5 ? 'bg-danger' : 'bg-success' }}">{{ $barang->stok }}</span>
                                </td>
                                <td class="fw-bold text-primary">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                                
                                <!-- MENAMPILKAN DISKON -->
                                <td class="fw-bold text-success">
                                    @if($barang->diskon > 0)
                                        Rp {{ number_format($barang->diskon, 0, ',', '.') }}
                                    @else
                                        <span class="text-muted fw-normal">-</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="/barang/barcode/{{ $barang->id }}" target="_blank" class="btn btn-dark btn-sm" title="Cetak Barcode"><i class="bi bi-upc-scan"></i></a>
                                        <a href="/barang/{{ $barang->id }}/edit" class="btn btn-warning btn-sm text-dark" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <form action="/barang/{{ $barang->id }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm rounded-0 rounded-end" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data barang di gudang.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection