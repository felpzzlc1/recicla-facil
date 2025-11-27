package com.reciclafacil.desktop.controller;

import com.reciclafacil.desktop.core.AppContext;
import com.reciclafacil.desktop.model.Conquista;
import com.reciclafacil.desktop.model.PontuacaoDashboard;
import com.reciclafacil.desktop.model.PontuacaoResumo;
import com.reciclafacil.desktop.model.RankingEntry;
import com.reciclafacil.desktop.model.RegistrarDescarteResponse;
import com.reciclafacil.desktop.service.PontuacaoService;
import com.reciclafacil.desktop.util.NumberFormatter;
import javafx.application.Platform;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.concurrent.Task;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.ListView;
import javafx.scene.control.ProgressBar;
import javafx.scene.control.Slider;
import javafx.scene.control.Spinner;
import javafx.scene.control.SpinnerValueFactory;
import javafx.scene.control.TextField;
import javafx.scene.input.KeyEvent;
import javafx.scene.layout.StackPane;
import javafx.scene.layout.VBox;
import javafx.scene.shape.Rectangle;

import java.io.IOException;
import java.util.List;
import java.util.Map;
import java.util.Objects;
import java.util.concurrent.CompletableFuture;

public class PontuacaoViewController {

    @FXML
    private StackPane rootPane;
    @FXML
    private VBox loadingState;
    @FXML
    private VBox errorState;
    @FXML
    private Label errorMessageLabel;
    @FXML
    private VBox contentContainer;
    @FXML
    private Label pontosLabel;
    @FXML
    private Label aumentoSemanalLabel;
    @FXML
    private Label nivelLabel;
    @FXML
    private Label nivelNomeLabel;
    @FXML
    private ProgressBar progressoNivelBar;
    @FXML
    private Label progressoNivelTexto;
    @FXML
    private Label faltamPontosLabel;
    @FXML
    private Label descartesLabel;
    @FXML
    private Label sequenciaLabel;
    @FXML
    private Label badgesLabel;
    @FXML
    private ListView<Conquista> conquistasListView;
    @FXML
    private ListView<RankingEntry> rankingListView;
    @FXML
    private Label conquistasResumoLabel;
    @FXML
    private StackPane modalOverlay;
    @FXML
    private Spinner<Double> pesoSpinner;
    @FXML
    private TextField materialField;
    @FXML
    private Label pontosPreviewLabel;
    @FXML
    private Button registrarButton;

    private final ObservableList<Conquista> conquistas = FXCollections.observableArrayList();
    private final ObservableList<RankingEntry> ranking = FXCollections.observableArrayList();

    private final PontuacaoService pontuacaoService = AppContext.get().getPontuacaoService();

    private PontuacaoResumo resumoAtual;

    @FXML
    public void initialize() {
        loadingState.managedProperty().bind(loadingState.visibleProperty());
        errorState.managedProperty().bind(errorState.visibleProperty());
        contentContainer.managedProperty().bind(contentContainer.visibleProperty());
        modalOverlay.managedProperty().bind(modalOverlay.visibleProperty());

        conquistasListView.setItems(conquistas);
        conquistasListView.setCellFactory(list -> new ViewCellFactory.ConquistaCell());
        rankingListView.setItems(ranking);
        rankingListView.setCellFactory(list -> new ViewCellFactory.RankingCell());

        pesoSpinner.setValueFactory(new SpinnerValueFactory.DoubleSpinnerValueFactory(0.1, 100, 1, 0.1));
        pesoSpinner.valueProperty().addListener((obs, oldVal, newVal) -> atualizarPontosPreview());

        carregarConteudo();
    }

    @FXML
    private void tentarNovamente(ActionEvent event) {
        carregarConteudo();
    }

    @FXML
    private void abrirModal() {
        modalOverlay.setVisible(true);
        materialField.setText("papel");
        pesoSpinner.getValueFactory().setValue(1.0);
        atualizarPontosPreview();
    }

    @FXML
    private void fecharModal() {
        modalOverlay.setVisible(false);
    }

    @FXML
    private void registrarDescarte(ActionEvent event) {
        String material = materialField.getText();
        double peso = pesoSpinner.getValue();
        if (material == null || material.isBlank()) {
            mostrarErro("Informe o material descartado.");
            return;
        }
        setLoading(true);
        CompletableFuture
                .supplyAsync(() -> executarRegistro(material, peso))
                .whenComplete((response, throwable) -> Platform.runLater(() -> {
                    setLoading(false);
                    if (throwable != null) {
                        mostrarErro("Falha ao registrar descarte: " + throwable.getMessage());
                        return;
                    }
                    if (response != null && response.getPontuacao() != null) {
                        atualizarResumo(response.getPontuacao());
                        fecharModal();
                    }
                }));
    }

    private RegistrarDescarteResponse executarRegistro(String material, double peso) {
        try {
            return pontuacaoService.registrarDescarte(material, peso);
        } catch (IOException | InterruptedException e) {
            throw new RuntimeException(e);
        }
    }

    private void carregarConteudo() {
        setLoading(true);
        setError(null);
        CompletableFuture
                .supplyAsync(this::fetchDashboard)
                .whenComplete((dashboard, throwable) -> Platform.runLater(() -> {
                    setLoading(false);
                    if (throwable != null) {
                        setError("Não foi possível carregar suas estatísticas. " + throwable.getMessage());
                        return;
                    }
                    if (dashboard != null) {
                        preencherDashboard(dashboard);
                    }
                }));
    }

    private PontuacaoDashboard fetchDashboard() {
        try {
            return pontuacaoService.carregarPainelCompleto(10);
        } catch (IOException | InterruptedException e) {
            throw new RuntimeException(e);
        }
    }

    private void preencherDashboard(PontuacaoDashboard dashboard) {
        atualizarResumo(dashboard.getResumo());
        atualizarConquistas(dashboard.getConquistas());
        atualizarRanking(dashboard.getRanking());
    }

    private void atualizarResumo(PontuacaoResumo resumo) {
        if (resumo == null) {
            return;
        }
        this.resumoAtual = resumo;
        contentContainer.setVisible(true);
        pontosLabel.setText(NumberFormatter.formatInt(resumo.getPontuacaoTotal()));
        aumentoSemanalLabel.setText("+" + NumberFormatter.formatInt(resumo.getPontosSemanaAtual()) + " esta semana");
        nivelLabel.setText("Nível " + resumo.getNivel());
        nivelNomeLabel.setText(resumo.getNivelNome());

        double progresso = resumo.getProgressoNivel() / 100.0;
        progressoNivelBar.setProgress(progresso);
        progressoNivelTexto.setText(NumberFormatter.formatInt(resumo.getPontuacaoTotal()) + " / " +
                NumberFormatter.formatInt(resumo.getPontuacaoTotal() + resumo.getPontosParaProximoNivel()));

        faltamPontosLabel.setText("Faltam " + NumberFormatter.formatInt(resumo.getPontosParaProximoNivel()) + " pontos");
        descartesLabel.setText(NumberFormatter.formatInt(resumo.getDescartes()));
        sequenciaLabel.setText(NumberFormatter.formatInt(resumo.getSequenciaDias()));
        badgesLabel.setText(NumberFormatter.formatInt(resumo.getBadgesConquistadas()));
    }

    private void atualizarConquistas(List<Conquista> lista) {
        conquistas.setAll(lista);
        long desbloqueadas = lista.stream().filter(Conquista::isDesbloqueada).count();
        conquistasResumoLabel.setText(desbloqueadas + " / " + lista.size());
    }

    private void atualizarRanking(List<RankingEntry> lista) {
        ranking.setAll(lista);
    }

    private void atualizarPontosPreview() {
        double peso = pesoSpinner.getValue();
        String material = materialField.getText();
        int pontos = calcularPontos(material, peso);
        pontosPreviewLabel.setText(pontos + " pontos");
        registrarButton.setDisable(pontos <= 0 || material == null || material.isBlank());
    }

    @FXML
    private void atualizarPontosPreview(KeyEvent event) {
        atualizarPontosPreview();
    }

    private int calcularPontos(String material, double peso) {
        Map<String, Integer> pontosPorKg = Map.of(
                "papel", 10,
                "plastico", 15,
                "vidro", 20,
                "metal", 25,
                "organico", 5
        );
        return (int) Math.round(peso * pontosPorKg.getOrDefault(material, 10));
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

    private void mostrarErro(String mensagem) {
        setError(mensagem);
    }
}

