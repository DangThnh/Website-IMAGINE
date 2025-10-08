<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat Room</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Load SockJS, STOMP, jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sockjs-client/1.6.1/sockjs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/stomp.js/2.3.3/stomp.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.1/dist/index.css">
    <script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.1/dist/index.js"></script>
    <link rel="stylesheet" href="{{ asset('/assets/css/chatBox.css') }}">

</head>
<body>
    <!-- Modal hiển thị ảnh khi click -->
    <div id="imageModal">
        <span id="closeModal">×</span>
        <img id="modalImage" src="" alt="Full Image">
    </div>

    <div id="chatRooms">
        <div>
            <button id="backButton" onclick="goBack()">
                <i class="fas fa-arrow-left"></i>
            </button>
            {{-- <i class="fa-solid fa-comments"></i> --}}
            <input type="text" id="artistSearchInput" placeholder="Search for artist...">
            <button class="search-button" onclick="searchChatRoomsByArtistName()">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
        <ul id="roomsList"></ul>
    </div>

    <div id="chatArea">
        <h3 id="chatTitle">Select a room</h3>
        <div id="messages"></div>
        <div id="inputArea">
            <input type="file" id="imageInput" accept="image/*" style="display:none;">
            <button id="sendImage"><i class="fas fa-image"></i></button>

            <input type="text" id="messageInput" placeholder="Type a message...">
            <button id="sendMessage">Send</button>
        </div>
    </div>

    <script>
        let stompClient = null;
        let currentRoomId = null;
        let userId = {{ Auth::id() }};
        let subscription = null; // Biến lưu subscription hiện tại

        function connectWebSocket() {
            let socket = new SockJS('http://localhost:8080/ws');
            stompClient = Stomp.over(socket);
            stompClient.connect({}, function (frame) {
                console.log("Connected to WebSocket");
            });
        }


        function subscribeToRoom(roomId) {
            if (stompClient) {
                // Hủy bỏ subscription cũ trước khi tạo cái mới
                if (subscription !== null) {
                    subscription.unsubscribe();
                }

                subscription = stompClient.subscribe(`/topic/chat/${roomId}`, function (message) {
                    let msg = JSON.parse(message.body);
                    if (msg.roomId === currentRoomId) {
                        showMessage(msg);
                    }
                });
            }
        }


        function loadChatRooms() {
            $.get('/chat-rooms', function(rooms) {
                updateRoomList(rooms);
            });
        }

        function updateRoomList(rooms) {
            $('#roomsList').empty();
            rooms.forEach(room => {
                // let userName = room.user_id === userId ? room.artist.name : room.user.name;
                console.log(room);
                let userName = room.user_id === userId ? room.artistName : room.userName;
                // $('#roomsList').append(`<li onclick="loadMessages(${room.id}, '${userName}')">${userName}</li>`);
                $('#roomsList').append(`
                    <li class="room" data-room-id="${room.id}" onclick="loadMessages(${room.id}, '${userName}')">
                        <img src="${room.artistAvatar}" alt="Avatar" style="width:40px; height:40px; border-radius:50%; margin-right:15px;">
                        <span>${userName}</span>
                    </li>
                `);
            });
        }

        function loadMessages(roomId, name) {
            currentRoomId = roomId;
            $('#chatTitle').text(name);

            // Xóa danh sách tin nhắn cũ trước khi tải mới
            $('#messages').empty();

            // Hủy subscription cũ trước khi tạo mới
            if (subscription !== null) {
                subscription.unsubscribe();
            }

            // Lấy lịch sử tin nhắn trước khi subscribe để đảm bảo hiển thị đúng
            $.get(`/messages/${roomId}`, function(messages) {
                messages.forEach(msg => {
                    showMessage(msg);
                });

                // Sau khi tải xong tin nhắn, mới subscribe để nhận tin nhắn real-time
                subscribeToRoom(roomId);
            });

            // Xóa class active-room khỏi tất cả các phòng
            $('.room').removeClass('active-room');

            // Thêm class active-room vào phòng đang chọn
            $(`.room[data-room-id="${roomId}"]`).addClass('active-room');
        }


        function sendMessage() {
            let messageContent = $('#messageInput').val().trim();

            if (messageContent !== '' && currentRoomId) {
                let chatMessage = {
                    roomId: currentRoomId,
                    senderId: userId,
                    content: messageContent
                };

                stompClient.send("/app/chat", {}, JSON.stringify(chatMessage));

                $.ajax({
                    url: '/messages',
                    type: 'POST',
                    data: { roomId: currentRoomId, content: messageContent },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        console.log("Message saved:", response);
                    },
                    error: function (error) {
                        console.error("Error saving message:", error);
                    }
                });

                $('#messageInput').val('');
            }
        }

        function showMessage(message) {
            let isMine = message.senderId === userId || message.sender_id === userId;
            let sender = message.senderName || "Unknown";
            let className = isMine ? "sent" : "received";

            // Kiểm tra nếu nội dung tin nhắn là URL ảnh (có thể mở rộng điều kiện kiểm tra)
            let isImage = message.content.match(/\.(jpeg|jpg|gif|png)$/) !== null;

            let content = isImage
                ? ` <div class="message sent">
                        <img src="${message.content}" class="chat-image" onclick="openImageModal('${message.content}')">
                    </div>`
                : `<span class="message-content">${message.content}</span>`;

            let messageElement = `
                <div class="message ${className}">
                    ${isMine ? '' : `<b>${sender}:</b>`} ${content}
                </div>
            `;

            $('#messages').append(messageElement);
            $('#messages').scrollTop($('#messages')[0].scrollHeight);
        }





        function goBack() {
            window.location.href = "/images/gallery";
        }

        function searchChatRoomsByArtistName() {
            const artistName = $('#artistSearchInput').val().trim(); // Get artist name from input

            if (artistName !== '') {
                $.get(`/chat-rooms/search-by-artist-name?artist_name=${artistName}`, function(rooms) {
                    updateRoomList(rooms); // Update room list with search results
                });
            } else {
                loadChatRooms(); // If input is empty, reload all chat rooms
            }
        }


        $('#sendImage').click(() => {
        $('#imageInput').click(); // Mở hộp thoại chọn ảnh
    });

        $('#imageInput').change(function (event) {
        let file = event.target.files[0]; // Lấy file người dùng chọn

        if (file) {
            uploadImage(file); // Gọi hàm upload ảnh
        }
    });

        function uploadImage(file) {
        if (!currentRoomId) {
            alert("Please select a chat room first!");
            return;
        }

        let formData = new FormData();
        formData.append('image', file);
        formData.append('room_id', currentRoomId); // Đảm bảo gửi roomId

        $.ajax({
            url: '/messages/image-upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                console.log("Image sent:", response);

                let messageElement = `
                    <div class="message sent">
                        <img src="${response.content}" class="chat-image" onclick="openImageModal('${response.content}')">
                    </div>
                `;
                $('#messages').append(messageElement);
                $('#messages').scrollTop($('#messages')[0].scrollHeight);
            },
            error: function(error) {
                console.error("Error uploading image:", error);
            }
        });
    }

    // Hàm mở modal ảnh
        function openImageModal(imageUrl) {
            $('#modalImage').attr('src', imageUrl);
            $('#imageModal').css('display', 'flex');
        }

        // Đóng modal khi bấm nút X
        $('#closeModal').click(function () {
            $('#imageModal').css('display', 'none');
        });

        // Đóng modal khi bấm ra ngoài ảnh
        $('#imageModal').click(function (event) {
            if (event.target === this) {
                $(this).css('display', 'none');
            }
        });




        $('#messageInput').keypress(function (e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        $('#sendMessage').click(function (e) {
                sendMessage();

        });

        $(document).ready(function () {
            connectWebSocket();
            loadChatRooms();

            let roomId = "{{ request()->roomId ?? '' }}"; // Lấy roomId từ URL
            let artistName = "{{ request()->artistName ?? '' }}"; // Lấy tên artist từ URL

            if (roomId) {
                setTimeout(() => {
                    loadMessages(roomId, artistName);
                    $(`.room[data-room-id="${roomId}"]`).addClass('active-room'); // Đảm bảo phòng chat được bôi đậm
                }, 500); // Chờ một chút để danh sách phòng chat được tải xong
            }
        });




    </script>
</body>
</html>
