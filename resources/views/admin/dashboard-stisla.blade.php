@extends('admin.layouts.stisla')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/stisla/modules/jqvmap/dist/jqvmap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/stisla/modules/summernote/summernote-bs4.css') }}">
<link rel="stylesheet" href="{{ asset('assets/stisla/modules/owlcarousel2/dist/assets/owl.carousel.min.css') }}">
@endpush

@section('content')
<div class="section-header">
    <h1>Dashboard</h1>
</div>

<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Pesanan</h4>
                </div>
                <div class="card-body">
                    {{ $totalOrders }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Pendapatan</h4>
                </div>
                <div class="card-body">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-warning">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Pengguna</h4>
                </div>
                <div class="card-body">
                    {{ $totalUsers }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-info">
                <i class="fas fa-video"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Live Streaming</h4>
                </div>
                <div class="card-body">
                    {{ $activeLiveStreams }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-md-12 col-12 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>Statistik Penjualan</h4>
                <div class="card-header-action">
                    <div class="btn-group">
                        <button class="btn btn-primary" id="week">Minggu Ini</button>
                        <button class="btn" id="month">Bulan Ini</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="182"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-12 col-12 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>Aktivitas Terkini</h4>
            </div>
            <div class="card-body">
                <ul class="list-unstyled list-unstyled-border">
                    @foreach($recentActivities as $activity)
                    <li class="media">
                        <div class="media-icon"><i class="far fa-circle"></i></div>
                        <div class="media-body">
                            <div class="media-title">{{ $activity->title }}</div>
                            <span class="text-small text-muted">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    </li>
                    @endforeach
                </ul>
                <div class="pt-1 pb-1 text-center">
                    <a href="#" class="btn btn-primary btn-lg btn-round">
                        Lihat Semua
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Pesanan Terbaru</h4>
                <div class="card-header-action">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>
                                    <div class="badge badge-{{ $order->status_color }}">{{ $order->status_label }}</div>
                                </td>
                                <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-primary btn-sm">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-hero">
            <div class="card-header">
                <div class="card-icon">
                    <i class="far fa-question-circle"></i>
                </div>
                <h4>{{ $pendingComplaints }} Komplain</h4>
                <div class="card-description">Menunggu respon</div>
            </div>
            <div class="card-body p-0">
                <div class="tickets-list">
                    @foreach($recentComplaints as $complaint)
                    <a href="{{ route('admin.orders.show', $complaint->order_id) }}" class="ticket-item">
                        <div class="ticket-title">
                            <h4>{{ $complaint->title }}</h4>
                        </div>
                        <div class="ticket-info">
                            <div>{{ $complaint->user->name }}</div>
                            <div class="bullet"></div>
                            <div class="text-primary">{{ $complaint->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                    @endforeach
                    <a href="{{ route('admin.orders.index', ['filter' => 'complaints']) }}" class="ticket-item ticket-more">
                        Lihat Semua <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/stisla/modules/chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                label: 'Penjualan',
                data: {!! json_encode($chartData['data']) !!},
                borderWidth: 2,
                backgroundColor: 'rgba(63,82,227,.8)',
                borderWidth: 0,
                borderColor: 'transparent',
                pointBorderWidth: 0,
                pointRadius: 3.5,
                pointBackgroundColor: 'transparent',
                pointHoverBackgroundColor: 'rgba(63,82,227,.8)',
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false,
                    },
                    ticks: {
                        stepSize: 1000000,
                        callback: function(value) {
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }],
                xAxes: [{
                    gridLines: {
                        color: '#fbfbfb',
                        lineWidth: 2
                    }
                }]
            },
        }
    });

    // Toggle between week and month view
    document.getElementById('week').addEventListener('click', function() {
        this.classList.add('btn-primary');
        document.getElementById('month').classList.remove('btn-primary');
        updateChart('week');
    });

    document.getElementById('month').addEventListener('click', function() {
        this.classList.add('btn-primary');
        document.getElementById('week').classList.remove('btn-primary');
        updateChart('month');
    });

    function updateChart(period) {
        axios.get(`/admin/dashboard/chart-data?period=${period}`)
            .then(response => {
                salesChart.data.labels = response.data.labels;
                salesChart.data.datasets[0].data = response.data.data;
                salesChart.update();
            })
            .catch(error => console.error('Error fetching chart data:', error));
    }
});
</script>
@endpush
