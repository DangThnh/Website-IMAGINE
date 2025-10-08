package com.websocket.chat.controller;

import com.websocket.chat.model.ChatMessage;
import com.websocket.chat.model.User;
import com.websocket.chat.services.UserService;
import lombok.RequiredArgsConstructor;
import org.springframework.messaging.handler.annotation.MessageMapping;
import org.springframework.messaging.handler.annotation.Payload;
import org.springframework.messaging.simp.SimpMessagingTemplate;
import org.springframework.stereotype.Controller;
import java.util.Date;

@RequiredArgsConstructor
@Controller
public class ChatController {
    private final SimpMessagingTemplate messagingTemplate;
    private final UserService userService;

    @MessageMapping("/chat")
    public void sendMessage(ChatMessage message) {
        // Lấy thông tin người gửi từ DB
        User sender = userService.getUserById(message.getSenderId());
        if (sender != null) {
            message.setSenderName(sender.getName()); // Gán tên người gửi vào tin nhắn
        } else {
            message.setSenderName("Unknown");
        }

        // Gửi tin nhắn đến topic
        messagingTemplate.convertAndSend("/topic/chat/" + message.getRoomId(), message);
    }
}
