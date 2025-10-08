<!DOCTYPE html>
<html>

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
<head>
    <title>Edit Image</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/styleEditPage.css') }}">
    <script>
        function confirmLeave() {
            return confirm("Bạn có chắc muốn quay lại chứ? Mọi thay đổi sẽ biến mất.");
        }
    </script>
</head>
<body>
    <div class="header">
        <a href="{{ url('images/gallery') }}" onclick="return confirmLeave();" style="text-decoration: none; color: white;">
            <h1>IMAGINE</h1>
            <h2>Image engine, power your dream</h2>
        </a>
    </div>

    <h1>Edit Image</h1>

    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <form action="{{ route('images.update', $image->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST') <!-- Sử dụng phương thức POST cho cập nhật -->

        <label for="image">Select a new file to upload (optional):</label>
        <input type="file" name="image" id="image">
        <br>

        <label for="filename">Artwork Name:</label>
        <input type="text" name="filename" id="filename" value="{{ $image->filename }}" required>
        <br>

        <label for="description">Artwork Description:</label>
        <textarea name="description" id="description">{{ $image->description }}</textarea>
        <br>

        <label for="category">Artwork Categories:</label>
        <input type="text" name="category" id="category" value="{{ $image->category }}" required>

        <button type="submit">Update Image</button>
    </form>

    <form action="{{ route('images.delete', $image->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');">
        @csrf
        @method('DELETE') <!-- Sử dụng phương thức DELETE cho xóa -->
        <button type="submit">Delete Image</button>
    </form>

    <br>
    <a href="{{ url('/images/gallery') }}" onclick="return confirmLeave();">Back to Gallery</a> <!-- Nút quay lại với xác nhận -->
</body>
</html>
