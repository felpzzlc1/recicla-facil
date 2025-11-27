package com.reciclafacil.desktop.controller;

import com.reciclafacil.desktop.model.Conquista;
import com.reciclafacil.desktop.model.CronogramaColeta;
import com.reciclafacil.desktop.model.PontoColeta;
import com.reciclafacil.desktop.model.Recompensa;
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
    
    static class PontoColetaCell extends ListCell<PontoColeta> {
        private final VBox container = new VBox();
        private final HBox header = new HBox();
        private final Label nome = new Label();
        private final Label tipo = new Label();
        private final Label endereco = new Label();
        private final Label telefone = new Label();
        private final Label horario = new Label();
        private final Label materiais = new Label();
        private final Label distancia = new Label();

        PontoColetaCell() {
            container.getStyleClass().add("ponto-coleta-cell");
            nome.getStyleClass().add("ponto-coleta-nome");
            tipo.getStyleClass().add("badge");
            endereco.getStyleClass().add("ponto-coleta-info");
            telefone.getStyleClass().add("ponto-coleta-info");
            horario.getStyleClass().add("ponto-coleta-info");
            materiais.getStyleClass().add("ponto-coleta-info");
            distancia.getStyleClass().add("ponto-coleta-distancia");
            
            header.getChildren().addAll(nome, tipo, distancia);
            header.setSpacing(12);
            HBox.setHgrow(nome, Priority.ALWAYS);
            
            container.getChildren().addAll(header, endereco, telefone, horario, materiais);
            container.setSpacing(8);
            container.setPadding(new Insets(16));
        }

        @Override
        protected void updateItem(PontoColeta ponto, boolean empty) {
            super.updateItem(ponto, empty);
            if (empty || ponto == null) {
                setGraphic(null);
                return;
            }
            nome.setText(ponto.getNome());
            tipo.setText(ponto.getTipo() != null ? ponto.getTipo() : "Ponto de Coleta");
            endereco.setText("📍 " + ponto.getEndereco());
            telefone.setText(ponto.getTelefone() != null ? "📞 " + ponto.getTelefone() : "");
            horario.setText(ponto.getHorario() != null ? "🕐 " + ponto.getHorario() : "");
            if (ponto.getMateriaisAceitos() != null && !ponto.getMateriaisAceitos().isEmpty()) {
                materiais.setText("♻️ " + String.join(", ", ponto.getMateriaisAceitos()));
            } else {
                materiais.setText("");
            }
            distancia.setText(ponto.getDistancia() != null ? ponto.getDistancia() : "");
            setGraphic(container);
        }
    }
    
    static class CronogramaCell extends ListCell<CronogramaColeta> {
        private final VBox container = new VBox();
        private final HBox header = new HBox();
        private final Label material = new Label();
        private final Label diaSemana = new Label();
        private final Label horario = new Label();
        private final Label localizacao = new Label();

        CronogramaCell() {
            container.getStyleClass().add("cronograma-cell");
            material.getStyleClass().add("cronograma-material");
            diaSemana.getStyleClass().add("badge");
            horario.getStyleClass().add("cronograma-horario");
            localizacao.getStyleClass().add("cronograma-info");
            
            header.getChildren().addAll(material, diaSemana);
            header.setSpacing(12);
            HBox.setHgrow(material, Priority.ALWAYS);
            
            container.getChildren().addAll(header, horario, localizacao);
            container.setSpacing(8);
            container.setPadding(new Insets(16));
        }

        @Override
        protected void updateItem(CronogramaColeta cronograma, boolean empty) {
            super.updateItem(cronograma, empty);
            if (empty || cronograma == null) {
                setGraphic(null);
                return;
            }
            material.setText("♻️ " + cronograma.getMaterial());
            diaSemana.setText(cronograma.getDiaSemana());
            horario.setText("🕐 " + cronograma.getHorarioFormatado());
            localizacao.setText("📍 " + cronograma.getLocalizacaoCompleta());
            setGraphic(container);
        }
    }
    
    static class RecompensaCell extends ListCell<Recompensa> {
        private final HBox container = new HBox();
        private final Label icone = new Label();
        private final VBox infoBox = new VBox();
        private final Label titulo = new Label();
        private final Label descricao = new Label();
        private final HBox footer = new HBox();
        private final Label pontos = new Label();
        private final Label disponivel = new Label();

        RecompensaCell() {
            container.getStyleClass().add("recompensa-cell");
            icone.getStyleClass().add("recompensa-icon");
            titulo.getStyleClass().add("recompensa-titulo");
            descricao.getStyleClass().add("recompensa-descricao");
            pontos.getStyleClass().add("recompensa-pontos");
            disponivel.getStyleClass().add("badge");
            
            footer.getChildren().addAll(pontos, disponivel);
            footer.setSpacing(12);
            infoBox.getChildren().addAll(titulo, descricao, footer);
            HBox.setHgrow(infoBox, Priority.ALWAYS);
            container.getChildren().addAll(icone, infoBox);
            container.setSpacing(12);
            container.setPadding(new Insets(16));
        }

        @Override
        protected void updateItem(Recompensa recompensa, boolean empty) {
            super.updateItem(recompensa, empty);
            if (empty || recompensa == null) {
                setGraphic(null);
                return;
            }
            icone.setText(recompensa.getIcone() != null ? recompensa.getIcone() : "🎁");
            titulo.setText(recompensa.getTitulo());
            descricao.setText(recompensa.getDescricao());
            pontos.setText(NumberFormatter.formatInt(recompensa.getPontos()) + " pontos");
            disponivel.setText(recompensa.isDisponivel() ? "Disponível (" + recompensa.getDisponivel() + ")" : "Indisponível");
            setGraphic(container);
        }
    }
}

