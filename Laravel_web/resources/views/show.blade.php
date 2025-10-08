@extends('layouts.layout')
<title>{{ $image->user->name }} - {{ $image->filename }}</title>
@section('content')

    <link rel="stylesheet" href="{{ asset('/assets/css/styleMainPage.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/searchBar.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/show.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/commentify.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/popup.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/collectionAdd.css') }}">
    <title>Image Details</title>
    <style>
        body {
            background-color: rgb(255, 255, 255); /* Màu nền */
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            max-width: none; /* Cho phép ảnh không bị giới hạn chiều rộng */
            max-height: none; /* Cho phép ảnh không bị giới hạn chiều cao */
            cursor: grab; /* Đổi con trỏ khi di chuyển */
        }

        .close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        .content-wrapper {
            display: flex; /* Sử dụng flexbox để chia đôi */
            margin: 20px;
        }

        .left-pane {
            flex: 2; /* Chiếm 2 phần */
            margin-right: 20px; /* Khoảng cách bên phải */
        }

        .right-pane {
            flex: 1; /* Chiếm 1 phần */
            background-color: #f0f0f0; /* Màu nền khung bên phải */
            padding: 15px; /* Padding cho khung */
            border-radius: 8px; /* Bo góc cho khung */
        }

        .image-thumbnail {
            margin-bottom: 10px; /* Khoảng cách giữa các hình thu nhỏ */
        }
        @media (max-width: 768px) {
        .modal-content2 {
            flex-direction: column; /* Đổi thành column trên màn hình nhỏ */
            max-width: 90%;
            padding: 20px;
            gap: 20px;
        }
        .collections-list {
            max-height: 300px;
            padding-right: 0;
            border-right: none;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
            width: 100%; /* Đảm bảo chiếm toàn bộ chiều rộng */
        }
        .create-collection-container {
            padding-left: 0;
            width: 100%; /* Đảm bảo chiếm toàn bộ chiều rộng */
        }
        .collection-item-container img,
        .collection-item-container .no-thumbnail {
            width: 70px;
            height: 70px;
        }
    }
    </style>

    <div class="container">
        <div class="content-wrapper">
            <div class="left-pane">
                <img src="{{ asset($image->path) }}" alt="{{ $image->filename }}" class="img-fluid"
                     style="width: 100%; height: 600px; object-fit: cover; cursor: pointer;"
                     onclick="openModal('{{ asset($image->path) }}')"> <!-- Thêm sự kiện onclick -->


                    <div class="mt-3" style="flex: 50%;">
                    <h2 style="font-size: 200%;">{{ $image->filename }}</h2>
                    <p>
                        <strong>Author:</strong>
                        <a href="{{ route('profile.showUser', $image->user->id) }}" style="color: inherit; text-decoration: none;">
                            {{ $image->user->name }}
                        </a>
                    </p>
                    <p><strong>Description:</strong> {{ $image->description ?? 'No description available' }}</p>
                    @if (auth()->check() && auth()->user()->id === $image->user_id)
                                <a href="{{ route('images.edit', $image->id) }}" class="btn btn-warning">Edit Image</a>
                    @endif
                    <button id="add-link" class="inside_button" style="margin-top: 20px;">Add to Collection</button>

                </div>
            </div>

            <div class="right-pane">
                <h3>Other Works by {{ $image->user->name }}</h3>
                @if ($otherImages->isEmpty())
                    <p>No other images by this author.</p>
                @else
                    <div class="image-gallery" style="display: flex; flex-wrap: wrap; gap: 10px;"> <!-- Flexbox container -->
                        @foreach ($otherImages as $otherImage)
                            <div class="image-thumbnail" style="flex: 1 1 auto; max-width: 175px;"> <!-- Thay đổi flex để điều chỉnh kích thước -->
                                <a href="{{ url('/images', $otherImage->id) }}">
                                    <img src="{{ asset($otherImage->path) }}" alt="{{ $otherImage->filename }}" style="width: 100%; height: auto;">
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div style="margin-top: 15px;">
                    </div>
                @endif
                <a href="{{ route('profile.showUser', $image->user->id) }}" style="color: blue; text-decoration: underline;">
                    See more of this author
                </a>
            </div>
        </div>
    </div>

    <div class="comment-section">
        @foreach ($take as $image)
            <livewire:comments :model="$image"/>
        @endforeach
    </div>
    <!-- Include modal partial -->
    @include('partials.collection-modal')

    <div style="text-align: center; margin-top: 20px;">
        <a href="javascript:history.back()" class="back-button"
            style="padding: 10px 15px; background-color: #007BFF;
            color: white; text-decoration: none; border-radius: 5px;">
            Return
        </a>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage" draggable="false">
    </div>

    <script>
        let isDragging = false;
        let startX, startY, offsetX = 0, offsetY = 0;

        function openModal(imageSrc) {
            const modal = document.getElementById("myModal");
            const modalImage = document.getElementById("modalImage");
            modal.style.display = "flex"; // Hiện modal
            modalImage.src = imageSrc; // Đặt src cho ảnh trong modal
            modalImage.onload = function() {
                // Reset vị trí khi ảnh được tải
                modalImage.style.transform = `translate(0px, 0px)`; // Đặt lại vị trí ảnh
            }
        }

        function closeModal() {
            const modal = document.getElementById("myModal");
            modal.style.display = "none"; // Ẩn modal
        }

        // Đóng modal khi nhấn ra ngoài ảnh
        window.onclick = function(event) {
            const modal = document.getElementById("myModal");
            if (event.target === modal) {
                closeModal();
            }
        }

        // Kéo thả ảnh
        const modalImage = document.getElementById("modalImage");
        modalImage.onmousedown = function(event) {
            isDragging = true;
            startX = event.clientX - offsetX;
            startY = event.clientY - offsetY;
            modalImage.style.cursor = "grabbing"; // Đổi con trỏ khi kéo
        };

        modalImage.onmouseup = function() {
            isDragging = false;
            modalImage.style.cursor = "grab"; // Đổi lại con trỏ
        };

        modalImage.onmouseleave = function() {
            isDragging = false;
            modalImage.style.cursor = "grab"; // Đổi lại con trỏ
        };

        modalImage.onmousemove = function(event) {
            if (isDragging) {
                event.preventDefault();
                offsetX = event.clientX - startX;
                offsetY = event.clientY - startY;
                modalImage.style.transform = `translate(${offsetX}px, ${offsetY}px)`; // Di chuyển ảnh
            }
        };

        // Cuộn chuột để di chuyển ảnh
        modalImage.onwheel = function(event) {
            event.preventDefault();
            offsetY += event.deltaY; // Thay đổi offsetY theo cuộn chuột
            modalImage.style.transform = `translate(${offsetX}px, ${offsetY}px)`; // Di chuyển ảnh
        };
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const addToCollectionButton = document.getElementById('add-link');
    const collectionModal = document.getElementById('collectionModal');
    const closeCollectionModal = document.getElementById('closeCollectionModal');
    const saveButtons = document.querySelectorAll('.save-to-collection');
    const createForm = document.getElementById('create-collection-form');

    // Show modal when clicking "Add to Collection"
    if (addToCollectionButton) {
        addToCollectionButton.addEventListener('click', function(e) {
            e.preventDefault();
            collectionModal.style.display = 'block';
        });
    }

    // Close modal with X button
    if (closeCollectionModal) {
        closeCollectionModal.addEventListener('click', () => {
            collectionModal.style.display = 'none';
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target === collectionModal) {
            collectionModal.style.display = 'none';
        }
    });

    // Handle saving image to existing collections
    saveButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const collectionId = this.getAttribute('data-collection');
            const imageId = this.getAttribute('data-image');

            if (!collectionId || !imageId) {
                alert('Thiếu thông tin collection hoặc image.');
                return;
            }

            fetch('/collections/add-image', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector
                    ('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                    collection_id: collectionId,
                    image_id: imageId
                })
            })

            .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
            .then(data => {

                if (data.success) {
                    alert('Đã lưu ảnh vào bộ sưu tập!');
                    collectionModal.style.display = 'none';
                } else {
                alert('Lỗi: ' + (data.error || 'Không thể lưu ảnh')
                + (data.details ? ' - ' + data.details : ''));
                }
            })
            .catch(error => {
            console.error('Fetch error:', error);
            alert('Đã xảy ra lỗi khi lưu ảnh');
        });
    });
});

    // Handle creating a new collection and adding image
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Tạo FormData từ form
            const formData = new FormData(this);
            formData.append('image_id', '{{ $image->id }}'); // Thêm image_id từ Blade

            if (!formData.has('is_public')) {
                formData.append('is_public', '0');
            }
            // _token đã được thêm tự động bởi @csrf trong form

            // Debug: Kiểm tra dữ liệu gửi đi
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            fetch('/collections', {
                method: 'POST',
                body: formData, // Gửi FormData thay vì JSON
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Không cần Content-Type, FormData tự xử lý
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw errorData; // Ném lỗi để xử lý trong catch
                    });
                }
                return response.json();
            })
            .then(data => {
                alert(data.success); // Hiển thị thông báo thành công
                collectionModal.style.display = 'none'; // Đóng modal
                // Tùy chọn: Reload trang để cập nhật danh sách collections
                 window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                if (error.messages) {
                    // Xử lý lỗi validation
                    let errorMsg = 'Lỗi:\n';
                    for (let field in error.messages) {
                        errorMsg += error.messages[field].join('\n')
                        + '\n';
                    }
                    alert(errorMsg);
                } else {
                    alert('Đã xảy ra lỗi khi tạo collection');
                }
            });
        });
    }
});

    </script>
@endsection