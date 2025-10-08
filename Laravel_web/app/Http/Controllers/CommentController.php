<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function show()
    {
        //$image = Image::findOrFail($imageId); // Lấy hình ảnh theo ID
        //comments = Comment::where('image_id', $imageId)->get(); // Lấy comment liên quan đến hình ảnh

        // $post = Post::all();
        // return view('new', compact('post'));
    }

    // public function getComments($imageId)
    // {
    // $comments = Comment::where('image_id', $imageId)->get();
    // return view('livewire.comments', compact('comments'));
    // }

}
