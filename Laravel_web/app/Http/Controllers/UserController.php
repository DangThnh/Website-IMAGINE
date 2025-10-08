<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\User;

class UserController extends Controller
{
    public function showProfileIfAuth()
    {
        $user = Auth::user();
        $images = $user->images; // Giả sử bạn đã thiết lập mối quan hệ giữa User và Image

        return view('profile', compact('user', 'images'));
    }

    public function showProfile($imageId)
    {
        // Lấy bức ảnh dựa trên ID
        $image = Image::with('user')->findOrFail($imageId);

        // Lấy thông tin của tác giả từ bức ảnh
        $user = $image->user;

        // Lấy tất cả các bức ảnh của tác giả
        $images = $user->images;

        return view('profile', compact('user', 'images'));
    }


    public function showUserProfile($userId)
    {
        $user = User::findOrFail($userId); // Tìm người dùng dựa trên userId
        $images = $user->images; // Lấy tất cả ảnh của người dùng
        return view('profile', compact('user', 'images')); // Trả về view 'profile'
    }
}
