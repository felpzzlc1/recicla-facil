package com.reciclafacil.desktop.core;

import com.reciclafacil.desktop.config.AppConfig;
import com.reciclafacil.desktop.net.ApiClient;
import com.reciclafacil.desktop.security.AuthSession;
import com.reciclafacil.desktop.service.AuthService;
import com.reciclafacil.desktop.service.CronogramaService;
import com.reciclafacil.desktop.service.PontoColetaService;
import com.reciclafacil.desktop.service.PontuacaoService;
import com.reciclafacil.desktop.service.RecompensaService;

/**
 * Container simples para centralizar instâncias compartilhadas no cliente JavaFX.
 */
public final class AppContext {

    private static final AppContext INSTANCE = new AppContext();

    private final AppConfig config;
    private final AuthSession authSession;
    private final ApiClient apiClient;
    private final AuthService authService;
    private final PontuacaoService pontuacaoService;
    private final PontoColetaService pontoColetaService;
    private final CronogramaService cronogramaService;
    private final RecompensaService recompensaService;

    private AppContext() {
        this.config = new AppConfig();
        this.authSession = new AuthSession(config.getDefaultToken());
        this.apiClient = new ApiClient(config.getApiBaseUrl(), authSession);
        this.authService = new AuthService(apiClient, authSession);
        this.pontuacaoService = new PontuacaoService(apiClient);
        this.pontoColetaService = new PontoColetaService(apiClient);
        this.cronogramaService = new CronogramaService(apiClient);
        this.recompensaService = new RecompensaService(apiClient);
        
        // Fazer login automático com usuário demo
        try {
            authService.loginDemo();
        } catch (Exception e) {
            System.err.println("Aviso: Não foi possível fazer login automático: " + e.getMessage());
            // Continua mesmo sem login - o usuário pode fazer login manualmente depois
        }
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
    
    public AuthService getAuthService() {
        return authService;
    }
    
    public PontoColetaService getPontoColetaService() {
        return pontoColetaService;
    }
    
    public CronogramaService getCronogramaService() {
        return cronogramaService;
    }
    
    public RecompensaService getRecompensaService() {
        return recompensaService;
    }
}

