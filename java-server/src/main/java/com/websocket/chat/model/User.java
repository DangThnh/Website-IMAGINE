package com.websocket.chat.model;


import jakarta.persistence.*;
import lombok.*;

@Entity
@Table(name = "users") // Đảm bảo trùng với tên bảng trong database
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
public class User {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;  // Đây là trường khóa chính

    private String name;
    private String email;

    private String password;
    private String avatar;
    private String bio;

    @Column(name = "created_at", updatable = false)
    private java.time.LocalDateTime createdAt;

    @Column(name = "updated_at")
    private java.time.LocalDateTime updatedAt;
}

