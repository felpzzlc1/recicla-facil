package com.reciclafacil.desktop.security;

import java.util.Optional;
import java.util.concurrent.atomic.AtomicReference;

/**
 * Armazena token e dados básicos do usuário logado para reaproveitar nos requests.
 */
public class AuthSession {

    private final AtomicReference<String> tokenRef = new AtomicReference<>();

    public AuthSession(String initialToken) {
        if (initialToken != null && !initialToken.isBlank()) {
            tokenRef.set(initialToken.trim());
        }
    }

    public Optional<String> getToken() {
        return Optional.ofNullable(tokenRef.get());
    }

    public void updateToken(String token) {
        if (token == null || token.isBlank()) {
            tokenRef.set(null);
        } else {
            tokenRef.set(token.trim());
        }
    }
}

