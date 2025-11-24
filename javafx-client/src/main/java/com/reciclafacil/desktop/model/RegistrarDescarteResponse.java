package com.reciclafacil.desktop.model;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import com.fasterxml.jackson.annotation.JsonProperty;

import java.util.List;

@JsonIgnoreProperties(ignoreUnknown = true)
public class RegistrarDescarteResponse {

    private PontuacaoResumo pontuacao;
    @JsonProperty("pontos_ganhos")
    private int pontosGanhos;
    @JsonProperty("novas_conquistas")
    private List<Conquista> novasConquistas;

    public PontuacaoResumo getPontuacao() {
        return pontuacao;
    }

    public void setPontuacao(PontuacaoResumo pontuacao) {
        this.pontuacao = pontuacao;
    }

    public int getPontosGanhos() {
        return pontosGanhos;
    }

    public void setPontosGanhos(int pontosGanhos) {
        this.pontosGanhos = pontosGanhos;
    }

    public List<Conquista> getNovasConquistas() {
        return novasConquistas;
    }

    public void setNovasConquistas(List<Conquista> novasConquistas) {
        this.novasConquistas = novasConquistas;
    }
}

