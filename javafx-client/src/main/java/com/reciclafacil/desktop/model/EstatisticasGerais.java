package com.reciclafacil.desktop.model;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;

@JsonIgnoreProperties(ignoreUnknown = true)
public class EstatisticasGerais {

    private int usuariosAtivos;
    private int descartesRegistrados;
    private int pontosDistribuidos;

    public int getUsuariosAtivos() {
        return usuariosAtivos;
    }

    public void setUsuariosAtivos(int usuariosAtivos) {
        this.usuariosAtivos = usuariosAtivos;
    }

    public int getDescartesRegistrados() {
        return descartesRegistrados;
    }

    public void setDescartesRegistrados(int descartesRegistrados) {
        this.descartesRegistrados = descartesRegistrados;
    }

    public int getPontosDistribuidos() {
        return pontosDistribuidos;
    }

    public void setPontosDistribuidos(int pontosDistribuidos) {
        this.pontosDistribuidos = pontosDistribuidos;
    }
}

