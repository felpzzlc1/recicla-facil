package com.reciclafacil.desktop.controller;

import com.reciclafacil.desktop.core.AppContext;
import com.reciclafacil.desktop.model.PontoColeta;
import com.reciclafacil.desktop.service.PontoColetaService;
import javafx.application.Platform;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Label;
import javafx.scene.control.ListView;
import javafx.scene.layout.VBox;

import java.io.IOException;
import java.util.List;
import java.util.concurrent.CompletableFuture;

public class PontosColetaViewController {
    
    @FXML
    private VBox contentContainer;
    
    @FXML
    private VBox loadingState;
    
    @FXML
    private VBox errorState;
    
    @FXML
    private Label errorMessageLabel;
    
    @FXML
    private ListView<PontoColeta> pontosListView;
    
    private final ObservableList<PontoColeta> pontos = FXCollections.observableArrayList();
    private final PontoColetaService pontoColetaService = AppContext.get().getPontoColetaService();
    
    @FXML
    public void initialize() {
        loadingState.managedProperty().bind(loadingState.visibleProperty());
        errorState.managedProperty().bind(errorState.visibleProperty());
        contentContainer.managedProperty().bind(contentContainer.visibleProperty());
        
        pontosListView.setItems(pontos);
        pontosListView.setCellFactory(list -> new ViewCellFactory.PontoColetaCell());
        
        carregarPontos();
    }
    
    @FXML
    private void atualizarLista(ActionEvent event) {
        carregarPontos();
    }
    
    @FXML
    private void tentarNovamente(ActionEvent event) {
        carregarPontos();
    }
    
    private void carregarPontos() {
        setLoading(true);
        setError(null);
        CompletableFuture
                .supplyAsync(this::fetchPontos)
                .whenComplete((lista, throwable) -> Platform.runLater(() -> {
                    setLoading(false);
                    if (throwable != null) {
                        setError("Não foi possível carregar os pontos de coleta. " + throwable.getMessage());
                        return;
                    }
                    if (lista != null) {
                        pontos.setAll(lista);
                        contentContainer.setVisible(true);
                    }
                }));
    }
    
    private List<PontoColeta> fetchPontos() {
        try {
            return pontoColetaService.listarTodos();
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

