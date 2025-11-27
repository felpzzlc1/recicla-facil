package com.reciclafacil.desktop.model;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import com.fasterxml.jackson.annotation.JsonProperty;

@JsonIgnoreProperties(ignoreUnknown = true)
public class PontuacaoResumo {

    private int nivel;
    @JsonProperty("nivel_nome")
    private String nivelNome;
    @JsonProperty("pontos_para_proximo_nivel")
    private int pontosParaProximoNivel;
    @JsonProperty("progresso_nivel")
    private double progressoNivel;
    @JsonProperty("pontos_semana_atual")
    private int pontosSemanaAtual;
    @JsonProperty("pontos")
    private int pontuacaoTotal;
    private int descartes;
    @JsonProperty("sequencia_dias")
    private int sequenciaDias;
    @JsonProperty("badges_conquistadas")
    private int badgesConquistadas;

    public int getNivel() {
        return nivel;
    }

    public void setNivel(int nivel) {
        this.nivel = nivel;
    }

    public String getNivelNome() {
        return nivelNome;
    }

    public void setNivelNome(String nivelNome) {
        this.nivelNome = nivelNome;
    }

    public int getPontosParaProximoNivel() {
        return pontosParaProximoNivel;
    }

    public void setPontosParaProximoNivel(int pontosParaProximoNivel) {
        this.pontosParaProximoNivel = pontosParaProximoNivel;
    }

    public double getProgressoNivel() {
        return progressoNivel;
    }

    public void setProgressoNivel(double progressoNivel) {
        this.progressoNivel = progressoNivel;
    }

    public int getPontosSemanaAtual() {
        return pontosSemanaAtual;
    }

    public void setPontosSemanaAtual(int pontosSemanaAtual) {
        this.pontosSemanaAtual = pontosSemanaAtual;
    }

    public int getPontuacaoTotal() {
        return pontuacaoTotal;
    }

    public void setPontuacaoTotal(int pontuacaoTotal) {
        this.pontuacaoTotal = pontuacaoTotal;
    }

    public int getDescartes() {
        return descartes;
    }

    public void setDescartes(int descartes) {
        this.descartes = descartes;
    }

    public int getSequenciaDias() {
        return sequenciaDias;
    }

    public void setSequenciaDias(int sequenciaDias) {
        this.sequenciaDias = sequenciaDias;
    }

    public int getBadgesConquistadas() {
        return badgesConquistadas;
    }

    public void setBadgesConquistadas(int badgesConquistadas) {
        this.badgesConquistadas = badgesConquistadas;
    }

    public int getPontosRestantes() {
        return Math.max(0, pontosParaProximoNivel);
    }
}

