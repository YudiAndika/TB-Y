@extends('layouts.app')
@section('title', 'Kasir POS')
@section('content')

    <!-- Pesan Sukses atau Error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            @if(session('kode_transaksi'))
                <a href="/kasir/struk/{{ session('kode_transaksi') }}" target="_blank" class="btn btn-dark btn-sm ms-2 mt-2 mt-sm-0 fw-bold d-inline-block">
                    <i class="bi bi-printer-fill me-1"></i> CETAK STRUK SEKARANG
                </a>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 g-lg-4">
        <!-- Area Kiri: Input Transaksi -->
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm p-3 p-md-4 border-0 h-100">
                <h5 class="fw-bold mb-3 text-dark fs-6 fs-md-5"><i class="bi bi-upc-scan me-2 text-primary"></i>Input Barang</h5>
                <form action="/kasir/add-cart" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Scan Barcode</label>
                        <input type="text" name="barcode" class="form-control form-control-lg bg-light text-center fw-bold text-primary" placeholder="Tembak Scanner..." autofocus autocomplete="off">
                    </div>
                    
                    <div class="text-center my-2 text-muted fw-semibold" style="font-size: 0.75rem;">--- ATAU PILIH MANUAL ---</div>
                    
                    <div class="mb-3">
                        <select name="barang_id" class="form-select">
                            <option value="">Cari Nama Barang...</option>
                            @foreach($barangs as $b)
                                <option value="{{ $b->id }}">
                                    {{ $b->nama_barang }} (Stok: {{ $b->stok }}) - Rp {{ number_format($b->harga_jual, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Jumlah Beli</label>
                        <input type="number" name="jumlah" class="form-control text-center" value="1" min="1" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 py-md-3 fw-bold shadow-sm">
                        <i class="bi bi-cart-plus me-2"></i> TAMBAH KE KERANJANG
                    </button>
                </form>
            </div>
        </div>

        <!-- Area Kanan: Keranjang Belanja & Pembayaran -->
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold text-dark fs-6 fs-md-5"><i class="bi bi-cart4 me-2 text-primary"></i>Keranjang Belanja</h5>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <!-- Tambahan class text-nowrap agar tabel tidak tergencet di layar HP -->
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 ps-md-4">Barang</th>
                                    <th>Harga</th>
                                    <th class="text-center">Jml</th>
                                    <th>Diskon (Rp)</th>
                                    <th>Subtotal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalBelanja = 0; @endphp
                                @if(session()->has('keranjang') && count(session('keranjang')) > 0)
                                    @foreach(session('keranjang') as $id => $item)
                                        @php $totalBelanja += $item['total']; @endphp
                                        <tr>
                                            <td class="ps-3 ps-md-4 fw-bold text-dark">{{ $item['nama_barang'] }}</td>
                                            <td>Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 fw-bold">{{ $item['jumlah'] }}</span>
                                            </td>
                                            
                                            <td>
                                                <form action="/kasir/update-diskon/{{ $id }}" method="POST" class="d-flex gap-1 mb-0">
                                                    @csrf
                                                    <input type="number" name="diskon" class="form-control form-control-sm" 
                                                           placeholder="0" value="{{ $item['diskon'] ?? 0 }}" style="width: 75px;">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Terapkan Diskon">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                            </td>

                                            <td class="text-primary fw-bold">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <a href="/kasir/hapus/{{ $id }}" class="text-danger fs-5" title="Hapus"><i class="bi bi-x-circle-fill"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted text-wrap">
                                            <i class="bi bi-cart-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                            Keranjang masih kosong.<br>Silakan input barang di sebelah kiri.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if(session()->has('keranjang') && count(session('keranjang')) > 0)
                <div class="card-footer bg-light p-3 p-md-4 border-0">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3 text-center text-md-start">
                        <h5 class="mb-1 mb-md-0 text-secondary fw-bold">TOTAL TAGIHAN:</h5>
                        <h2 class="mb-0 fw-bold text-primary fs-3 fs-md-2" id="totalTagihan" data-total="{{ $totalBelanja }}">
                            Rp {{ number_format($totalBelanja, 0, ',', '.') }}
                        </h2>
                    </div>
                    
                    <form action="/kasir/bayar" method="POST">
                        @csrf
                        
                        <!-- Input Data Pelanggan -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Pelanggan</label>
                            <input type="text" name="nama_pelanggan" class="form-control" placeholder="Nama Pelanggan" value="Umum" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold small">No. WhatsApp <span class="text-muted fw-normal">(Opsional)</span></label>
                                <input type="text" name="no_wa" class="form-control" placeholder="0812...">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold small">Alamat <span class="text-muted fw-normal">(Opsional)</span></label>
                                <input type="text" name="alamat" class="form-control" placeholder="Jl. Raya...">
                            </div>
                        </div>
                        
                        <!-- METODE PEMBAYARAN UTAMA -->
                        <div class="mb-3">
                            <label for="metode_pembayaran" class="form-label fw-semibold small">Metode Pembayaran (1)</label>
                            <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                                <option value="Tunai">Tunai (Cash)</option>
                                <option value="Transfer">Transfer Bank</option>
                                <option value="QRIS">QRIS / E-Wallet</option>
                            </select>
                        </div>

                        <!-- TOMBOL TRIGGER SPLIT BILL -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm fw-bold w-100 border-dashed py-2" id="btnToggleSplit" onclick="toggleSplitBill()">
                                <i class="bi bi-plus-circle me-1"></i> + Gunakan 2 Metode (Split Bill)
                            </button>
                        </div>

                        <!-- BAGIAN FORM SPLIT BILL -->
                        <div id="formSplitBill" class="p-3 mb-3 border rounded-3 bg-white shadow-sm d-none">
                            <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-pie-chart me-1 text-primary"></i> Rincian Metode Kedua</h6>
                            <div class="row g-2">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Metode (2)</label>
                                    <select name="metode_kedua" class="form-select form-select-sm">
                                        <option value="">Pilih Metode...</option>
                                        <option value="Tunai">Tunai (Cash)</option>
                                        <option value="Transfer">Transfer Bank</option>
                                        <option value="QRIS">QRIS / E-Wallet</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold">Nominal Bayar (Rp)</label>
                                    <input type="number" name="uang_bayar_kedua" id="uangBayarKedua" class="form-control form-control-sm" placeholder="0" value="0">
                                </div>
                            </div>
                        </div>

                        <!-- INPUT PEMBAYARAN UTAMA & KEMBALIAN -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold small">Uang Pelanggan (Rp)</label>
                                <input type="number" name="uang_bayar" id="uangBayar" class="form-control form-control-lg fw-bold text-success fs-5" min="0" placeholder="0" value="0">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold small">Kembalian / Kurang</label>
                                <input type="text" id="uangKembalian" class="form-control form-control-lg fw-bold text-dark bg-white fs-5" readonly value="Rp 0">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold py-3 shadow-sm mb-2">
                            <i class="bi bi-cash-coin me-2"></i> PROSES PEMBAYARAN
                        </button>
                    </form>
                </div>
                @endif

                <!-- KOTAK FITUR HOLD & RESUME ANTREAN -->
                @php
                    use App\Models\HoldCart;
                    $daftarHold = HoldCart::latest()->get();
                @endphp

                <div class="card-footer bg-white border-top p-3 p-md-4 rounded-bottom-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark m-0 fs-6"><i class="bi bi-pause-circle me-1 text-warning"></i> Antrean (Hold)</h6>
                        <span class="badge bg-secondary">{{ count($daftarHold) }}</span>
                    </div>

                    @if(session()->has('keranjang') && count(session('keranjang')) > 0)
                    <button type="button" class="btn btn-warning btn-sm fw-bold text-dark mb-3 w-100 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalHold">
                        <i class="bi bi-pause-fill me-1"></i> TUNDA TRANSAKSI (HOLD)
                    </button>
                    @endif

                    <div class="list-group" style="max-height: 150px; overflow-y: auto;">
                        @forelse($daftarHold as $h)
                        <div class="list-group-item list-group-item-action d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center py-2 px-3 border mb-1 rounded-3 gap-2">
                            <div>
                                <span class="fw-bold small text-dark d-block">{{ $h->nama_antrean }}</span>
                                <span class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i>{{ $h->created_at->format('H:i:s') }}</span>
                            </div>
                            <a href="/kasir/resume/{{ $h->id }}" class="btn btn-sm btn-primary py-1 px-3 fw-bold w-100 w-sm-auto" style="font-size: 0.8rem;">
                                Resume <i class="bi bi-play-fill"></i>
                            </a>
                        </div>
                        @empty
                        <div class="text-center text-muted py-2 small border rounded-3 bg-light">
                            Tidak ada antrean tertunda.
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL INPUT NAMA ANTREAN HOLD -->
    <div class="modal fade" id="modalHold" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered px-2">
            <div class="modal-content border-0 shadow">
                <form action="/kasir/hold" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold fs-6">Tunda Transaksi (Hold)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label fw-semibold small">Nama Pelanggan/Keterangan:</label>
                        <input type="text" name="nama_antrean" class="form-control" placeholder="Contoh: Pak Budi - Ambil Besi" required autofocus>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning btn-sm fw-bold">Simpan ke Hold</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Hitung Kembalian & Toggle Split Bill (SAMA SEPERTI ASLINYA) -->
    <script>
        function toggleSplitBill() {
            const formSplit = document.getElementById('formSplitBill');
            const btnSplit = document.getElementById('btnToggleSplit');
            
            if (formSplit.classList.contains('d-none')) {
                formSplit.classList.remove('d-none');
                btnSplit.innerHTML = '<i class="bi bi-dash-circle me-1"></i> Batalkan Split Bill';
                btnSplit.classList.remove('btn-outline-secondary');
                btnSplit.classList.add('btn-outline-danger');
            } else {
                formSplit.classList.add('d-none');
                document.getElementById('uangBayarKedua').value = '0';
                btnSplit.innerHTML = '<i class="bi bi-plus-circle me-1"></i> + Gunakan 2 Metode Pembayaran (Split Bill)';
                btnSplit.classList.remove('btn-outline-danger');
                btnSplit.classList.add('btn-outline-secondary');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const inputBayar = document.getElementById('uangBayar');
            const inputKembalian = document.getElementById('uangKembalian');
            const totalTagihan = document.getElementById('totalTagihan');
            const metodePembayaran = document.getElementById('metode_pembayaran'); 
            
            if(inputBayar && totalTagihan) {
                const total = parseInt(totalTagihan.getAttribute('data-total'));
                
                if(metodePembayaran) {
                    metodePembayaran.addEventListener('change', function() {
                        if(this.value === 'Transfer' || this.value === 'QRIS') {
                            inputBayar.value = total;
                            const event = new Event('keyup');
                            inputBayar.dispatchEvent(event);
                        } else {
                            inputBayar.value = '0'; 
                            inputKembalian.value = 'Rp 0';
                        }
                    });
                }

                inputBayar.addEventListener('keyup', function() {
                    const bayar = parseInt(this.value) || 0;
                    const kembali = bayar - total;
                    
                    if(kembali >= 0) {
                        inputKembalian.value = 'Kembali: Rp ' + new Intl.NumberFormat('id-ID').format(kembali);
                        inputKembalian.classList.remove('text-danger');
                        inputKembalian.classList.add('text-success');
                    } else {
                        const sisaTagihan = Math.abs(kembali); 
                        inputKembalian.value = 'Kurang: Rp ' + new Intl.NumberFormat('id-ID').format(sisaTagihan);
                        inputKembalian.classList.remove('text-success');
                        inputKembalian.classList.add('text-danger');
                    }
                });
            }
        });
    </script>
@endsection