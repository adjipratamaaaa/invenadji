@extends('layouts.app')

@section('title', 'Edit Produk - PC Parts')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Produk</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Form Edit Produk
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('products.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Kode Produk</label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       value="{{ old('code', $product->code) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Kategori</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="{{ old('name', $product->name) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="purchase_price" class="form-label">Harga Beli</label>
                                <input type="number" class="form-control" id="purchase_price" name="purchase_price" 
                                       value="{{ old('purchase_price', $product->purchase_price) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="selling_price" class="form-label">Harga Jual</label>
                                <input type="number" class="form-control" id="selling_price" name="selling_price" 
                                       value="{{ old('selling_price', $product->selling_price) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="stock" name="stock" 
                                       value="{{ old('stock', $product->stock) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="min_stock" class="form-label">Stok Minimum</label>
                                <input type="number" class="form-control" id="min_stock" name="min_stock" 
                                       value="{{ old('min_stock', $product->min_stock) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="unit" class="form-label">Satuan</label>
                                <input type="text" class="form-control" id="unit" name="unit" 
                                       value="{{ old('unit', $product->unit) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                               {{ $product->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Produk Aktif</label>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Produk
                        </button>
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informasi
                </h5>
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    <i class="fas fa-lightbulb me-1"></i>
                    Pastikan semua data diisi dengan benar. Perubahan akan langsung tersimpan di database.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection