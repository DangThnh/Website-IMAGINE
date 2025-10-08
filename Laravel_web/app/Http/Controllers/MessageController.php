<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\ChatMessage;

class MessageController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'room_id' => 'required|exists:chat_rooms,id', // Đảm bảo room_id được gửi và hợp lệ
        ]);
    
        $image = $request->file('image');
        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('assets/pictures/messageImage');
    
        if (!\File::exists($destinationPath)) {
            \File::makeDirectory($destinationPath, 0755, true);
        }
    
        $image->move($destinationPath, $imageName);
        $imageUrl = asset("assets/pictures/messageImage/$imageName");
    
        $message = ChatMessage::create([
            'room_id' => $request->room_id, // Đảm bảo room_id luôn có giá trị
            'sender_id' => auth()->id(),
            'content' => $imageUrl,
        ]);
    
        return response()->json($message);
    }
    


    
}
