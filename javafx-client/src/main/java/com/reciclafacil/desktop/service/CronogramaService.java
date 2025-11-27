package com.reciclafacil.desktop.service;

import com.fasterxml.jackson.core.type.TypeReference;
import com.reciclafacil.desktop.model.CronogramaColeta;
import com.reciclafacil.desktop.net.ApiClient;
import com.reciclafacil.desktop.net.dto.ApiResponse;

import java.io.IOException;
import java.util.List;

public class CronogramaService {
    
    private final ApiClient apiClient;
    private final String basePath = "/cronograma";
    
    public CronogramaService(ApiClient apiClient) {
        this.apiClient = apiClient;
    }
    
    public List<CronogramaColeta> listarTodos() throws IOException, InterruptedException {
        ApiResponse<List<CronogramaColeta>> response = apiClient.get(basePath,
                new TypeReference<>() {});
        validar(response);
        return response.getData();
    }
    
    public List<CronogramaColeta> listarProximos() throws IOException, InterruptedException {
        ApiResponse<List<CronogramaColeta>> response = apiClient.get(basePath + "/proximos",
                new TypeReference<>() {});
        validar(response);
        return response.getData();
    }
    
    public List<CronogramaColeta> listarPorMaterial(String material) throws IOException, InterruptedException {
        ApiResponse<List<CronogramaColeta>> response = apiClient.get(
                basePath + "/material/" + material,
                new TypeReference<>() {});
        validar(response);
        return response.getData();
    }
    
    public List<CronogramaColeta> listarPorDiaSemana(String diaSemana) throws IOException, InterruptedException {
        ApiResponse<List<CronogramaColeta>> response = apiClient.get(
                basePath + "/dia/" + diaSemana,
                new TypeReference<>() {});
        validar(response);
        return response.getData();
    }
    
    public List<CronogramaColeta> listarPorCidade(String cidade) throws IOException, InterruptedException {
        ApiResponse<List<CronogramaColeta>> response = apiClient.get(
                basePath + "/cidade/" + cidade,
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

