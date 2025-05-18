@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Live Streaming Dashboard</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if($activeStream)
            <div class="row">
                <!-- Stream Info -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $activeStream->title }}</h3>
                            <div class="card-tools">
                                <span class="badge badge-success">Live</span>
                                <span class="badge badge-info ml-2">
                                    <i class="fas fa-users"></i> {{ $activeStream->viewer_count }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- ZegoCloud Stream Player -->
                            <div id="zego-player" class="mb-3" style="width: 100%; height: 400px;"></div>
                            
                            <div class="stream-controls mt-3">
                                <button class="btn btn-danger" onclick="endStream()">
                                    <i class="fas fa-stop-circle"></i> End Stream
                                </button>
                                <button class="btn btn-primary" onclick="toggleProducts()">
                                    <i class="fas fa-box"></i> Manage Products
                                </button>
                                <button class="btn btn-success" onclick="exportComments()">
                                    <i class="fas fa-file-export"></i> Export Comments
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Comments -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Live Comments</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="direct-chat-messages" id="comments-container" style="height: 400px;">
                                @foreach($activeStream->comments as $comment)
                                <div class="direct-chat-msg">
                                    <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-name float-left">{{ $comment->user->name }}</span>
                                        <span class="direct-chat-timestamp float-right">
                                            {{ $comment->created_at->format('H:i') }}
                                        </span>
                                    </div>
                                    <div class="direct-chat-text @if($comment->is_order) bg-warning @endif">
                                        {{ $comment->content }}
                                        @if($comment->is_order)
                                        <br>
                                        <small>Order: {{ $comment->order_code }} ({{ $comment->order_quantity }}pcs)</small>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Modal -->
            <div class="modal fade" id="productsModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Manage Stream Products</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Current Products</h5>
                                    <ul class="list-group mb-3" id="current-products">
                                        @foreach($activeStream->products as $product)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $product->name }}
                                            <button class="btn btn-sm btn-danger" onclick="removeProduct({{ $product->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </li>
                                        @endforeach
                                    </ul>

                                    <h5>Available Products</h5>
                                    <ul class="list-group" id="available-products">
                                        @foreach($availableProducts as $product)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $product->name }}
                                            <button class="btn btn-sm btn-success" onclick="addProduct({{ $product->id }})">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @else
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-center">No active stream. Start a new stream to begin.</p>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#startStreamModal">
                                    <i class="fas fa-play-circle"></i> Start New Stream
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
</div>

<!-- Start Stream Modal -->
<div class="modal fade" id="startStreamModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.streaming.start') }}" method="POST">
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
                        <label>Select Products</label>
                        <select name="products[]" class="form-control select2" multiple required>
                            @foreach($availableProducts as $product)
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
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('zegocloud-sdk.js') }}"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();

    // Initialize ZegoCloud player if stream is active
    @if($activeStream)
    initializeZegoPlayer();
    initializeCommentUpdates();
    @endif
});

function initializeZegoPlayer() {
    const zp = new ZegoExpressEngine({{ config('services.zego.app_id') }}, '{{ $activeStream->stream_token }}');
    zp.loginRoom('{{ $activeStream->room_id }}', {
        userID: 'admin',
        userName: 'Admin'
    }).then(() => {
        zp.startPublishingStream('{{ $activeStream->stream_id }}');
    });
}

function initializeCommentUpdates() {
    setInterval(() => {
        $.get('{{ route("admin.streaming.comments", $activeStream->id) }}', function(response) {
            updateComments(response.data);
        });
    }, 5000);
}

function updateComments(comments) {
    const container = $('#comments-container');
    container.html('');
    
    comments.forEach(comment => {
        container.append(`
            <div class="direct-chat-msg">
                <div class="direct-chat-infos clearfix">
                    <span class="direct-chat-name float-left">${comment.user.name}</span>
                    <span class="direct-chat-timestamp float-right">${moment(comment.created_at).format('HH:mm')}</span>
                </div>
                <div class="direct-chat-text ${comment.is_order ? 'bg-warning' : ''}">
                    ${comment.content}
                    ${comment.is_order ? `<br><small>Order: ${comment.order_code} (${comment.order_quantity}pcs)</small>` : ''}
                </div>
            </div>
        `);
    });
    
    container.scrollTop(container[0].scrollHeight);
}

function endStream() {
    if (confirm('Are you sure you want to end the stream?')) {
        $.post('{{ route("admin.streaming.end", $activeStream->id) }}', {
            _token: '{{ csrf_token() }}'
        }, function(response) {
            window.location.reload();
        });
    }
}

function exportComments() {
    window.location.href = '{{ route("admin.streaming.export-comments", $activeStream->id) }}';
}

function toggleProducts() {
    $('#productsModal').modal('show');
}

function addProduct(productId) {
    $.post('{{ route("admin.streaming.add-product") }}', {
        _token: '{{ csrf_token() }}',
        stream_id: '{{ $activeStream->id }}',
        product_id: productId
    }, function(response) {
        window.location.reload();
    });
}

function removeProduct(productId) {
    $.post('{{ route("admin.streaming.remove-product") }}', {
        _token: '{{ csrf_token() }}',
        stream_id: '{{ $activeStream->id }}',
        product_id: productId
    }, function(response) {
        window.location.reload();
    });
}
</script>
@endpush
