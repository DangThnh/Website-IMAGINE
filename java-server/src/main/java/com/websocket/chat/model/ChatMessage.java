package com.websocket.chat.model;

import java.util.Date;
import lombok.*;

@NoArgsConstructor
@AllArgsConstructor
@Data
@ToString
public class ChatMessage {
    private Long roomId;   // Thêm ID phòng chat
    private Long senderId; // Thêm ID người gửi
    private String senderName;
    private String content;
    private Date timestamp;
}
