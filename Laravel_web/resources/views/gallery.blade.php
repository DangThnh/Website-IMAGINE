<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('/assets/css/styleMainPage.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/searchBar.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/commentify.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/star.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/popup.css') }}">
    @auth
    <style>
        .user-name {
            color: white; /* Màu chữ trắng */
            position: absolute; /* Đặt vị trí tuyệt đối */
            right: 80px; /* Cách cạnh phải 20px */
            top: 30px; /* Cách cạnh trên 20px */
            font-size: 18px; /* Kích thước chữ */
            z-index: 1000; /* Đảm bảo nó nằm trên các phần tử khác */
        }
    </style>
    <div class="user-name">{{ auth()->user()->name }}</div>
    @endauth
    <title>IMAGINE</title>


</head>

<body>
    <div class="header">
        <a href="{{ url('images/gallery') }}" style="text-decoration: none; color: white;">
            <h1>IMAGINE</h1>
            <h2>Image engine, power your dream</h2>
        </a>
        @auth
        <a href="{{ url('/images/upload') }}" class="upload-button" style="top:60px; right: 80px; display: inline-block; background-color: #4CAF50; color: white; padding: 10px 20px; text-align: center; text-decoration: none; border-radius: 5px;">
            Upload
        </a>
        @endauth

        <div class="search-form">
            <form action="{{ route('images.search') }}" method="GET">
                <input type="text" name="query" placeholder="Search images by name" required>
            </form>
        </div>

        <div class="star-container">
            <!-- Các ngôi sao sẽ được thêm động bằng JavaScript -->
        </div>

    </div>

    <!-- Nút bấm hình tròn -->
    <button id="userButton" class="user-button">
        <img src="{{ asset('assets/pictures/userIcon.png') }}" alt="User" />
    </button>

    @auth
    <a href="{{ route('chat.index') }}" id="ChatButton" class="chat-button">
        <img src="{{ asset('assets/pictures/chatIcon.png') }}" alt="Chat" />
    </a>
    @endauth

    <!-- Modal cho người dùng -->
    <div class="modal2" id="userModal">
        <div class="modal-content2">
            <span class="close-button2" id="closeModal">&times;</span>
            @guest
                <a href="{{ url('/login') }}" class="modal-button2">Login</a>
                <a href="{{ url('/registration') }}" class="modal-button2">Sign Up</a>
            @else
                <a href="{{ url('/profile') }}" class="modal-button2">Profile</a>
                <a href="{{ url('/images/upload') }}" class="modal-button2">Upload</a>
                <a href="{{ url('/logout') }}" class="modal-button2">Log out</a>

            @endguest
        </div>
    </div>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <div class="box-container">
        @if ($images->isEmpty())
            <div class="no-results" style="text-align: center; padding: 20px; font-size: 18px; color: rgb(41, 41, 41);">
                No results was found for your search.
            </div>
        @else
            @foreach ($images as $image)
                <div class="image-container">
                    <div class="box" onclick="openModal('{{ $image->filename }}',
                    '{{ $image->description ?? 'No description available' }}',
                    '{{ auth()->check() && auth()->id() === $image->user_id ? route('images.edit',
                     $image->id) : '' }}', '{{ asset($image->path) }}',
                     '{{ route('images.showDetail', $image->id) }}', '{{ $image->id }}')">
                        <div class="overlay"></div>
                        <img src="{{ asset($image->path) }}" alt="{{ $image->filename }}">
                    </div>
                </div>
            @endforeach
        @endif


    </div>

    <!-- Modal khi mở box ảnh -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modal-title"></h2>
            <img id="modal-image" src="" alt="" style="max-width: 100%; height: auto; margin-bottom: 15px; cursor: pointer;" onclick="zoomImage(this)">
            <p id="modal-description"></p>
            <a id="edit-link" href="#" class="inside_button" style="display:none;">Edit</a>
            <a id="more-link" href="#" class="inside_button" style="margin-top: 10px;">More</a>
            {{--<a id="add-link" href="#" class="inside_button" style="margin-top: 20px;" >Add to Collection</a>--}}
        </div>
    </div>

        <!-- Modal chọn collection -->
    {{--@include('partials.collection-modal')--}}

    <script>
        function openModal(title, description, editLink, imagePath, moreLink) {
        if (!imagePath) {
            console.error("Image path is required.");
            return;
        }

        document.getElementById("modal-image").src = imagePath;
        document.getElementById("modal-title").innerHTML = title;

        const maxLength = 200;
        document.getElementById("modal-description").innerHTML = description.length > maxLength
            ? description.substring(0, maxLength) + '...'
            : description;

        const editButton = document.getElementById("edit-link");
        if (editLink) {
            editButton.href = editLink;
            editButton.style.display = 'inline'; // Hiện nút chỉnh sửa
        } else {
            editButton.style.display = 'none'; // Ẩn nút chỉnh sửa
        }

        const moreButton = document.getElementById("more-link");
        moreButton.href = moreLink; // Cập nhật đường dẫn cho nút More

        document.getElementById("myModal").style.display = "block";
    }

        function zoomImage(img) {
            if (img.classList.contains('img-zoom')) {
                img.classList.remove('img-zoom'); // Bỏ zoom
            } else {
                img.classList.add('img-zoom'); // Thêm zoom
            }
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        // Đóng modal khi nhấn ngoài modal
        window.onclick = function(event) {
            if (event.target == document.getElementById("myModal")) {
                closeModal();
            }
        }
    </script>

    <script>
        function createStar() {
            const star = document.createElement('div');
            star.classList.add('star');
            document.querySelector('.header').appendChild(star);

            // Vị trí bắt đầu ngẫu nhiên (trên header)
            const startX = Math.random() * 90; // Ngẫu nhiên từ 0 đến 100% chiều rộng header
            const startY = 20; // Bắt đầu từ trên header
            star.style.left = `${startX}%`;
            star.style.top = `${startY}%`;

            // Thời gian delay ngẫu nhiên
            const delay = Math.random() * 3;
            star.style.animationDelay = `${delay}s`;

            star.addEventListener('animationiteration', () => {
                star.remove();
            });
        }

        function startStarAnimation() {
            for (let i = 0; i < 4; i++) {
                setTimeout(createStar, Math.random() * 300);
            }
            setTimeout(startStarAnimation, 12000);
        }

        startStarAnimation();
    </script>

    <script>
        // Lấy các phần tử
        const userButton = document.getElementById('userButton');
        const userModal = document.getElementById('userModal');
        const closeModal2 = document.getElementById('closeModal');

        // Mở modal khi bấm vào nút
        userButton.addEventListener('click', () => {
            userModal.style.display = 'block';
        });

        // Đóng modal khi bấm vào nút đóng
        closeModal2.addEventListener('click', () => {
            userModal.style.display = 'none';
        });

        // Đóng modal khi bấm ra ngoài modal
        window.addEventListener('click', (event) => {
            if (event.target === userModal) {
                userModal.style.display = 'none';
            }
        });
    </script>

 {{--script cho nút add to collection bên ngoàingoài
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addLink = document.getElementById('add-link');
            const collectionModal = document.getElementById('collectionModal');
            const closeCollectionModal = document.getElementById('closeCollectionModal');
            const saveButtons = document.querySelectorAll('.save-to-collection');
            const createForm = document.getElementById('create-collection-form');

            if (!addLink) {
        console.error('Element #add-link not found in DOM');
        return;
    }

            // Mở #collectionModal khi nhấn Add to Collection
            addLink.addEventListener('click', function(e) {
                e.preventDefault();
                const imageId = this.getAttribute('data-image-id');
                if (!imageId) {
                    console.error('Image ID is missing');
                    return;
                }
                saveButtons.forEach(btn => btn.setAttribute('data-image', imageId));
                collectionModal.style.display = 'block';
            });

            // Đóng #collectionModal
            if (closeCollectionModal) {
                closeCollectionModal.addEventListener('click', () => {
                    collectionModal.style.display = 'none';
                });
            }

            window.addEventListener('click', (event) => {
                if (event.target === collectionModal) {
                    collectionModal.style.display = 'none';
                }
            });

            // Lưu vào collection hiện có
            saveButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const collectionId = this.getAttribute('data-collection');
                    const imageId = this.getAttribute('data-image');

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
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Đã lưu ảnh vào bộ sưu tập!');
                            collectionModal.style.display = 'none';
                        } else {
                            alert('Lỗi: ' + (data.error || 'Không thể lưu ảnh'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Đã xảy ra lỗi khi lưu ảnh');
                    });
                });
            });

            // Tạo collection mới
            if (createForm) {
                createForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const imageId = addLink.getAttribute('data-image-id');
                    formData.append('image_id', imageId);

                    if (!formData.has('is_public')) {
                        formData.append('is_public', '0');
                    }

                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }

                    fetch('/collections', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw errorData;
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        alert(data.success);
                        collectionModal.style.display = 'none';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (error.messages) {
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
    </script>--}}
</body>
</html>
