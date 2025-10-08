<div class="modal2" id="collectionModal">
    <div class="modal-content2">
        <span class="close-button2" id="closeCollectionModal">×</span>
        <div class="modal-header">
            <h3 class="modal-title">Save to Collection</h3>
        </div>

        <div class="modal-body">
            @if(auth()->check())
                <!-- Hiển thị danh sách collection (bên trái) -->
                <div class="collections-list">
                    @if(isset($collections) && $collections->count() > 0)
                        @foreach($collections as $collection)
                            <div class="collection-item-container">
                                @if($collection->images->isNotEmpty())
                                    <img src="{{ asset($collection->images->first()->path) }}" alt="{{ $collection->title }}">
                                @else
                                    <div class="no-thumbnail">No Images</div>
                                @endif
                                <a href="#" class="modal-button2 save-to-collection"
                                    data-collection="{{ $collection->id }}"
                                    data-image="{{ $image->id }}">
                                    {{ $collection->title }}
                                </a>
                            </div>
                        @endforeach
                    @else
                        <p style="text-align: center; margin: 10px 0;">Bạn chưa có bộ sưu tập nào</p>
                    @endif
                </div>

                <!-- Form tạo collection mới (bên phải) -->
                <div class="create-collection-container">
                    <h4>Tạo bộ sưu tập mới</h4>
                    <form id="create-collection-form">
                        @csrf
                        <input type="text" name="title" placeholder="Tên bộ sưu tập" required>

                        <div class="checkbox-container">
                            <input type="checkbox" name="is_public" id="is_public" value="1" checked>
                            <label for="is_public">Công khai</label>
                        </div>

                        <button type="submit" class="modal-button2" style="width: 100%;">Tạo và lưu</button>
                    </form>
                </div>
            @else
                <p style="text-align: center; margin: 20px 0;">
                    Vui lòng <a href="{{ url('/login') }}" style="color: #4CAF50;">đăng nhập</a> để lưu vào bộ sưu tập
                </p>
            @endif
        </div>
    </div>
</div>
