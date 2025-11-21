@extends('layouts.app')

@section('title', 'Edit Stok Masuk - PC Parts')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Stok Masuk</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('stock-ins.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="fas fa-edit me-2"></i>Edit Stok Masuk
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('stock-ins.update', $stockIn->id) }}" method="POST">
            @csrf
            @method('PUT')
            
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
                                <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                                       id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $stockIn->invoice_number) }}" required>
                                @error('invoice_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label">Tanggal Masuk</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                       id="date" name="date" value="{{ old('date', $stockIn->date->format('Y-m-d')) }}" required>
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
                                        <option value="{{ $category->id }}" {{ old('category_id', $stockIn->product->category_id) == $category->id ? 'selected' : '' }}>
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
                                       id="product_name" name="product_name" value="{{ old('product_name', $stockIn->product->name) }}" 
                                       placeholder="Contoh: Intel Core i5-12400F, RTX 4060 Ti 8GB, DDR4 16GB 3200MHz" required>
                                <small class="form-text text-muted">
                                    Nama produk dapat diubah, akan mempengaruhi data produk
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
                                               id="quantity" name="quantity" value="{{ old('quantity', $stockIn->quantity) }}" min="1" required>
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
                                                   id="price" name="price" value="{{ old('price', $stockIn->price) }}" min="0" step="0.01" required>
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
                                          id="note" name="note" rows="3" placeholder="Detail spesifikasi, supplier, atau catatan lainnya">{{ old('note', $stockIn->note) }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Informasi Produk Saat Ini -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Informasi Produk Saat Ini</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Kode Produk:</th>
                                    <td>{{ $stockIn->product->code }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori:</th>
                                    <td>{{ $stockIn->product->category->name }}</td>
                                </tr>
                                <tr>
                                    <th>Stok Saat Ini:</th>
                                    <td>
                                        <span class="badge bg-{{ $stockIn->product->stock_status_color }}">
                                            {{ $stockIn->product->stock }} {{ $stockIn->product->unit }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Harga Jual:</th>
                                    <td>Rp {{ number_format($stockIn->product->selling_price, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                            
                            <div class="alert alert-info mt-3">
                                <small>
                                    <strong>Note:</strong> Mengubah data di form ini akan mengupdate:<br>
                                    • Data stok masuk<br>
                                    • Informasi produk (nama, kategori, harga)<br>
                                    • Stok akan disesuaikan otomatis
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('stock-ins.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save me-1"></i>Update Stok Masuk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
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