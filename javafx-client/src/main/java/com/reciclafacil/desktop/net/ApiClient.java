package com.reciclafacil.desktop.net;

import com.fasterxml.jackson.core.type.TypeReference;
import com.fasterxml.jackson.databind.DeserializationFeature;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.reciclafacil.desktop.net.dto.ApiResponse;
import com.reciclafacil.desktop.security.AuthSession;

import java.io.IOException;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.nio.charset.StandardCharsets;
import java.time.Duration;
import java.util.Map;
import java.util.Objects;

/**
 * Cliente HTTP mínimo com serialização Jackson para dialogar com a API existente (Laravel).
 */
public class ApiClient {

    private final String baseUrl;
    private final AuthSession authSession;
    private final HttpClient httpClient;
    private final ObjectMapper objectMapper;

    public ApiClient(String baseUrl, AuthSession authSession) {
        this.baseUrl = baseUrl.endsWith("/") ? baseUrl.substring(0, baseUrl.length() - 1) : baseUrl;
        this.authSession = authSession;
        this.httpClient = HttpClient.newBuilder()
                .connectTimeout(Duration.ofSeconds(5))
                .build();
        this.objectMapper = new ObjectMapper()
                .configure(DeserializationFeature.FAIL_ON_UNKNOWN_PROPERTIES, false);
    }

    public <T> ApiResponse<T> get(String path, TypeReference<ApiResponse<T>> typeReference) throws IOException, InterruptedException {
        HttpRequest request = baseRequestBuilder(path).GET().build();
        HttpResponse<String> response = httpClient.send(request, HttpResponse.BodyHandlers.ofString(StandardCharsets.UTF_8));
        return parseResponse(response.body(), typeReference);
    }

    public <T> ApiResponse<T> post(String path, Object body, TypeReference<ApiResponse<T>> typeReference) throws IOException, InterruptedException {
        String payload = objectMapper.writeValueAsString(body);
        HttpRequest request = baseRequestBuilder(path)
                .header("Content-Type", "application/json")
                .POST(HttpRequest.BodyPublishers.ofString(payload, StandardCharsets.UTF_8))
                .build();
        HttpResponse<String> response = httpClient.send(request, HttpResponse.BodyHandlers.ofString(StandardCharsets.UTF_8));
        return parseResponse(response.body(), typeReference);
    }

    private HttpRequest.Builder baseRequestBuilder(String path) {
        String normalizedPath = path.startsWith("/") ? path : "/" + path;
        HttpRequest.Builder builder = HttpRequest.newBuilder()
                .uri(URI.create(baseUrl + normalizedPath))
                .timeout(Duration.ofSeconds(10));

        authSession.getToken().ifPresent(token -> builder.header("Authorization", "Bearer " + token));
        return builder;
    }

    private <T> ApiResponse<T> parseResponse(String payload, TypeReference<ApiResponse<T>> typeReference) throws IOException {
        return objectMapper.readValue(payload, typeReference);
    }

    public ObjectMapper getMapper() {
        return objectMapper;
    }

    public Map<String, Object> toMap(Object body) {
        return objectMapper.convertValue(body, new TypeReference<Map<String, Object>>() {});
    }
}

