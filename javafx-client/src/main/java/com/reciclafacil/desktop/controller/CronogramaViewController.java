package com.reciclafacil.desktop.controller;

import com.reciclafacil.desktop.core.AppContext;
import com.reciclafacil.desktop.model.CronogramaColeta;
import com.reciclafacil.desktop.service.CronogramaService;
import javafx.application.Platform;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.ComboBox;
import javafx.scene.control.Label;
import javafx.scene.control.ListView;
import javafx.scene.layout.VBox;

import java.io.IOException;
import java.util.List;
import java.util.concurrent.CompletableFuture;

public class CronogramaViewController {
    
    @FXML
    private VBox contentContainer;
    
    @FXML
    private VBox loadingState;
    
    @FXML
    private VBox errorState;
    
    @FXML
    private Label errorMessageLabel;
    
    @FXML
    private ListView<CronogramaColeta> cronogramaListView;
    
    @FXML
    private ComboBox<String> filtroMaterial;
    
    @FXML
    private ComboBox<String> filtroDia;
    
    private final ObservableList<CronogramaColeta> cronogramas = FXCollections.observableArrayList();
    private final CronogramaService cronogramaService = AppContext.get().getCronogramaService();
    
    @FXML
    public void initialize() {
        loadingState.managedProperty().bind(loadingState.visibleProperty());
        errorState.managedProperty().bind(errorState.visibleProperty());
        contentContainer.managedProperty().bind(contentContainer.visibleProperty());
        
        cronogramaListView.setItems(cronogramas);
        cronogramaListView.setCellFactory(list -> new ViewCellFactory.CronogramaCell());
        
        filtroMaterial.setItems(FXCollections.observableArrayList("Papel", "Plástico", "Vidro", "Metal", "Orgânico"));
        filtroDia.setItems(FXCollections.observableArrayList("Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado", "Domingo"));
        
        filtroMaterial.setOnAction(e -> aplicarFiltros());
        filtroDia.setOnAction(e -> aplicarFiltros());
        
        carregarCronograma();
    }
    
    @FXML
    private void atualizarLista(ActionEvent event) {
        carregarCronograma();
    }
    
    @FXML
    private void tentarNovamente(ActionEvent event) {
        carregarCronograma();
    }
    
    private void aplicarFiltros() {
        // Por enquanto, apenas recarrega tudo
        // Pode ser melhorado para filtrar localmente ou fazer requisições específicas
        carregarCronograma();
    }
    
    private void carregarCronograma() {
        setLoading(true);
        setError(null);
        CompletableFuture
                .supplyAsync(this::fetchCronograma)
                .whenComplete((lista, throwable) -> Platform.runLater(() -> {
                    setLoading(false);
                    if (throwable != null) {
                        setError("Não foi possível carregar o cronograma. " + throwable.getMessage());
                        return;
                    }
                    if (lista != null) {
                        cronogramas.setAll(lista);
                        contentContainer.setVisible(true);
                    }
                }));
    }
    
    private List<CronogramaColeta> fetchCronograma() {
        try {
            String material = filtroMaterial.getValue();
            String dia = filtroDia.getValue();
            
            if (material != null && !material.isEmpty()) {
                return cronogramaService.listarPorMaterial(material);
            } else if (dia != null && !dia.isEmpty()) {
                return cronogramaService.listarPorDiaSemana(dia);
            } else {
                return cronogramaService.listarProximos();
            }
        } catch (IOException | InterruptedException e) {
            throw new RuntimeException(e);
        }
    }
    
    private void setLoading(boolean loading) {
        loadingState.setVisible(loading);
        contentContainer.setDisable(loading);
    }
    
    private void setError(String message) {
        boolean hasError = message != null && !message.isBlank();
        errorState.setVisible(hasError);
        errorMessageLabel.setText(hasError ? message : "");
        contentContainer.setVisible(!hasError);
    }
}

