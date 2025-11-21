@extends('layouts.app')

@section('title', 'Laporan Pendapatan')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Pendapatan</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button class="btn btn-success me-2" onclick="window.print()">
            <i class="fas fa-print me-1"></i>Print
        </button>
        <button class="btn btn-primary" onclick="exportToExcel()">
            <i class="fas fa-file-excel me-1"></i>Export Excel
        </button>
    </div>
</div>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.income') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ $startDate }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ $endDate }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Pembelian</h5>
                        <h3 class="mb-0">
                            Rp {{ number_format($stockIns->flatten()->sum('total'), 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-success mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Penjualan</h5>
                        <h3 class="mb-0">
                            Rp {{ number_format($stockOuts->flatten()->where('type', 'sale')->sum('total'), 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-cash-register fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-info mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Laba Kotor</h5>
                        <h3 class="mb-0">
                            Rp {{ number_format($stockOuts->flatten()->where('type', 'sale')->sum('total') - $stockIns->flatten()->sum('total'), 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Rincian Pendapatan per Tanggal</h5>
        <div class="table-responsive">
            <table class="table table-striped" id="incomeTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pembelian</th>
                        <th>Penjualan</th>
                        <th>Laba Kotor</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $allDates = array_unique(array_merge(
                            $stockIns->keys()->toArray(),
                            $stockOuts->keys()->toArray()
                        ));
                        sort($allDates);
                    @endphp
                    
                    @foreach($allDates as $date)
                    @php
                        $purchase = $stockIns[$date]->sum('total') ?? 0;
                        $sales = $stockOuts[$date]->where('type', 'sale')->sum('total') ?? 0;
                        $profit = $sales - $purchase;
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                        <td class="text-danger">Rp {{ number_format($purchase, 0, ',', '.') }}</td>
                        <td class="text-success">Rp {{ number_format($sales, 0, ',', '.') }}</td>
                        <td class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($profit, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-primary">
                        <th>Total</th>
                        <th class="text-danger">Rp {{ number_format($stockIns->flatten()->sum('total'), 0, ',', '.') }}</th>
                        <th class="text-success">Rp {{ number_format($stockOuts->flatten()->where('type', 'sale')->sum('total'), 0, ',', '.') }}</th>
                        <th class="{{ ($stockOuts->flatten()->where('type', 'sale')->sum('total') - $stockIns->flatten()->sum('total')) >= 0 ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($stockOuts->flatten()->where('type', 'sale')->sum('total') - $stockIns->flatten()->sum('total'), 0, ',', '.') }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#incomeTable').DataTable({
            order: [[0, 'desc']],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });

    function exportToExcel() {
        let table = document.getElementById('incomeTable');
        let html = table.outerHTML;
        let url = 'data:application/vnd.ms-excel,' + escape(html);
        let link = document.createElement('a');
        link.href = url;
        link.download = 'laporan-pendapatan-' + new Date().toISOString().split('T')[0] + '.xls';
        link.click();
    }
</script>
@endpush