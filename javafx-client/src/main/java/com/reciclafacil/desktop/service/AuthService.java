package com.reciclafacil.desktop.service;

import com.fasterxml.jackson.core.type.TypeReference;
import com.reciclafacil.desktop.net.ApiClient;
import com.reciclafacil.desktop.net.dto.ApiResponse;
import com.reciclafacil.desktop.security.AuthSession;

import java.io.IOException;
import java.util.HashMap;
import java.util.Map;

public class AuthService {
    
    private final ApiClient apiClient;
    private final AuthSession authSession;
    
    public AuthService(ApiClient apiClient, AuthSession authSession) {
        this.apiClient = apiClient;
        this.authSession = authSession;
    }
    
    public boolean login(String email, String senha) throws IOException, InterruptedException {
        Map<String, String> payload = new HashMap<>();
        payload.put("email", email);
        payload.put("senha", senha);
        
        ApiResponse<Map<String, Object>> response = apiClient.post("/auth/login", 
                payload, new TypeReference<>() {});
        
        if (response != null && response.isSuccess() && response.getData() != null) {
            Map<String, Object> userData = response.getData();
            String token = (String) userData.get("token");
            if (token != null && !token.isEmpty()) {
                authSession.updateToken(token);
                return true;
            }
        }
        return false;
    }
    
    public boolean loginDemo() throws IOException, InterruptedException {
        return login("demo@recicla.com", "123456");
    }
}

