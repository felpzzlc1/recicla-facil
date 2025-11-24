package com.reciclafacil.desktop;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

import java.io.IOException;
import java.net.URL;
import java.util.Locale;

/**
 * Ponto de entrada do cliente JavaFX.
 */
public class MainApp extends Application {

    @Override
    public void start(Stage primaryStage) throws IOException {
        Locale.setDefault(new Locale("pt", "BR"));
        URL resource = getClass().getResource("/fxml/pontuacao-view.fxml");
        if (resource == null) {
            throw new IllegalStateException("FXML de pontuação não encontrado.");
        }
        Parent root = FXMLLoader.load(resource);
        Scene scene = new Scene(root, 1280, 720);
        scene.getStylesheets().add(getClass().getResource("/styles/base.css").toExternalForm());
        scene.getStylesheets().add(getClass().getResource("/styles/pontuacao.css").toExternalForm());
        primaryStage.setTitle("Recicla Fácil - Dashboard de Pontuação");
        primaryStage.setScene(scene);
        primaryStage.show();
    }

    public static void main(String[] args) {
        launch(args);
    }
}

