@extends('layouts.app')

@section('title', 'Detail Produk - PC Parts')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Produk</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning me-2">
            <i class="fas fa-edit me-1"></i>Edit
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <!-- Informasi Produk -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informasi Produk
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Kode Produk</th>
                        <td>{{ $product->code }}</td>
                    </tr>
                    <tr>
                        <th>Nama Produk</th>
                        <td>{{ $product->name }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $product->category->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $product->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Satuan</th>
                        <td>{{ $product->unit }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($product->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Informasi Stok -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-boxes me-2"></i>Informasi Stok
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Stok Saat Ini</th>
                        <td>
                            <span class="badge bg-{{ $stockStatusColor }} fs-6">
                                {{ $product->stock }} {{ $product->unit }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Stok Minimum</th>
                        <td>{{ $product->min_stock }} {{ $product->unit }}</td>
                    </tr>
                    <tr>
                        <th>Status Stok</th>
                        <td>
                            @if($stockStatus == 'out-of-stock')
                                <span class="text-danger fw-bold">STOK HABIS</span>
                            @elseif($stockStatus == 'low-stock')
                                <span class="text-warning fw-bold">STOK RENDAH</span>
                            @else
                                <span class="text-success fw-bold">STOK AMAN</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Nilai Stok</th>
                        <td class="fw-bold">Rp {{ number_format($product->stock * $product->purchase_price, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <!-- Informasi Harga -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-money-bill-wave me-2"></i>Informasi Harga
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Harga Beli</th>
                        <td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Harga Jual</th>
                        <td class="fw-bold">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Profit per Unit</th>
                        <td class="text-success fw-bold">
                            Rp {{ number_format($product->selling_price - $product->purchase_price, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <th>Profit Margin</th>
                        <td class="{{ $profitMargin >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ number_format($profitMargin, 1) }}%
                        </td>
                    </tr>
                    <tr>
                        <th>Total Profit Potensial</th>
                        <td class="text-success fw-bold">
                            Rp {{ number_format($totalProfitPotential, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Riwayat Stok -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Riwayat Stok Terbaru
                </h5>
            </div>
            <div class="card-body">
                @php
                    $recentStockIns = $product->stockIns()->latest()->limit(3)->get();
                    $recentStockOuts = $product->stockOuts()->latest()->limit(3)->get();
                @endphp

                @if($recentStockIns->count() > 0 || $recentStockOuts->count() > 0)
                    <h6>Stok Masuk:</h6>
                    @forelse($recentStockIns as $stockIn)
                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                            <span>{{ $stockIn->date->format('d/m/Y') }}</span>
                            <span class="text-success">+{{ $stockIn->quantity }}</span>
                        </div>
                    @empty
                        <p class="text-muted">Belum ada stok masuk</p>
                    @endforelse

                    <h6 class="mt-3">Stok Keluar:</h6>
                    @forelse($recentStockOuts as $stockOut)
                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                            <span>{{ $stockOut->date->format('d/m/Y') }}</span>
                            <span class="text-danger">-{{ $stockOut->quantity }}</span>
                        </div>
                    @empty
                        <p class="text-muted">Belum ada stok keluar</p>
                    @endforelse
                @else
                    <p class="text-muted">Belum ada riwayat stok</p>
                @endif

                <div class="text-center mt-3">
                    <a href="{{ route('stock-ins.index') }}?product_id={{ $product->id }}" class="btn btn-sm btn-outline-primary me-2">
                        Lihat Semua Stok Masuk
                    </a>
                    <a href="{{ route('stock-outs.index') }}?product_id={{ $product->id }}" class="btn btn-sm btn-outline-danger">
                        Lihat Semua Stok Keluar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center">
                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger me-2" onclick="return confirm('Hapus produk ini?')">
                        <i class="fas fa-trash me-1"></i>Hapus Produk
                    </button>
                </form>
                
                {{-- TOGGLE STATUS BUTTON YANG SUDAH DIPERBAIKI --}}
                <a href="{{ route('products.toggle-status', $product->id) }}" 
                   class="btn btn-{{ $product->is_active ? 'warning' : 'success' }}"
                   onclick="return confirm('Yakin ingin {{ $product->is_active ? 'menonaktifkan' : 'mengaktifkan' }} produk ini?')">
                    <i class="fas fa-power-off me-1"></i>
                    {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Produk
                </a>

                {{-- QUICK STOCK UPDATE BUTTONS --}}
                <div class="mt-3">
                    <button type="button" class="btn btn-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#quickStockInModal">
                        <i class="fas fa-plus me-1"></i>Stok Masuk Cepat
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#quickStockOutModal">
                        <i class="fas fa-minus me-1"></i>Stok Keluar Cepat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stock In Modal -->
<div class="modal fade" id="quickStockInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('products.quick-update-stock', $product->id) }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="in">
                <div class="modal-header">
                    <h5 class="modal-title">Stok Masuk Cepat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quantity_in" class="form-label">Jumlah Stok Masuk</label>
                        <input type="number" class="form-control" id="quantity_in" name="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="note_in" class="form-label">Keterangan (opsional)</label>
                        <textarea class="form-control" id="note_in" name="note" rows="2" placeholder="Contoh: Tambahan stok dari supplier"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Stok Masuk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Stock Out Modal -->
<div class="modal fade" id="quickStockOutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('products.quick-update-stock', $product->id) }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="out">
                <div class="modal-header">
                    <h5 class="modal-title">Stok Keluar Cepat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quantity_out" class="form-label">Jumlah Stok Keluar</label>
                        <input type="number" class="form-control" id="quantity_out" name="quantity" min="1" max="{{ $product->stock }}" required>
                        <div class="form-text">Stok tersedia: {{ $product->stock }} {{ $product->unit }}</div>
                    </div>
                    <div class="mb-3">
                        <label for="note_out" class="form-label">Keterangan (opsional)</label>
                        <textarea class="form-control" id="note_out" name="note" rows="2" placeholder="Contoh: Penjualan ke customer"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Simpan Stok Keluar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection