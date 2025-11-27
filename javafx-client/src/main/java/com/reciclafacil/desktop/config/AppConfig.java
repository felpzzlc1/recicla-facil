package com.reciclafacil.desktop.config;

import java.io.IOException;
import java.io.InputStream;
import java.util.Objects;
import java.util.Properties;

/**
 * Responsável por carregar propriedades externas (ex.: base URL da API, token temporário etc.).
 */
public final class AppConfig {

    private static final String DEFAULT_RESOURCE = "/app.properties";

    private final Properties properties = new Properties();

    public AppConfig() {
        load(DEFAULT_RESOURCE);
    }

    private void load(String resource) {
        try (InputStream inputStream = AppConfig.class.getResourceAsStream(resource)) {
            if (inputStream == null) {
                throw new IllegalStateException("Arquivo de configuração não encontrado: " + resource);
            }
            properties.load(inputStream);
        } catch (IOException e) {
            throw new IllegalStateException("Erro ao carregar configurações de " + resource, e);
        }
    }

    public String getApiBaseUrl() {
        return getOverride("API_BASE_URL", "api.baseUrl");
    }

    public String getDefaultToken() {
        String envValue = System.getenv("AUTH_TOKEN");
        if (envValue != null && !envValue.isBlank()) {
            return envValue.trim();
        }
        String sysProp = System.getProperty("AUTH_TOKEN");
        if (sysProp != null && !sysProp.isBlank()) {
            return sysProp.trim();
        }
        // Token é opcional - pode ser vazio para permitir login posterior
        String token = properties.getProperty("auth.token");
        return (token != null) ? token.trim() : "";
    }

    public String getLocale() {
        return properties.getProperty("ui.locale", "pt-BR");
    }

    private String getOverride(String envKey, String propertyKey) {
        String envValue = System.getenv(envKey);
        if (envValue != null && !envValue.isBlank()) {
            return envValue.trim();
        }
        String sysProp = System.getProperty(envKey);
        if (sysProp != null && !sysProp.isBlank()) {
            return sysProp.trim();
        }
        return require(propertyKey);
    }

    private String require(String key) {
        String value = properties.getProperty(key);
        if (Objects.isNull(value) || value.isBlank()) {
            throw new IllegalStateException("Propriedade obrigatória não encontrada: " + key);
        }
        return value.trim();
    }
}

