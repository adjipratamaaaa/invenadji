@extends('layouts.app')

@section('title', 'Tambah Stok Masuk - PC Parts')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Stok Masuk</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('stock-ins.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-plus-circle me-2"></i>Input Stok Barang Baru
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('stock-ins.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <!-- Informasi Transaksi -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Informasi Transaksi</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="invoice_number" class="form-label">Nomor Invoice</label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                                           id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}" required>
                                    <button type="button" class="btn btn-outline-primary" id="generateInvoice">
                                        <i class="fas fa-sync-alt me-1"></i>Generate
                                    </button>
                                    @error('invoice_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label">Tanggal Masuk</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                       id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Informasi Produk -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Informasi Produk</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Kategori PC Part</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="product_name" class="form-label">Nama Produk/Spesifikasi</label>
                                <input type="text" class="form-control @error('product_name') is-invalid @enderror" 
                                       id="product_name" name="product_name" value="{{ old('product_name') }}" 
                                       placeholder="Contoh: Intel Core i5-12400F, RTX 4060 Ti 8GB, DDR4 16GB 3200MHz" required>
                                <small class="form-text text-muted">
                                    Produk akan otomatis dibuat/ditemukan berdasarkan nama dan kategori
                                </small>
                                @error('product_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <!-- Detail Stok & Harga -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Detail Stok & Harga</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                               id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required>
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Harga Beli per Unit</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                                   id="price" name="price" value="{{ old('price') }}" min="0" step="0.01" required>
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="note" class="form-label">Catatan Spesifikasi</label>
                                <textarea class="form-control @error('note') is-invalid @enderror" 
                                          id="note" name="note" rows="3" placeholder="Detail spesifikasi, supplier, atau catatan lainnya">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Preview Harga -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Preview Harga</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <small>
                                    <strong>Harga Jual Otomatis:</strong><br>
                                    <span id="sellingPricePreview">-</span><br>
                                    <em>Markup 15% dari harga beli</em>
                                </small>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-save me-2"></i>Simpan Stok Masuk
                                </button>
                                <small class="text-muted d-block mt-2">
                                    Produk akan otomatis tersedia untuk penjualan
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#generateInvoice').click(function() {
            $.get('{{ route("stock-ins.generate-invoice") }}', function(data) {
                $('#invoice_number').val(data.invoice_number);
            });
        });

        // Preview harga jual
        function updateSellingPrice() {
            var purchasePrice = $('#price').val();
            if (purchasePrice) {
                var sellingPrice = purchasePrice * 1.15;
                $('#sellingPricePreview').text('Rp ' + sellingPrice.toLocaleString('id-ID'));
            } else {
                $('#sellingPricePreview').text('-');
            }
        }

        $('#price').on('input', updateSellingPrice);
        updateSellingPrice(); // Initial update
    });
</script>
@endpush