@extends('layouts.app')

@section('title', 'Data Pelanggan & CRM')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-sm-6">
            <h2 class="fw-bold text-dark m-0"><i class="bi bi-people-fill me-2 text-primary"></i> Data Pelanggan / CRM</h2>
            <p class="text-secondary small m-0">Kelola database pelanggan tetap, kontraktor, dan mandor toko bangunan.</p>
        </div>
        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPelanggan">
                <i class="bi bi-plus-lg me-1"></i> Tambah Pelanggan Baru
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- TABEL PELANGGAN -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama Pelanggan</th>
                            <th>No. WhatsApp</th>
                            <th>Kategori</th>
                            <th>Alamat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pelanggans as $index => $p)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $p->nama_pelanggan }}</div>
                            </td>
                            <td>
                                @if($p->no_wa)
                                    <a href="https://wa.me/{{ $p->no_wa }}" target="_blank" class="text-decoration-none text-success fw-semibold">
                                        <i class="bi bi-whatsapp me-1"></i> {{ $p->no_wa }}
                                    </a>
                                @else
                                    <span class="text-muted small">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1 fw-bold">
                                    {{ $p->kategori }}
                                </span>
                            </td>
                            <td class="text-secondary small">{{ $p->alamat ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ url('/pelanggan/' . $p->id) }}" class="btn btn-sm btn-info text-white me-1" title="Histori Belanja">
                                    <i class="bi bi-clock-history"></i> Histori
                                </a>
                                <form action="{{ url('/pelanggan/' . $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data pelanggan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i> Belum ada data pelanggan tersimpan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PELANGGAN -->
<div class="modal fade" id="modalTambahPelanggan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ url('/pelanggan') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i> Tambah Pelanggan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap Pelanggan / Toko <span class="text-danger">*</span></label>
                    <input type="text" name="nama_pelanggan" class="form-control" placeholder="Contoh: Pak Budi Kontraktor" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor WhatsApp</label>
                    <input type="text" name="no_wa" class="form-control" placeholder="Contoh: 081234567890">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kategori Pelanggan <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        <option value="Umum">Umum / Retail</option>
                        <option value="Kontraktor">Kontraktor</option>
                        <option value="Mandor">Mandor Bangunan</option>
                        <option value="Tukang">Tukang Langganan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat proyek atau rumah pelanggan..."></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light px-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Pelanggan</button>
            </div>
        </form>
    </div>
</div>
@endsection