<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class ChatController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function getChatRooms()
    {
        $userId = Auth::id();
        $rooms = ChatRoom::where('user_id', $userId)
            ->orWhere('artist_id', $userId)
            ->with(['user', 'artist'])
            ->get();

        $formattedRooms = $rooms->map(function ($room) {
            return [
                'id' => $room->id,
                'room_name' => $room->room_name,
                'created_at' => $room->created_at,

                // Thông tin của User
                'user_id' => $room->user_id,
                'userName' => $room->user->name ?? 'Unknown', // Tên user
                'userAvatar' => $room->user->avatar ?? 'default-avatar.png', // Avatar user

                // Thông tin của Artist
                'artist_id' => $room->artist_id,
                'artistName' => $room->artist->name ?? 'Unknown',
                'artistAvatar' => $room->artist->avatar ?? 'default-avatar.png',
            ];
        });

        return response()->json($formattedRooms);
    }



    // public function getChatRooms()
    // {
    //     $userId = Auth::id();
    //     $rooms = ChatRoom::where('user_id', $userId)
    //                      ->orWhere('artist_id', $userId)
    //                      ->with(['user', 'artist'])
    //                      ->get();

    //     return response()->json($rooms);
    // }

    public function getMessages($roomId)
    {
        $messages = ChatMessage::where('room_id', $roomId)
            ->with('sender') // Đảm bảo có quan hệ sender
            ->orderBy('created_at', 'asc')
            ->get();

        // Thêm trường senderName và senderAvatar vào JSON trả về
        $formattedMessages = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'room_id' => $msg->room_id,
                'content' => $msg->content,
                'created_at' => $msg->created_at,
                'senderId' => $msg->sender_id, // ID người gửi
                'senderName' => $msg->sender->name ?? 'Unknown', // Tên người gửi
                'senderAvatar' => $msg->sender->avatar ?? 'default-avatar.png', // Ảnh đại diện
            ];
        });

        return response()->json($formattedMessages);
    }



    public function sendMessage(Request $request)
    {
        $request->validate([
            'roomId' => 'required|exists:chat_rooms,id',
            'content' => 'required|string',
        ]);

        $message = new ChatMessage();
        $message->room_id = $request->roomId;
        $message->sender_id = Auth::id();
        $message->content = $request->content;
        $message->save();

        return response()->json(['status' => 'Message sent!', 'message' => $message]);
    }

    public function createChatRoom(Request $request)
    {
        $request->validate([
            'artist_id' => 'required|exists:users,id', // ID của Artist
        ]);
    
        $userId = Auth::id();
        $artistId = $request->artist_id;
    
        // Kiểm tra nếu phòng chat đã tồn tại
        $existingRoom = ChatRoom::where(function ($query) use ($userId, $artistId) {
            $query->where('user_id', $userId)->where('artist_id', $artistId);
        })
            ->orWhere(function ($query) use ($userId, $artistId) {
                $query->where('user_id', $artistId)->where('artist_id', $userId);
            })
            ->first();
    
        $artist = User::find($artistId);
    
        if ($existingRoom) {
            return redirect()->route('chat.index', [
                'roomId' => $existingRoom->id,
                'artist_id' => $artist->id,
                'artistName' => $artist->name
            ]);
        }
    
        // Tạo phòng chat mới
        $chatRoom = new ChatRoom();
        $chatRoom->user_id = $userId;
        $chatRoom->artist_id = $artistId;
        $chatRoom->save();
    
        return redirect()->route('chat.index', [
            'roomId' => $chatRoom->id,
            'artist_id' => $artist->id,
            'artistName' => $artist->name
        ]);
    }

    public function searchRoomsByArtistName(Request $request)
    {
        $request->validate([
            'artist_name' => 'required|string', // Validate artist_name là bắt buộc và là string
        ]);

        $artistName = $request->artist_name;
        $userId = Auth::id();

        // Tìm User (Artist) theo tên gần đúng (LIKE query)
        $artists = User::where('name', 'like', '%' . $artistName . '%')->get();

        if ($artists->isEmpty()) {
            // Nếu không tìm thấy artist nào, trả về danh sách phòng chat rỗng
            return response()->json([]);
        }

        $roomIds = [];
        foreach ($artists as $artist) {
            $artistId = $artist->id;
            $roomsForArtist = ChatRoom::where(function ($query) use ($userId, $artistId) {
                    $query->where('user_id', $userId)->where('artist_id', $artistId);
                })->orWhere(function ($query) use ($userId, $artistId) {
                    $query->where('artist_id', $userId)->where('user_id', $artistId);
                })
                ->pluck('id') // Lấy chỉ ID của phòng chat
                ->toArray();
            $roomIds = array_merge($roomIds, $roomsForArtist); // Gộp ID phòng chat lại
        }

        // Lấy danh sách phòng chat dựa trên roomIds đã thu thập
        $rooms = ChatRoom::whereIn('id', $roomIds)
            ->with(['user', 'artist'])
            ->get();


        $formattedRooms = $rooms->map(function ($room) {
            return [
                'id' => $room->id,
                'room_name' => $room->room_name,
                'created_at' => $room->created_at,
                'user_id' => $room->user_id,
                'userName' => $room->user->name ?? 'Unknown',
                'userAvatar' => $room->user->avatar ?? 'default-avatar.png',
                'artist_id' => $room->artist_id,
                'artistName' => $room->artist->name ?? 'Unknown',
                'artistAvatar' => $room->artist->avatar ?? 'default-avatar.png',
            ];
        });

        return response()->json($formattedRooms);
    }
    
}
