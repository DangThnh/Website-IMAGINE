package com.websocket.chat.services;

import com.websocket.chat.model.User;
import com.websocket.chat.repository.UserRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.util.Optional;

@Service
public class UserService {

    @Autowired
    private UserRepository userRepository; // Gọi repository để lấy dữ liệu từ DB

    public User getUserById(Long userId) {
        return userRepository.findById(userId).orElse(null);
    }
}

