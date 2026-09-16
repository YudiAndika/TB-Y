@extends('layouts.app')

@section('title', 'Biaya Operasional Toko')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0"><i class="bi bi-wallet2 text-warning me-2"></i> Biaya Operasional Toko</h2>
            <p class="text-secondary small m-0">Catat pengeluaran di luar pembelian stok (seperti listrik, air, gaji, sewa, dll).</p>
        </div>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPengeluaran">
            <i class="bi bi-plus-circle me-1"></i> Catat Pengeluaran Baru
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- FILTER TANGGAL & RINGKASAN -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body bg-light">
                    <form method="GET" action="{{ route('pengeluaran.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Dari Tanggal</label>
                            <input type="date" name="dari_tanggal" class="form-control" value="{{ $dariTanggal }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Sampai Tanggal</label>
                            <input type="date" name="sampai_tanggal" class="form-control" value="{{ $sampaiTanggal }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter me-1"></i> Filter Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-warning-subtle h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <span class="text-secondary small fw-bold text-uppercase">Total Biaya Operasional</span>
                    <h3 class="fw-bold text-dark m-0">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL PENGELUARAN -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Tanggal</th>
                            <th>Kategori Biaya</th>
                            <th>Jumlah Biaya</th>
                            <th>Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengeluarans as $pengeluaran)
                        <tr>
                            <td class="ps-4 fw-semibold text-secondary">{{ $loop->iteration }}</td>
                            <td class="text-secondary small">{{ date('d/m/Y', strtotime($pengeluaran->tanggal_pengeluaran)) }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark border px-2 py-1 fw-bold">
                                    {{ $pengeluaran->kategori }}
                                </span>
                            </td>
                            <td class="fw-bold text-danger">Rp {{ number_format($pengeluaran->jumlah_biaya, 0, ',', '.') }}</td>
                            <td><span class="text-muted small">{{ $pengeluaran->keterangan ?? '-' }}</span></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEditPengeluaran{{ $pengeluaran->id }}" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('pengeluaran.destroy', $pengeluaran->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus catatan pengeluaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- MODAL EDIT PENGELUARAN -->
                        <div class="modal fade" id="modalEditPengeluaran{{ $pengeluaran->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('pengeluaran.update', $pengeluaran->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-light">
                                            <h5 class="fw-bold m-0"><i class="bi bi-pencil-square text-warning me-2"></i> Edit Biaya Operasional</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                                                <input type="date" name="tanggal_pengeluaran" class="form-control" value="{{ $pengeluaran->tanggal_pengeluaran }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Kategori Biaya <span class="text-danger">*</span></label>
                                                <input type="text" name="kategori" class="form-control" value="{{ $pengeluaran->kategori }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Jumlah Biaya (Rp) <span class="text-danger">*</span></label>
                                                <input type="number" name="jumlah_biaya" class="form-control" value="{{ $pengeluaran->jumlah_biaya }}" min="0" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Keterangan (Opsional)</label>
                                                <textarea name="keterangan" class="form-control" rows="2">{{ $pengeluaran->keterangan }}</textarea>
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
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-wallet2 fs-1 d-block mb-2"></i> Belum ada catatan biaya operasional pada periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PENGELUARAN -->
<div class="modal fade" id="modalTambahPengeluaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('pengeluaran.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="fw-bold m-0"><i class="bi bi-plus-circle text-primary me-2"></i> Catat Biaya Operasional Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pengeluaran" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Kategori Biaya <span class="text-danger">*</span></label>
                        <input type="text" name="kategori" class="form-control" placeholder="Contoh: Listrik & Air, Gaji Pegawai, Sewa Toko" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Jumlah Biaya (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_biaya" class="form-control" placeholder="0" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Simpan Pengeluaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection