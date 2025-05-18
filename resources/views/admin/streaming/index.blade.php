@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Live Streaming</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#startStreamModal">
                            <i class="fas fa-play-circle"></i> Start New Stream
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Active Stream -->
            @if($activeStream = $streams->firstWhere('status', 'active'))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-gradient-success">
                        <div class="card-header">
                            <h3 class="card-title">Active Stream</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4>{{ $activeStream->title }}</h4>
                                    <p>{{ $activeStream->description }}</p>
                                    <p>
                                        <i class="fas fa-users"></i> {{ $activeStream->viewer_count }} viewers
                                        <span class="ml-3">
                                            <i class="fas fa-clock"></i> Started {{ $activeStream->started_at->diffForHumans() }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <a href="{{ route('admin.streaming.dashboard') }}" class="btn btn-light">
                                        <i class="fas fa-video"></i> Go to Stream
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Past Streams -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Past Streams</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Date</th>
                                        <th>Duration</th>
                                        <th>Viewers</th>
                                        <th>Products</th>
                                        <th>Comments</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($streams->where('status', '!=', 'active') as $stream)
                                    <tr>
                                        <td>{{ $stream->title }}</td>
                                        <td>{{ $stream->started_at ? $stream->started_at->format('Y-m-d H:i') : 'Not started' }}</td>
                                        <td>
                                            @if($stream->started_at && $stream->ended_at)
                                                {{ $stream->started_at->diffInMinutes($stream->ended_at) }} minutes
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $stream->viewer_count }}</td>
                                        <td>{{ $stream->products->count() }}</td>
                                        <td>{{ $stream->comments->count() }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" onclick="viewStreamDetails({{ $stream->id }})">
                                                        <i class="fas fa-eye"></i> View Details
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('admin.streaming.export-comments', $stream->id) }}">
                                                        <i class="fas fa-file-export"></i> Export Comments
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteStream({{ $stream->id }})">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer clearfix">
                            {{ $streams->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Start Stream Modal -->
<div class="modal fade" id="startStreamModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.streaming.start') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Start New Stream</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Stream Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Thumbnail</label>
                        <div class="custom-file">
                            <input type="file" name="thumbnail" class="custom-file-input" accept="image/*" required>
                            <label class="custom-file-label">Choose file</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Select Products</label>
                        <select name="products[]" class="form-control select2" multiple required>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Start Stream</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stream Details Modal -->
<div class="modal fade" id="streamDetailsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Stream Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="streamDetailsContent">
                    Loading...
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();
    
    // Initialize custom file input
    bsCustomFileInput.init();
});

function viewStreamDetails(streamId) {
    $('#streamDetailsModal').modal('show');
    $('#streamDetailsContent').load(`{{ url('admin/streaming') }}/${streamId}/details`);
}

function deleteStream(streamId) {
    if (confirm('Are you sure you want to delete this stream? This action cannot be undone.')) {
        $.post(`{{ url('admin/streaming') }}/${streamId}`, {
            _token: '{{ csrf_token() }}',
            _method: 'DELETE'
        }, function(response) {
            window.location.reload();
        });
    }
}
</script>
@endpush
