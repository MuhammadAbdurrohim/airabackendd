@extends('admin.layouts.stisla')

@section('title', 'Manajemen Produk')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/stisla/modules/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/stisla/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endpush

@section('content')
<div class="section-header">
    <h1>Manajemen Produk</h1>
    <div class="section-header-button">
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Tambah Produk Baru</a>
    </div>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Daftar Produk</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                            {{ session('success') }}
                        </div>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped" id="products-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Gambar</th>
                                    <th>Nama Produk</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                             width="50" class="img-thumbnail">
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        <div class="badge badge-{{ $product->is_active ? 'success' : 'danger' }}">
                                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm" 
                                                onclick="confirmDelete('{{ $product->id }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $product->id }}" 
                                              action="{{ route('admin.products.destroy', $product->id) }}" 
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
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
@endsection

@push('scripts')
<script src="{{ asset('assets/stisla/modules/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/stisla/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/stisla/modules/sweetalert/sweetalert.min.js') }}"></script>

<script>
$(document).ready(function() {
    $('#products-table').DataTable();
});

function confirmDelete(productId) {
    swal({
        title: 'Apakah Anda yakin?',
        text: 'Produk yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            document.getElementById('delete-form-' + productId).submit();
        }
    });
}
</script>
@endpush
