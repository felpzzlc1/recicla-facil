package com.reciclafacil.desktop.service;

import com.fasterxml.jackson.core.type.TypeReference;
import com.reciclafacil.desktop.model.Recompensa;
import com.reciclafacil.desktop.net.ApiClient;
import com.reciclafacil.desktop.net.dto.ApiResponse;

import java.io.IOException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class RecompensaService {
    
    private final ApiClient apiClient;
    private final String basePath = "/recompensas";
    
    public RecompensaService(ApiClient apiClient) {
        this.apiClient = apiClient;
    }
    
    public List<Recompensa> listarTodas() throws IOException, InterruptedException {
        ApiResponse<List<Recompensa>> response = apiClient.get(basePath,
                new TypeReference<>() {});
        validar(response);
        return response.getData();
    }
    
    public Recompensa obterPorId(int id) throws IOException, InterruptedException {
        ApiResponse<Recompensa> response = apiClient.get(basePath + "/" + id,
                new TypeReference<>() {});
        validar(response);
        return response.getData();
    }
    
    public Map<String, Object> resgatar(int recompensaId) throws IOException, InterruptedException {
        Map<String, Object> payload = new HashMap<>();
        payload.put("recompensa_id", recompensaId);
        
        ApiResponse<Map<String, Object>> response = apiClient.post(basePath + "/resgatar",
                payload, new TypeReference<>() {});
        validar(response);
        return response.getData();
    }
    
    public List<Map<String, Object>> meusResgates() throws IOException, InterruptedException {
        ApiResponse<List<Map<String, Object>>> response = apiClient.get(basePath + "/meus-resgates",
                new TypeReference<>() {});
        validar(response);
        return response.getData();
    }
    
    private void validar(ApiResponse<?> response) {
        if (response == null || !response.isSuccess()) {
            String message = response != null ? response.getMessage() : "Resposta vazia da API";
            throw new IllegalStateException(message);
        }
    }
}

