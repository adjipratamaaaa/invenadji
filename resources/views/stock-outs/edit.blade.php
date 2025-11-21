@extends('layouts.app')

@section('title', 'Edit Stok Keluar')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Stok Keluar</h1>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('stock-outs.update', $stockOut->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="invoice_number" class="form-label">Nomor Invoice</label>
                        <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                               id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $stockOut->invoice_number) }}" required>
                        @error('invoice_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Tanggal</label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" 
                               id="date" name="date" value="{{ old('date', $stockOut->date->format('Y-m-d')) }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="product_id" class="form-label">Produk</label>
                        <select class="form-select @error('product_id') is-invalid @enderror" 
                                id="product_id" name="product_id" required>
                            <option value="">Pilih Produk</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                    {{ old('product_id', $stockOut->product_id) == $product->id ? 'selected' : '' }}
                                    data-stock="{{ $product->stock }}">
                                    {{ $product->name }} (Stok: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Tipe</label>
                        <select class="form-select @error('type') is-invalid @enderror" 
                                id="type" name="type" required>
                            <option value="">Pilih Tipe</option>
                            <option value="sale" {{ old('type', $stockOut->type) == 'sale' ? 'selected' : '' }}>Penjualan</option>
                            <option value="return" {{ old('type', $stockOut->type) == 'return' ? 'selected' : '' }}>Retur</option>
                            <option value="damage" {{ old('type', $stockOut->type) == 'damage' ? 'selected' : '' }}>Rusak</option>
                            <option value="other" {{ old('type', $stockOut->type) == 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                               id="quantity" name="quantity" value="{{ old('quantity', $stockOut->quantity) }}" min="1" required>
                        <small class="form-text text-muted" id="stockInfo">Stok tersedia: -</small>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Harga per Unit</label>
                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                               id="price" name="price" value="{{ old('price', $stockOut->price) }}" min="0" step="0.01" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Catatan</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" 
                                  id="note" name="note" rows="3">{{ old('note', $stockOut->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('stock-outs.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Update Stok Keluar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Update stock info and auto-fill price
        function updateStockInfo() {
            var selectedOption = $('#product_id').find('option:selected');
            var stock = selectedOption.data('stock');
            $('#stockInfo').text('Stok tersedia: ' + stock);
        }

        $('#product_id').change(function() {
            updateStockInfo();
            
            var productId = $(this).val();
            if (productId) {
                $.get('/products/' + productId, function(data) {
                    $('#price').val(data.selling_price);
                });
            }
        });

        // Check stock availability
        $('#quantity').on('input', function() {
            var quantity = $(this).val();
            var stock = $('#product_id').find('option:selected').data('stock');
            
            if (quantity > stock) {
                $(this).addClass('is-invalid');
                $('#stockInfo').addClass('text-danger').removeClass('text-muted');
            } else {
                $(this).removeClass('is-invalid');
                $('#stockInfo').removeClass('text-danger').addClass('text-muted');
            }
        });

        // Initial stock info update
        updateStockInfo();
    });
</script>
@endpush