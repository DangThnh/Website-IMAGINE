@extends('layouts.layout')
@section('title', $collection->title)
@section('styles')
<style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .collection-header {
        margin-bottom: 30px;
    }
    .collection-header h1 {
        margin-bottom: 10px;
    }
    .collection-actions {
        margin-top: 15px;
        display: flex;
        gap: 10px;
    }
    .alert-success {
        padding: 15px;
        margin-bottom: 20px;
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
    }
    /* CSS cho grid ảnh hiển thị ngang */
    .collection-grid {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        gap: 15px;
        overflow-x: auto;
        padding: 15px 0;
        width: 100%;
        min-height: 250px;
        scroll-behavior: smooth;
    }
    .collection-image {
        flex: 0 0 auto;
        width: 220px;
        height: 220px;
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        cursor: move; /* Cursor thể hiện có thể kéo thả */
    }
    .collection-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .image-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.7);
        display: flex;
        justify-content: center;
        gap: 10px;
        padding: 10px;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .collection-image:hover .image-overlay {
        opacity: 1;
    }
    .view-btn, .remove-btn {
        padding: 5px 10px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
    }
    .view-btn {
        background: #007BFF;
        color: white;
    }
    .remove-btn {
        background: #dc3545;
        color: white;
    }

    /* Lightbox Modal Styles */
    .lightbox-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .lightbox-modal.active {
        display: flex;
        opacity: 1;
    }
    
    .lightbox-content {
        position: relative;
        max-width: 90%;
        max-height: 90%;
        margin: auto;
    }
    
    .lightbox-image {
        display: block;
        max-width: 100%;
        max-height: 90vh;
        margin: 0 auto;
        box-shadow: 0 5px 15px rgba(0,0,0,0.5);
    }
    
    .lightbox-close {
        position: absolute;
        top: -40px;
        right: 0;
        font-size: 30px;
        color: white;
        background: transparent;
        border: none;
        cursor: pointer;
    }
    
    .lightbox-caption {
        color: white;
        text-align: center;
        padding: 10px;
        width: 100%;
    }
    
    .lightbox-nav {
        position: absolute;
        top: 50%;
        width: 100%;
        display: flex;
        justify-content: space-between;
        transform: translateY(-50%);
    }
    
    .lightbox-prev, .lightbox-next {
        background: rgba(0,0,0,0.5);
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        margin: 0 20px;
    }
    .collection-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="collection-header">
        <h1>{{ $collection->title }}</h1>
        <p>{{ $collection->description }}</p>
        
        @auth
            @if(Auth::id() == $collection->user_id)
                <div class="collection-actions">
                    <a href="{{ route('collections.edit', $collection->id) }}" class="btn btn-secondary">Edit Collection</a>
                    <form action="{{ route('collections.destroy', $collection->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this collection?')">Delete Collection</button>
                    </form>
                </div>
            @endif
        @endauth
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($collection->images->isEmpty())
        <p>No images in this collection yet.</p>
    @else
    <div class="collection-grid" id="sortable-images">
        @foreach($collection->images as $image)
            <div class="collection-image" data-image-id="{{ $image->id }}">
                <a href="{{ route('images.showDetail', $image->id) }}">
                    <img src="{{ asset($image->path) }}" alt="{{ $image->filename }}">
                </a>
                <div class="image-overlay">
                    <button class="view-btn" onclick="openLightbox('{{ asset($image->path) }}', '{{ $image->filename }}', {{ $loop->index }})">View</button>
                    @auth
                        @if(Auth::id() == $collection->user_id)
                            <form action="{{ route('collections.removeImage', ['collectionId' => $collection->id, 'imageId' => $image->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="remove-btn" onclick="return confirm('Remove this image from the collection?')">Remove</button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        @endforeach
    </div>
@endif

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('collections.index') }}" class="btn btn-primary" style="padding: 10px 15px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">Return</a>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="lightbox-modal" class="lightbox-modal">
    <div class="lightbox-content">
        <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
        <img id="lightbox-image" class="lightbox-image" src="" alt="">
        <div class="lightbox-caption" id="lightbox-caption"></div>
        <div class="lightbox-nav">
            <button class="lightbox-prev" onclick="changeImage(-1)">&lt;</button>
            <button class="lightbox-next" onclick="changeImage(1)">&gt;</button>
        </div>
    </div>
</div>

@auth
    @if(Auth::id() == $collection->user_id)
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script>
            $(function() {
                $("#sortable-images").sortable({
                    update: function(event, ui) {
                        const imageIds = $(this).sortable('toArray', {attribute: 'data-image-id'});
                        $.ajax({
                            url: '{{ route('collections.updateImageOrder', $collection->id) }}',
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            data: {
                                images: imageIds
                            },
                            success: function(response) {
                                console.log('Order updated successfully');
                            },
                            error: function(xhr) {
                                console.error('Error updating order');
                            }
                        });
                    }
                });
            });
        </script>
    @endif
@endauth

<!-- Lightbox JavaScript -->
<script>
    // Lưu trữ thông tin ảnh trong collection
    const collectionImages = [
        @foreach($collection->images as $image)
            {
                src: "{{ asset($image->path) }}",
                caption: "{{ $image->filename }}"
            },
        @endforeach
    ];
    
    let currentImageIndex = 0;
    
    function openLightbox(src, caption, index) {
        document.getElementById('lightbox-image').src = src;
        document.getElementById('lightbox-caption').textContent = caption;
        document.getElementById('lightbox-modal').classList.add('active');
        currentImageIndex = index;
        
        // Ngăn cuộn trang khi lightbox đang mở
        document.body.style.overflow = 'hidden';
    }
    
    function closeLightbox() {
        document.getElementById('lightbox-modal').classList.remove('active');
        // Cho phép cuộn trang trở lại
        document.body.style.overflow = 'auto';
    }
    
    function changeImage(direction) {
        currentImageIndex += direction;
        
        // Xử lý khi vượt quá giới hạn
        if (currentImageIndex < 0) {
            currentImageIndex = collectionImages.length - 1;
        } else if (currentImageIndex >= collectionImages.length) {
            currentImageIndex = 0;
        }
        
        // Cập nhật ảnh và caption
        document.getElementById('lightbox-image').src = collectionImages[currentImageIndex].src;
        document.getElementById('lightbox-caption').textContent = collectionImages[currentImageIndex].caption;
    }
    
    // Đóng lightbox khi click bên ngoài ảnh
    document.getElementById('lightbox-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });
    
    // Xử lý các phím tắt
    document.addEventListener('keydown', function(e) {
        if (!document.getElementById('lightbox-modal').classList.contains('active')) return;
        
        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowLeft') {
            changeImage(-1);
        } else if (e.key === 'ArrowRight') {
            changeImage(1);
        }
    });
</script>
@endsection