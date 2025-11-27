package com.reciclafacil.desktop.service;

import com.fasterxml.jackson.core.type.TypeReference;
import com.reciclafacil.desktop.model.PontoColeta;
import com.reciclafacil.desktop.net.ApiClient;
import com.reciclafacil.desktop.net.dto.ApiResponse;

import java.io.IOException;
import java.util.List;

public class PontoColetaService {
    
    private final ApiClient apiClient;
    private final String basePath = "/pontos";
    
    public PontoColetaService(ApiClient apiClient) {
        this.apiClient = apiClient;
    }
    
    public List<PontoColeta> listarTodos() throws IOException, InterruptedException {
        ApiResponse<List<PontoColeta>> response = apiClient.get(basePath,
                new TypeReference<>() {});
        validar(response);
        return response.getData();
    }
    
    public List<PontoColeta> listarProximos(double latitude, double longitude) throws IOException, InterruptedException {
        ApiResponse<List<PontoColeta>> response = apiClient.get(
                basePath + "/proximos?lat=" + latitude + "&lng=" + longitude,
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

