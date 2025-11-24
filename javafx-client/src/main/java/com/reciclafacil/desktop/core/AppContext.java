package com.reciclafacil.desktop.core;

import com.reciclafacil.desktop.config.AppConfig;
import com.reciclafacil.desktop.net.ApiClient;
import com.reciclafacil.desktop.security.AuthSession;
import com.reciclafacil.desktop.service.PontuacaoService;

/**
 * Container simples para centralizar instâncias compartilhadas no cliente JavaFX.
 */
public final class AppContext {

    private static final AppContext INSTANCE = new AppContext();

    private final AppConfig config;
    private final AuthSession authSession;
    private final ApiClient apiClient;
    private final PontuacaoService pontuacaoService;

    private AppContext() {
        this.config = new AppConfig();
        this.authSession = new AuthSession(config.getDefaultToken());
        this.apiClient = new ApiClient(config.getApiBaseUrl(), authSession);
        this.pontuacaoService = new PontuacaoService(apiClient);
    }

    public static AppContext get() {
        return INSTANCE;
    }

    public AppConfig getConfig() {
        return config;
    }

    public AuthSession getAuthSession() {
        return authSession;
    }

    public PontuacaoService getPontuacaoService() {
        return pontuacaoService;
    }
}

