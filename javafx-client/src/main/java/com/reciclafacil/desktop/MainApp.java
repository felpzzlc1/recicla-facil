package com.reciclafacil.desktop;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

import java.net.URL;
import java.util.Locale;

/**
 * Ponto de entrada do cliente JavaFX.
 */
public class MainApp extends Application {

    @Override
    public void start(Stage primaryStage) {
        try {
            Locale.setDefault(new Locale("pt", "BR"));
            URL resource = getClass().getResource("/fxml/main-view.fxml");
            if (resource == null) {
                throw new IllegalStateException("FXML principal não encontrado.");
            }
            Parent root = FXMLLoader.load(resource);
            Scene scene = new Scene(root, 1280, 720);
            URL baseCss = getClass().getResource("/styles/base.css");
            if (baseCss != null) {
                scene.getStylesheets().add(baseCss.toExternalForm());
            }
            URL pontuacaoCss = getClass().getResource("/styles/pontuacao.css");
            if (pontuacaoCss != null) {
                scene.getStylesheets().add(pontuacaoCss.toExternalForm());
            }
            primaryStage.setTitle("Recicla Fácil - Sistema de Reciclagem");
            primaryStage.setScene(scene);
            primaryStage.show();
        } catch (Exception e) {
            e.printStackTrace();
            System.err.println("Erro ao iniciar aplicação: " + e.getMessage());
            e.printStackTrace();
            throw new RuntimeException("Falha ao iniciar aplicação", e);
        }
    }

    public static void main(String[] args) {
        launch(args);
    }
}

