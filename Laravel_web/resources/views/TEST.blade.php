@extends('layouts.layout')
<title>{{ $image->user->name }} - {{ $image->filename }}</title>
@section('content')

    <link rel="stylesheet" href="{{ asset('/assets/css/styleMainPage.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/searchBar.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/show.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/popup.css') }}">
    <title>Image Details</title>
    <style>
            body {
        background-color: rgb(255, 255, 255);
    }
    /* CSS cho modal */
    .modal2 {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        overflow: auto;
    }
    .modal-content2 {
    background-color: #fff;
    margin: 5% auto;
    padding: 25px;
    border-radius: 10px;
    width: 90%;
    max-width: 1000px;
    position: relative;
    display: flex;
    flex-direction: column; /* Change to column to stack header and body */
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
    .close-button2 {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 28px;
        cursor: pointer;
        color: #666;
        transition: color 0.3s;
    }
    .close-button2:hover {
        color: #000;
    }
    .collections-list {
        flex: 1; /* Chiếm một phần không gian linh hoạt */
        max-height: 500px;
        overflow-y: auto;
        padding-right: 15px;
        border-right: 1px solid #eee;
    }
    .collections-list h3 {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007BFF;
        color: #333;
        font-weight: 600;
    }
    .collection-item-container {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px;
        border-bottom: 1px solid #eee;
        transition: background-color 0.2s;
    }
    .collection-item-container:hover {
        background-color: #f9f9f9;
    }
    .collection-item-container img {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .collection-item-container .no-thumbnail {
        width: 90px;
        height: 90px;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #888;
        font-size: 0.9em;
    }
    .create-collection-container {
        flex: 1; /* Chiếm một phần không gian linh hoạt */
        padding-left: 15px;
    }
    .create-collection-container h4 {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #28a745;
        color: #333;
        font-weight: 600;
        text-align: center;
    }
    .modal-button2 {
        padding: 10px 15px;
        background-color: #007BFF;
        color: white;
        text-align: center;
        text-decoration: none;
        border-radius: 6px;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        font-weight: 500;
        display: inline-block;
    }
    .modal-button2:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    #create-collection-form input[type="text"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1em;
        transition: border 0.3s;
    }
    #create-collection-form input[type="text"]:focus {
        border-color: #007BFF;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
    }
    .checkbox-container {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    #is_public {
        margin-right: 8px;
        transform: scale(1.2);
    }
    /* Responsive: Xếp dọc trên màn hình nhỏ */
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
    .modal-title {
    text-align: center;
    font-size: 26px;
    font-weight: 600;
    color: #333;
    padding-bottom: 10px;
    border-bottom: 2px solid #4CAF50;
    margin-bottom: 5px;
    width: 60%;
}
    .modal-header {
    width: 100%;
    margin-bottom: 20px;
    display: flex;
    justify-content: center;
}
.modal-body {
    display: flex;
    flex-direction: row; /* This keeps your two columns side by side */
    gap: 30px;
    width: 100%;
}
.modal-title {
        width: 80%;
        font-size: 22px;
    }
    </style>

    <div class="container">
        <div class="inner-wrap" style="display: flex; align-items: flex-start;">
            <img src="{{ asset($image->path) }}" alt="{{ $image->filename }}" class="img-fluid"
                style="max-width: 75%; height: auto; margin-right: 20px;">
            <div class="mt-3" style="flex: 1;">
                <h2>{{ $image->filename }}</h2>
                <p><strong>Author:</strong> {{ $image->user->name }}</p>
                <p><strong>Description:</strong> {{ $image->description ?? 'No description available' }}</p>
                @if (auth()->check() && auth()->user()->id === $image->user_id)
                    <button id="add-link" class="inside_button" style="margin-top: 20px;">Add to Collection</button>
                    <a href="{{ route('images.edit', $image->id) }}" class="btn btn-warning">Edit Image</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Include modal partial -->
    @include('partials.collection-modal')

    <div style="text-align: center; margin-top: 20px;">
        <a href="javascript:history.back()" class="back-button"
            style="padding: 10px 15px; background-color: #007BFF;
            color: white; text-decoration: none; border-radius: 5px;">
            Return
        </a> <!-- Nút Quay lại -->
    </div>

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

        //console.log('Sending to /collections/add-image:', { collection_id: collectionId, image_id: imageId });

        fetch('/collections/add-image', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                collection_id: collectionId,
                image_id: imageId
            })
        })
        .then(response => {
            //console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            //console.log('Response data:', data);
            if (data.success) {
                alert('Đã lưu ảnh vào bộ sưu tập!');
                collectionModal.style.display = 'none';
            } else {
                alert('Lỗi: ' + (data.error || 'Không thể lưu ảnh') + (data.details ? ' - ' + data.details : ''));
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
                        errorMsg += error.messages[field].join('\n') + '\n';
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
