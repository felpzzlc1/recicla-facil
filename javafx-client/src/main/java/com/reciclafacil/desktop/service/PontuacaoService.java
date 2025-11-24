package com.reciclafacil.desktop.service;

import com.fasterxml.jackson.core.type.TypeReference;
import com.reciclafacil.desktop.model.*;
import com.reciclafacil.desktop.net.ApiClient;
import com.reciclafacil.desktop.net.dto.ApiResponse;

import java.io.IOException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * Replica o contrato do serviço AngularJS para o cliente JavaFX.
 */
public class PontuacaoService {

    private final ApiClient apiClient;
    private final String basePath = "/pontuacao";

    public PontuacaoService(ApiClient apiClient) {
        this.apiClient = apiClient;
    }

    public PontuacaoResumo obterResumo() throws IOException, InterruptedException {
        ApiResponse<PontuacaoResumo> response = apiClient.get(basePath + "/estatisticas",
                new TypeReference<>() {});
        validar(response);
        return response.getData();
    }

    public List<Conquista> listarConquistas() throws IOException, InterruptedException {
        ApiResponse<List<Conquista>> response = apiClient.get(basePath + "/conquistas",
                new TypeReference<>() {});
        validar(response);
        return response.getData();
    }

    public List<RankingEntry> listarRanking(int limite) throws IOException, InterruptedException {
        ApiResponse<List<RankingEntry>> response = apiClient.get(basePath + "/ranking?limite=" + limite,
                new TypeReference<>() {});
        validar(response);
        return response.getData();
    }

    public EstatisticasGerais obterEstatisticasGerais() throws IOException, InterruptedException {
        ApiResponse<EstatisticasGerais> response = apiClient.get(basePath + "/estatisticas-gerais",
                new TypeReference<>() {});
        validar(response);
        return response.getData();
    }

    public RegistrarDescarteResponse registrarDescarte(String material, double peso) throws IOException, InterruptedException {
        Map<String, Object> payload = new HashMap<>();
        payload.put("material", material);
        payload.put("peso", peso);

        ApiResponse<RegistrarDescarteResponse> response = apiClient.post(basePath + "/registrar-descarte",
                payload, new TypeReference<>() {});
        validar(response);
        return response.getData();
    }

    public PontuacaoDashboard carregarPainelCompleto(int limiteRanking) throws IOException, InterruptedException {
        PontuacaoDashboard dashboard = new PontuacaoDashboard();
        dashboard.setResumo(obterResumo());
        dashboard.setConquistas(listarConquistas());
        dashboard.setRanking(listarRanking(limiteRanking));
        dashboard.setEstatisticasGerais(obterEstatisticasGerais());
        return dashboard;
    }

    private void validar(ApiResponse<?> response) {
        if (response == null || !response.isSuccess()) {
            String message = response != null ? response.getMessage() : "Resposta vazia da API";
            throw new IllegalStateException(message);
        }
    }
}

