package com.reciclafacil.desktop.controller;

import com.reciclafacil.desktop.core.AppContext;
import com.reciclafacil.desktop.model.Recompensa;
import com.reciclafacil.desktop.service.RecompensaService;
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

public class RecompensasViewController {
    
    @FXML
    private VBox contentContainer;
    
    @FXML
    private VBox loadingState;
    
    @FXML
    private VBox errorState;
    
    @FXML
    private Label errorMessageLabel;
    
    @FXML
    private ListView<Recompensa> recompensasListView;
    
    private final ObservableList<Recompensa> recompensas = FXCollections.observableArrayList();
    private final RecompensaService recompensaService = AppContext.get().getRecompensaService();
    
    @FXML
    public void initialize() {
        loadingState.managedProperty().bind(loadingState.visibleProperty());
        errorState.managedProperty().bind(errorState.visibleProperty());
        contentContainer.managedProperty().bind(contentContainer.visibleProperty());
        
        recompensasListView.setItems(recompensas);
        recompensasListView.setCellFactory(list -> new ViewCellFactory.RecompensaCell());
        
        carregarRecompensas();
    }
    
    @FXML
    private void atualizarLista(ActionEvent event) {
        carregarRecompensas();
    }
    
    @FXML
    private void tentarNovamente(ActionEvent event) {
        carregarRecompensas();
    }
    
    private void carregarRecompensas() {
        setLoading(true);
        setError(null);
        CompletableFuture
                .supplyAsync(this::fetchRecompensas)
                .whenComplete((lista, throwable) -> Platform.runLater(() -> {
                    setLoading(false);
                    if (throwable != null) {
                        setError("Não foi possível carregar as recompensas. " + throwable.getMessage());
                        return;
                    }
                    if (lista != null) {
                        recompensas.setAll(lista);
                        contentContainer.setVisible(true);
                    }
                }));
    }
    
    private List<Recompensa> fetchRecompensas() {
        try {
            return recompensaService.listarTodas();
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

