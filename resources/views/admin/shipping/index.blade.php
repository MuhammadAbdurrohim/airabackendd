@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1>Shipping Management</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Update Shipping Information</h3>
        </div>
        <div class="card-body">
            <!-- Shipping Cost Estimation Form -->
            <form action="{{ route('admin.shipping.updateCosts') }}" method="POST" class="mb-4">
                @csrf
                <h4>Shipping Cost Estimation</h4>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Origin City</label>
                            <input type="text" name="origin_city" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Destination City</label>
                            <input type="text" name="destination_city" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cost per KG</label>
                            <input type="number" name="cost_per_kg" class="form-control" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Shipping Cost</button>
            </form>

            <hr>

            <!-- Tracking Number Update Form -->
            <form action="{{ route('admin.shipping.updateTracking') }}" method="POST">
                @csrf
                <h4>Update Tracking Number</h4>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Order ID</label>
                            <input type="text" name="order_id" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tracking Number</label>
                            <input type="text" name="tracking_number" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Courier</label>
                            <select name="courier" class="form-control" required>
                                <option value="jne">JNE</option>
                                <option value="tiki">TIKI</option>
                                <option value="pos">POS Indonesia</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Tracking</button>
            </form>
        </div>
    </div>

    <!-- Recent Shipments Table -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Recent Shipments</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Destination</th>
                        <th>Tracking Number</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shipments as $shipment)
                    <tr>
                        <td>{{ $shipment->order_id }}</td>
                        <td>{{ $shipment->order->user->name }}</td>
                        <td>{{ $shipment->order->shipping_address }}</td>
                        <td>{{ $shipment->tracking_number ?? 'Not assigned' }}</td>
                        <td>{{ $shipment->status }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $shipment->order_id) }}" class="btn btn-sm btn-info">
                                View Order
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
