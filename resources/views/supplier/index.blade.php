@extends('layouts.app')

@section('title', 'Data Supplier')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0"><i class="bi bi-truck text-primary me-2"></i> Data Supplier / Distributor</h2>
            <p class="text-secondary small m-0">Kelola daftar toko bangunan, pabrik, atau sales langganan penyuplai stok.</p>
        </div>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSupplier">
            <i class="bi bi-plus-circle me-1"></i> Tambah Supplier Baru
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- TABEL SUPPLIER -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama Supplier / Toko</th>
                            <th>Nama Sales Person</th>
                            <th>No. WhatsApp</th>
                            <th>Alamat</th>
                            <th>Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td class="ps-4 fw-semibold text-secondary">{{ $loop->iteration }}</td>
                            <td class="fw-bold text-dark">{{ $supplier->nama_supplier }}</td>
                            <td>{{ $supplier->sales_person ?? '-' }}</td>
                            <td>
                                @if($supplier->no_wa)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $supplier->no_wa)) }}" target="_blank" class="text-decoration-none text-success fw-bold">
                                        <i class="bi bi-whatsapp me-1"></i> {{ $supplier->no_wa }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ Str::limit($supplier->alamat, 30) ?? '-' }}</td>
                            <td><span class="text-muted small">{{ $supplier->keterangan ?? '-' }}</span></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEditSupplier{{ $supplier->id }}" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- MODAL EDIT SUPPLIER -->
                        <div class="modal fade" id="modalEditSupplier{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-light">
                                            <h5 class="fw-bold m-0"><i class="bi bi-pencil-square text-warning me-2"></i> Edit Data Supplier</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Nama Supplier / Toko <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_supplier" class="form-control" value="{{ $supplier->nama_supplier }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Nama Sales Person</label>
                                                <input type="text" name="sales_person" class="form-control" value="{{ $supplier->sales_person }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">No. WhatsApp</label>
                                                <input type="text" name="no_wa" class="form-control" value="{{ $supplier->no_wa }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Alamat</label>
                                                <textarea name="alamat" class="form-control" rows="2">{{ $supplier->alamat }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Keterangan / Produk yang Disuplai</label>
                                                <textarea name="keterangan" class="form-control" rows="2">{{ $supplier->keterangan }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning btn-sm text-white fw-bold">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-truck fs-1 d-block mb-2"></i> Belum ada data supplier yang terdaftar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH SUPPLIER -->
<div class="modal fade" id="modalTambahSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('supplier.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="fw-bold m-0"><i class="bi bi-plus-circle text-primary me-2"></i> Tambah Supplier Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Supplier / Toko <span class="text-danger">*</span></label>
                        <input type="text" name="nama_supplier" class="form-control" placeholder="Contoh: PT Semen Tiga Roda / Toko Besi Jaya" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Sales Person</label>
                        <input type="text" name="sales_person" class="form-control" placeholder="Contoh: Bpk. Budi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">No. WhatsApp</label>
                        <input type="text" name="no_wa" class="form-control" placeholder="Contoh: 081234567890">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat supplier..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Keterangan / Produk yang Disuplai</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Khusus suplai besi beton & semen"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection