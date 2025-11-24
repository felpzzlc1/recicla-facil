package com.reciclafacil.desktop.model;

import java.util.Collections;
import java.util.List;

public class PontuacaoDashboard {

    private PontuacaoResumo resumo;
    private List<Conquista> conquistas = Collections.emptyList();
    private List<RankingEntry> ranking = Collections.emptyList();
    private EstatisticasGerais estatisticasGerais;

    public PontuacaoResumo getResumo() {
        return resumo;
    }

    public void setResumo(PontuacaoResumo resumo) {
        this.resumo = resumo;
    }

    public List<Conquista> getConquistas() {
        return conquistas;
    }

    public void setConquistas(List<Conquista> conquistas) {
        this.conquistas = conquistas;
    }

    public List<RankingEntry> getRanking() {
        return ranking;
    }

    public void setRanking(List<RankingEntry> ranking) {
        this.ranking = ranking;
    }

    public EstatisticasGerais getEstatisticasGerais() {
        return estatisticasGerais;
    }

    public void setEstatisticasGerais(EstatisticasGerais estatisticasGerais) {
        this.estatisticasGerais = estatisticasGerais;
    }
}

