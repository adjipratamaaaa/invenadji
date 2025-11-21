<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Produk - {{ $product->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .barcode-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        .barcode-image {
            text-align: center;
            margin: 20px 0;
        }
        .product-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="barcode-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Barcode Produk</h2>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
            </div>

            <div class="product-info">
                <h5>{{ $product->name }}</h5>
                <p class="mb-1"><strong>Kode:</strong> {{ $product->code }}</p>
                <p class="mb-1"><strong>Barcode:</strong> {{ $product->barcode }}</p>
                <p class="mb-0"><strong>Harga:</strong> Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
            </div>

            <div class="barcode-image">
                {!! $barcodeImage !!}
                <p class="mt-2 text-muted">{{ $product->barcode }}</p>
            </div>

            <div class="text-center">
                <a href="{{ route('products.download-barcode', $product->id) }}" 
                   class="btn btn-success me-2">
                   📥 Download Barcode
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    🖨️ Print Barcode
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>