@extends('layouts.app')

@section('title', 'Buat Pembelian Stok (PO)')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('pembelian.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <h2 class="fw-bold text-dark m-0"><i class="bi bi-cart-plus text-primary me-2"></i> Form Purchase Order (PO) Pembelian Stok</h2>
        <p class="text-secondary small m-0">Catat nota belanja dari supplier. Stok barang di inventaris akan otomatis bertambah.</p>
    </div>

    <form action="{{ route('pembelian.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Informasi Utama -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold m-0 text-dark">Informasi Nota</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Kode PO</label>
                            <input type="text" class="form-control font-monospace bg-light" value="{{ $kodePO }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Pilih Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Tanggal Pembelian <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pembelian" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Status Pembayaran <span class="text-danger">*</span></label>
                            <select name="status_pembayaran" class="form-select" required>
                                <option value="Lunas">Lunas</option>
                                <option value="Utang">Utang / Tempo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Catatan (Opsional)</label>
                            <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan pengiriman..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Barang Dibeli -->
            <div class="col-md-8 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0 text-dark">Daftar Barang Masuk</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="tambahBaris">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Barang
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="wrapper-item">
                            <div class="row item-row mb-3 align-items-end border-bottom pb-3">
                                <div class="col-md-5">
                                    <label class="form-label fw-bold small">Barang <span class="text-danger">*</span></label>
                                    <select name="barangs[0][barang_id]" class="form-select" required>
                                        <option value="">-- Pilih Barang Gudang --</option>
                                        @foreach($barangs as $barang)
                                            <option value="{{ $barang->id }}">{{ $barang->nama_barang }} (Stok: {{ $barang->stok }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small">Jumlah (Qty) <span class="text-danger">*</span></label>
                                    <input type="number" name="barangs[0][jumlah]" class="form-control" min="1" value="1" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Harga Beli Satuan (Rp) <span class="text-danger">*</span></label>
                                    <input type="number" name="barangs[0][harga_beli]" class="form-control" min="0" placeholder="0" required>
                                </div>
                                <div class="col-md-1 text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm hapus-baris" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm mt-3">
                            <i class="bi bi-check-circle me-1"></i> Simpan Pembelian & Perbarui Stok Gudang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- SCRIPT UNTUK TAMBAH BARIS DINAMIS -->
<script>
    let rowIndex = 1;
    document.getElementById('tambahBaris').addEventListener('click', function() {
        let wrapper = document.getElementById('wrapper-item');
        let html = `
            <div class="row item-row mb-3 align-items-end border-bottom pb-3">
                <div class="col-md-5">
                    <select name="barangs[${rowIndex}][barang_id]" class="form-select" required>
                        <option value="">-- Pilih Barang Gudang --</option>
                        @foreach($barangs as $barang)
                            <option value="{{ $barang->id }}">{{ $barang->nama_barang }} (Stok: {{ $barang->stok }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="barangs[${rowIndex}][jumlah]" class="form-control" min="1" value="1" required>
                </div>
                <div class="col-md-4">
                    <input type="number" name="barangs[${rowIndex}][harga_beli]" class="form-control" min="0" placeholder="0" required>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm hapus-baris">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        wrapper.insertAdjacentHTML('beforeend', html);
        rowIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.hapus-baris')) {
            e.target.closest('.item-row').remove();
        }
    });
</script>
@endsection