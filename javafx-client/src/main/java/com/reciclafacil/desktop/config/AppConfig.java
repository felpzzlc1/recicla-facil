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
        return require("api.baseUrl");
    }

    public String getDefaultToken() {
        return properties.getProperty("auth.token", "");
    }

    public String getLocale() {
        return properties.getProperty("ui.locale", "pt-BR");
    }

    private String require(String key) {
        String value = properties.getProperty(key);
        if (Objects.isNull(value) || value.isBlank()) {
            throw new IllegalStateException("Propriedade obrigatória não encontrada: " + key);
        }
        return value.trim();
    }
}

