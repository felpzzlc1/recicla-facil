package com.reciclafacil.desktop.model;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import com.fasterxml.jackson.annotation.JsonProperty;
import com.fasterxml.jackson.databind.annotation.JsonDeserialize;
import com.reciclafacil.desktop.util.JsonStringListDeserializer;

import java.util.List;

@JsonIgnoreProperties(ignoreUnknown = true)
public class PontoColeta {
    
    private int id;
    private String nome;
    private String tipo;
    private String endereco;
    private String telefone;
    private String horario;
    private Double latitude;
    private Double longitude;
    @JsonProperty("materiais_aceitos")
    @JsonDeserialize(using = JsonStringListDeserializer.class)
    private List<String> materiaisAceitos;
    private boolean ativo;
    private String distancia; // Calculado no backend

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getNome() {
        return nome;
    }

    public void setNome(String nome) {
        this.nome = nome;
    }

    public String getTipo() {
        return tipo;
    }

    public void setTipo(String tipo) {
        this.tipo = tipo;
    }

    public String getEndereco() {
        return endereco;
    }

    public void setEndereco(String endereco) {
        this.endereco = endereco;
    }

    public String getTelefone() {
        return telefone;
    }

    public void setTelefone(String telefone) {
        this.telefone = telefone;
    }

    public String getHorario() {
        return horario;
    }

    public void setHorario(String horario) {
        this.horario = horario;
    }

    public Double getLatitude() {
        return latitude;
    }

    public void setLatitude(Double latitude) {
        this.latitude = latitude;
    }

    public Double getLongitude() {
        return longitude;
    }

    public void setLongitude(Double longitude) {
        this.longitude = longitude;
    }

    public List<String> getMateriaisAceitos() {
        return materiaisAceitos;
    }

    public void setMateriaisAceitos(List<String> materiaisAceitos) {
        this.materiaisAceitos = materiaisAceitos;
    }

    public boolean isAtivo() {
        return ativo;
    }

    public void setAtivo(boolean ativo) {
        this.ativo = ativo;
    }

    public String getDistancia() {
        return distancia;
    }

    public void setDistancia(String distancia) {
        this.distancia = distancia;
    }
}

