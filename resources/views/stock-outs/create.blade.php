@extends('layouts.app')

@section('title', 'Tambah Stok Keluar')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Stok Keluar</h1>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('stock-outs.store') }}" method="POST" id="stockOutForm">
            @csrf
            
            <!-- BARCODE SCANNER SECTION -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-barcode me-2"></i>Barcode Scanner
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="barcode_scanner" class="form-label">Scan Barcode Produk</label>
                            <input type="text" 
                                   id="barcode_scanner" 
                                   class="form-control form-control-lg" 
                                   placeholder="Tempatkan kursor disini dan scan barcode..."
                                   autofocus>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Scan barcode produk atau ketik manual kode barcode
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-end h-100">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="clearScanner()">
                                    <i class="fas fa-times me-1"></i>Clear
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="scan-result" class="alert alert-success mt-3" style="display:none;">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Produk Ditemukan:</strong> 
                        <span id="scanned-product-name"></span>
                    </div>
                    <div id="scan-error" class="alert alert-danger mt-3" style="display:none;">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Produk Tidak Ditemukan!</strong> 
                        <span id="error-message"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="invoice_number" class="form-label">Nomor Invoice</label>
                        <div class="input-group">
                            <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                                   id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}" required>
                            <button type="button" class="btn btn-outline-secondary" id="generateInvoice">
                                Generate
                            </button>
                            @error('invoice_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Tanggal</label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" 
                               id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- PRODUK SECTION -->
                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        
                        <!-- Hidden input untuk simpan product_id -->
                        <input type="hidden" id="product_id" name="product_id" value="{{ old('product_id') }}">
                        
                        <!-- Display produk yang auto terisi -->
                        <div id="product_display" class="form-control" style="background-color: #f8f9fa; min-height: 38px; display: flex; align-items: center; padding: 6px 12px; border: 1px solid #ced4da; border-radius: 0.375rem;">
                            <span id="selected_product_name" class="text-muted">Pindai barcode untuk memilih produk...</span>
                        </div>
                        
                        <!-- Error message -->
                        <div id="product_error" class="invalid-feedback" style="display: none;">
                            Harus pindai barcode produk terlebih dahulu!
                        </div>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Tipe</label>
                        <select class="form-select @error('type') is-invalid @enderror" 
                                id="type" name="type" required>
                            <option value="">Pilih Tipe</option>
                            <option value="sale" {{ old('type') == 'sale' ? 'selected' : '' }}>Penjualan</option>
                            <option value="return" {{ old('type') == 'return' ? 'selected' : '' }}>Retur</option>
                            <option value="damage" {{ old('type') == 'damage' ? 'selected' : '' }}>Rusak</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
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
                               id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required>
                        <small class="form-text text-muted" id="stockInfo">
                            Stok tersedia: -
                        </small>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- HARGA PER UNIT -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Harga per Unit</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price', $autoPrice ? number_format($autoPrice, 0, ',', '.') : '') }}" 
                                   placeholder="0" required>
                        </div>
                        <div class="form-text">
                            💡 Harga otomatis dari harga jual produk. Bisa diubah manual jika perlu.
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Catatan</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" 
                                  id="note" name="note" rows="3" placeholder="Contoh: Penjualan ke customer...">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- BARCODE INFO - SIMPLE VERSION -->
                    <div class="mb-3">
                        <label class="form-label">Kode Barcode</label>
                        <div class="form-control-plaintext">
                            <span id="barcode-info" class="text-muted">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('stock-outs.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Stok Keluar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const barcodeInput = document.getElementById('barcode_scanner');
        const stockOutForm = document.getElementById('stockOutForm');
        const priceInput = document.getElementById('price');
        
        // Generate Invoice Number
        document.getElementById('generateInvoice').addEventListener('click', function() {
            fetch('{{ route("stock-outs.generate-invoice") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('invoice_number').value = data.invoice_number;
                });
        });

        // Barcode Scanner
        barcodeInput.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                let barcode = this.value.trim();
                
                if(barcode) {
                    fetch(`/api/products/search-by-barcode?barcode=${encodeURIComponent(barcode)}`)
                        .then(response => response.json())
                        .then(data => {
                            if(data.product) {
                                selectProductManually(data.product);
                                document.getElementById('scanned-product-name').textContent = data.product.name;
                                document.getElementById('scan-result').style.display = 'block';
                                document.getElementById('scan-error').style.display = 'none';
                                this.value = '';
                            } else {
                                document.getElementById('error-message').textContent = 'Barcode tidak ditemukan: ' + barcode;
                                document.getElementById('scan-result').style.display = 'none';
                                document.getElementById('scan-error').style.display = 'block';
                                this.value = '';
                            }
                        })
                        .catch(error => {
                            document.getElementById('error-message').textContent = 'Error: ' + error.message;
                            document.getElementById('scan-result').style.display = 'none';
                            document.getElementById('scan-error').style.display = 'block';
                            this.value = '';
                        });
                }
            }
        });

        function selectProductManually(product) {
            // 1. Set hidden input value
            document.getElementById('product_id').value = product.id;
            
            // 2. Update display
            document.getElementById('selected_product_name').textContent = `${product.name} (Stok: ${product.stock})`;
            document.getElementById('selected_product_name').className = 'text-success fw-bold';
            
            // 3. Update other fields
            document.getElementById('stockInfo').textContent = 'Stok tersedia: ' + product.stock;
            
            // Format harga
            let harga = parseFloat(product.selling_price);
            let hargaFormatted = new Intl.NumberFormat('id-ID').format(harga);
            document.getElementById('price').value = hargaFormatted;
            
            // Tampilkan kode barcode
            document.getElementById('barcode-info').textContent = product.barcode;
            document.getElementById('barcode-info').className = 'text-success fw-bold';
            
            // 4. Highlight effect
            const productDisplay = document.getElementById('product_display');
            productDisplay.style.border = '2px solid #28a745';
            productDisplay.style.backgroundColor = '#d4edda';
            document.getElementById('product_error').style.display = 'none';
            
            // 5. Focus ke quantity
            setTimeout(() => {
                document.getElementById('quantity').focus();
            }, 100);
        }

        // Format harga input saat user ketik
        priceInput.addEventListener('input', function(e) {
            let value = this.value.replace(/[^\d]/g, '');
            if(value) {
                let number = parseInt(value);
                this.value = new Intl.NumberFormat('id-ID').format(number);
            }
        });

        // Convert back to number sebelum submit
        stockOutForm.addEventListener('submit', function(e) {
            const productId = document.getElementById('product_id').value;
            if(!productId) {
                e.preventDefault();
                document.getElementById('product_error').style.display = 'block';
                document.getElementById('product_display').style.border = '1px solid #dc3545';
                document.getElementById('product_display').style.backgroundColor = '#f8d7da';
                alert('Harus pindai barcode produk terlebih dahulu!');
                barcodeInput.focus();
                return;
            }
            
            // Convert harga ke angka sebelum submit
            const priceValue = priceInput.value.replace(/[^\d]/g, '');
            priceInput.value = priceValue;
        });

        function clearScanner() {
            barcodeInput.value = '';
            barcodeInput.focus();
            document.getElementById('scan-result').style.display = 'none';
            document.getElementById('scan-error').style.display = 'none';
        }

        // Check stock availability
        document.getElementById('quantity').addEventListener('input', function() {
            const quantity = parseInt(this.value);
            const stockText = document.getElementById('stockInfo').textContent;
            const stockMatch = stockText.match(/Stok tersedia: (\d+)/);
            
            if(stockMatch) {
                const stock = parseInt(stockMatch[1]);
                const stockInfo = document.getElementById('stockInfo');
                
                if(quantity > stock) {
                    this.classList.add('is-invalid');
                    stockInfo.classList.add('text-danger');
                    stockInfo.classList.remove('text-muted');
                } else {
                    this.classList.remove('is-invalid');
                    stockInfo.classList.remove('text-danger');
                    stockInfo.classList.add('text-muted');
                }
            }
        });
    });
</script>
@endpush