package com.reciclafacil.desktop.controller;

import com.reciclafacil.desktop.model.Conquista;
import com.reciclafacil.desktop.model.RankingEntry;
import com.reciclafacil.desktop.util.NumberFormatter;
import javafx.geometry.Insets;
import javafx.scene.control.Label;
import javafx.scene.control.ListCell;
import javafx.scene.layout.HBox;
import javafx.scene.layout.Priority;
import javafx.scene.layout.VBox;

/**
 * Helper para montar células customizadas das listas.
 */
final class ViewCellFactory {

    private ViewCellFactory() {}

    static class ConquistaCell extends ListCell<Conquista> {
        private final HBox container = new HBox();
        private final Label icone = new Label();
        private final VBox textoBox = new VBox();
        private final Label titulo = new Label();
        private final Label descricao = new Label();
        private final Label status = new Label();

        ConquistaCell() {
            container.getStyleClass().add("achievement-cell");
            icone.getStyleClass().add("achievement-icon");
            titulo.getStyleClass().add("achievement-title");
            descricao.getStyleClass().add("achievement-description");
            status.getStyleClass().add("achievement-status");

            textoBox.getChildren().addAll(titulo, descricao);
            HBox.setHgrow(textoBox, Priority.ALWAYS);
            container.setSpacing(12);
            container.setPadding(new Insets(10));
            container.getChildren().addAll(icone, textoBox, status);
        }

        @Override
        protected void updateItem(Conquista conquista, boolean empty) {
            super.updateItem(conquista, empty);
            if (empty || conquista == null) {
                setGraphic(null);
                return;
            }
            icone.setText(conquista.getIcone());
            titulo.setText(conquista.getNome());
            descricao.setText(conquista.getDescricao());
            status.setText(conquista.isDesbloqueada() ? "✓" : String.format("%.0f%%", conquista.getProgresso()));
            container.pseudoClassStateChanged(javafx.css.PseudoClass.getPseudoClass("locked"), !conquista.isDesbloqueada());
            setGraphic(container);
        }
    }

    static class RankingCell extends ListCell<RankingEntry> {
        private final HBox container = new HBox();
        private final Label posicao = new Label();
        private final Label nome = new Label();
        private final Label pontos = new Label();

        RankingCell() {
            container.getStyleClass().add("ranking-cell");
            posicao.getStyleClass().add("ranking-position");
            nome.getStyleClass().add("ranking-name");
            pontos.getStyleClass().add("ranking-points");

            HBox.setHgrow(nome, Priority.ALWAYS);
            container.getChildren().addAll(posicao, nome, pontos);
            container.setSpacing(12);
            container.setPadding(new Insets(8, 12, 8, 12));
        }

        @Override
        protected void updateItem(RankingEntry item, boolean empty) {
            super.updateItem(item, empty);
            if (empty || item == null) {
                setGraphic(null);
                return;
            }
            posicao.setText("#" + item.getPosicao());
            nome.setText(item.getNome());
            pontos.setText(NumberFormatter.formatInt(item.getPontos()) + " pts");
            setGraphic(container);
        }
    }
}

