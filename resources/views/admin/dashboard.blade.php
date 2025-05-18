@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-box"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Products</span>
                        <span class="info-box-number">{{ $stats['total_products'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Transactions</span>
                        <span class="info-box-number">{{ $stats['total_transactions'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Orders</span>
                        <span class="info-box-number">{{ $stats['active_orders'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-video"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Live Streams</span>
                        <span class="info-box-number">{{ $stats['live_streams'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Revenue</span>
                        <span class="info-box-number">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Monthly Orders -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Orders per Month ({{ date('Y') }})</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyOrdersChart" style="min-height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Best Selling Products -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Best Selling Products</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table m-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Total Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['best_selling_products'] as $product)
                                    <tr>
                                        <td>{{ $product->product->name }}</td>
                                        <td>{{ $product->total_sold }}</td>
                                        <td>Rp {{ number_format($product->total_sold * $product->product->price, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Stream Status -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Live Streaming Status</h3>
                    </div>
                    <div class="card-body">
                        @if($activeStream)
                            <div class="alert alert-success">
                                <h5><i class="icon fas fa-broadcast-tower"></i> Live Stream Active!</h5>
                                <p>Title: {{ $activeStream->title }}</p>
                                <p>Viewers: <span id="viewer-count">{{ $activeStream->viewer_count }}</span></p>
                                <p>Duration: <span id="stream-duration">{{ $activeStream->created_at->diffForHumans() }}</span></p>
                            </div>
                            <a href="{{ route('admin.streaming.dashboard') }}" class="btn btn-primary">Go to Streaming Dashboard</a>
                        @else
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> No Active Stream</h5>
                                <p>Start a new live stream to engage with your customers!</p>
                            </div>
                            <a href="{{ route('admin.streaming.create') }}" class="btn btn-success">Start New Stream</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Transactions</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table m-0">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['recent_transactions'] as $order)
                                    <tr>
                                        <td><a href="{{ route('admin.orders.show', $order->id) }}">#{{ $order->id }}</a></td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $order->status == 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Orders Chart
const monthlyOrdersCtx = document.getElementById('monthlyOrdersChart').getContext('2d');
const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const monthlyData = @json($stats['monthly_orders']);

window.monthlyOrdersChart = new Chart(monthlyOrdersCtx, {
    type: 'bar',
    data: {
        labels: monthNames,
        datasets: [{
            label: 'Orders',
            data: monthNames.map((_, index) => monthlyData[index + 1] || 0),
            backgroundColor: 'rgba(60, 141, 188, 0.8)',
            borderColor: 'rgba(60, 141, 188, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Real-time updates using AJAX
function updateDashboardStats() {
    $.ajax({
        url: '{{ route("admin.dashboard.stats") }}',
        method: 'GET',
        success: function(response) {
            // Update stats
            $('.active-orders-count').text(response.active_orders);
            $('.live-viewers-count').text(response.live_viewers);
            $('.total-transactions-count').text(response.total_transactions);
            
            // Update recent orders table
            let ordersHtml = '';
            response.recent_orders.forEach(function(order) {
                ordersHtml += `
                    <tr>
                        <td><a href="/admin/orders/${order.id}">#${order.id}</a></td>
                        <td>${order.user.name}</td>
                        <td>Rp ${number_format(order.total_amount)}</td>
                        <td><span class="badge badge-${order.status == 'completed' ? 'success' : 'warning'}">${order.status}</span></td>
                    </tr>
                `;
            });
            $('#recent-orders-table tbody').html(ordersHtml);

            // Update monthly orders chart
            if (window.monthlyOrdersChart) {
                window.monthlyOrdersChart.data.datasets[0].data = monthNames.map((_, index) => 
                    response.monthly_orders[index + 1] || 0
                );
                window.monthlyOrdersChart.update();
            }

            // Update best selling products table
            let productsHtml = '';
            response.best_selling_products.forEach(function(product) {
                productsHtml += `
                    <tr>
                        <td>${product.product.name}</td>
                        <td>${product.total_sold}</td>
                        <td>Rp ${number_format(product.total_sold * product.product.price)}</td>
                    </tr>
                `;
            });
            $('#best-selling-products-table tbody').html(productsHtml);
        }
    });
}

// Update stats every 30 seconds
setInterval(updateDashboardStats, 30000);

function number_format(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}
</script>
@endpush
