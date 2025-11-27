package com.reciclafacil.desktop.controller;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.control.Tab;
import javafx.scene.control.TabPane;
import javafx.scene.layout.StackPane;

import java.io.IOException;

public class MainViewController {
    
    @FXML
    private TabPane tabPane;
    
    @FXML
    private Tab dashboardTab;
    
    @FXML
    private Tab pontosTab;
    
    @FXML
    private Tab cronogramaTab;
    
    @FXML
    private Tab recompensasTab;
    
    @FXML
    private StackPane dashboardContainer;
    
    @FXML
    private StackPane pontosContainer;
    
    @FXML
    private StackPane cronogramaContainer;
    
    @FXML
    private StackPane recompensasContainer;
    
    @FXML
    public void initialize() {
        try {
            // Carregar Dashboard (Pontuação)
            FXMLLoader dashboardLoader = new FXMLLoader(getClass().getResource("/fxml/pontuacao-view.fxml"));
            Parent dashboardView = dashboardLoader.load();
            dashboardContainer.getChildren().add(dashboardView);
            
            // Carregar Pontos de Coleta
            FXMLLoader pontosLoader = new FXMLLoader(getClass().getResource("/fxml/pontos-coleta-view.fxml"));
            Parent pontosView = pontosLoader.load();
            pontosContainer.getChildren().add(pontosView);
            
            // Carregar Cronograma
            FXMLLoader cronogramaLoader = new FXMLLoader(getClass().getResource("/fxml/cronograma-view.fxml"));
            Parent cronogramaView = cronogramaLoader.load();
            cronogramaContainer.getChildren().add(cronogramaView);
            
            // Carregar Recompensas
            FXMLLoader recompensasLoader = new FXMLLoader(getClass().getResource("/fxml/recompensas-view.fxml"));
            Parent recompensasView = recompensasLoader.load();
            recompensasContainer.getChildren().add(recompensasView);
            
        } catch (IOException e) {
            e.printStackTrace();
            System.err.println("Erro ao carregar views: " + e.getMessage());
        }
    }
}

