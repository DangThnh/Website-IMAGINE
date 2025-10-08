<!DOCTYPE html>
<html>
<head>
    @auth
    <style>
        .user-name {
            color: white; /* Màu chữ trắng */
            position: absolute; /* Đặt vị trí tuyệt đối */
            right: 30px; /* Cách cạnh phải 20px */
            top: 30px; /* Cách cạnh trên 20px */
            font-size: 18px; /* Kích thước chữ */
            z-index: 1000; /* Đảm bảo nó nằm trên các phần tử khác */
        }
    </style>
    <div class="user-name">{{ auth()->user()->name }}</div>
    @endauth
    <title>Upload Image</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/styleUploadPage.css') }}">
    <script>
        // Hàm xác nhận rời khỏi trang
        function confirmLeave() {
            return confirm("Bạn có chắc chắn muốn rời khỏi trang này? Tất cả dữ liệu chưa lưu sẽ bị mất.");
        }

        // Thêm sự kiện trước khi rời khỏi trang
        window.addEventListener('beforeupload', function(event) {
            event.returnValue = "Bạn có chắc chắn muốn rời khỏi trang này?"; // Cảnh báo khi rời trang
        });

        // Tắt cảnh báo khi nhấn nút Upload
        function disableUnloadWarning() {
            window.removeEventListener('beforeupload', function(event) {
                event.returnValue = "Bạn có chắc chắn muốn rời khỏi trang này?";
            });
        }
    </script>
</head>
<body>
    <div class="header">
        <a href="{{ url('images/gallery') }}" style="text-decoration: none; color: white;">
            <h1>IMAGINE</h1>
            <h2>Image engine, power your dream</h2>
        </a>
    </div>

    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <form action="{{ url('/images/upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="image">Select the file to upload:</label>
        <input type="file" name="image" id="image" required>

        <label for="filename">Artwork Name:</label>
        <input type="text" name="filename" id="filename" required>

        <label for="description">Artwork Description:</label>
        <textarea name="description" id="description"></textarea>

        <label for="category">Artwork Categories:</label>
        <input type ="text" name = "category" id="category" required>

        <button type="submit">Upload Image</button>
    </form>
    <a href="{{ url('/images/gallery') }}" onclick="return confirmLeave();">Back to Gallery</a>
</body>
</html>
