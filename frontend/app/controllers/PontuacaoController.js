(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('PontuacaoController', ['$scope', '$timeout', 'pontuacaoService', 'AuthService', 'ModalService', function($scope, $timeout, pontuacaoService, AuthService, modalService) {
      var vm = this;
      
      // Estado inicial
      vm.carregando = true;
      vm.erro = null;
      vm.pontos = 0;
      vm.nivel = 1;
      vm.nivelNome = 'Iniciante';
      vm.pontosProximoNivel = 100;
      vm.pontosRestantes = 100;
      vm.progressoNivel = 0;
      vm.aumentoSemanal = 0;
      
      vm.estatisticas = {
        descartes: 0,
        sequencia: 0,
        badges: 0
      };
      
      vm.conquistas = [];
      vm.ranking = [];
      vm.estatisticasGerais = {};
      
      // Modal de simulação de descarte
      vm.mostrarModalDescarte = false;
      vm.materialSelecionado = 'papel';
      vm.pesoDescarte = 1;
      vm.pontosCalculados = 0;
      
      // Materiais disponíveis
      vm.materiais = [
        { codigo: 'papel', nome: 'Papel', icone: '📄', pontosPorKg: 10 },
        { codigo: 'plastico', nome: 'Plástico', icone: '♻️', pontosPorKg: 15 },
        { codigo: 'vidro', nome: 'Vidro', icone: '🍶', pontosPorKg: 20 },
        { codigo: 'metal', nome: 'Metal', icone: '🔧', pontosPorKg: 25 },
        { codigo: 'organico', nome: 'Orgânico', icone: '🍃', pontosPorKg: 5 }
      ];

      // Inicializar dados
      vm.init = function() {
        // Atualizar status de login primeiro
        AuthService.refreshLoginStatus();
        
        vm.carregarEstatisticas();
        vm.carregarConquistas();
        vm.carregarRanking();
        vm.carregarEstatisticasGerais();
      };

      // Carregar estatísticas do usuário
      vm.carregarEstatisticas = function() {
        vm.carregando = true;
        vm.erro = null;
        
        // Verificar se usuário está logado
        if (!AuthService.isLoggedIn()) {
          vm.erro = 'Você precisa estar logado para ver suas estatísticas.';
          vm.carregando = false;
          return;
        }
        
        pontuacaoService.obterEstatisticas()
          .then(function(response) {
            if (response.success) {
              var dados = response.data;
              vm.pontos = dados.pontos;
              vm.nivel = dados.nivel;
              vm.nivelNome = dados.nivel_nome;
              vm.pontosProximoNivel = dados.pontos_para_proximo_nivel;
              vm.pontosRestantes = dados.pontos_para_proximo_nivel;
              vm.progressoNivel = dados.progresso_nivel;
              vm.aumentoSemanal = dados.pontos_semana_atual;
              
              vm.estatisticas = {
                descartes: dados.descartes,
                sequencia: dados.sequencia_dias,
                badges: dados.badges_conquistadas
              };
            } else {
              vm.erro = response.message || 'Erro ao carregar estatísticas';
              console.error('Erro na resposta:', response);
            }
          })
          .catch(function(error) {
            console.error('Erro ao carregar estatísticas:', error);
            if (error.status === 401) {
              vm.erro = 'Sessão expirada. Faça login novamente.';
              // Limpar sessão e redirecionar para login
              AuthService.logout();
            } else {
              vm.erro = 'Erro ao conectar com o servidor: ' + (error.message || 'Erro desconhecido');
            }
          })
          .finally(function() {
            vm.carregando = false;
          });
      };

      // Carregar conquistas
      vm.carregarConquistas = function() {
        pontuacaoService.obterConquistas()
          .then(function(response) {
            if (response.success) {
              vm.conquistas = response.data;
            }
          })
          .catch(function(error) {
            console.error('Erro ao carregar conquistas:', error);
          });
      };

      // Carregar ranking
      vm.carregarRanking = function() {
        pontuacaoService.obterRanking(10)
          .then(function(response) {
            if (response.success) {
              vm.ranking = response.data;
            }
          })
          .catch(function(error) {
            console.error('Erro ao carregar ranking:', error);
          });
      };

      // Carregar estatísticas gerais
      vm.carregarEstatisticasGerais = function() {
        pontuacaoService.obterEstatisticasGerais()
          .then(function(response) {
            if (response.success) {
              vm.estatisticasGerais = response.data;
            }
          })
          .catch(function(error) {
            console.error('Erro ao carregar estatísticas gerais:', error);
          });
      };

      // Abrir modal de simulação de descarte
      vm.abrirModalDescarte = function() {
        vm.mostrarModalDescarte = true;
        vm.materialSelecionado = 'papel';
        vm.pesoDescarte = 1;
        vm.calcularPontos();
      };

      // Fechar modal de simulação
      vm.fecharModalDescarte = function() {
        vm.mostrarModalDescarte = false;
      };

      // Calcular pontos baseado no material e peso
      vm.calcularPontos = function() {
        vm.pontosCalculados = pontuacaoService.calcularPontosPorMaterial(
          vm.materialSelecionado, 
          vm.pesoDescarte
        );
      };

      // Simular descarte
      vm.simularDescarte = function() {
        if (!vm.materialSelecionado || !vm.pesoDescarte || vm.pesoDescarte <= 0) {
          alert('Por favor, selecione um material e informe um peso válido.');
          return;
        }

        vm.carregando = true;
        
        pontuacaoService.simularDescarte(vm.materialSelecionado, vm.pesoDescarte)
          .then(function(response) {
            if (response.success) {
              var dados = response.data;
              
              // Mostrar notificação de sucesso
              vm.mostrarNotificacao(
                'Descarte realizado!', 
                'Você ganhou ' + dados.pontos_ganhos + ' pontos!',
                'success'
              );
              
              // Verificar se há novas conquistas
              if (dados.novas_conquistas && dados.novas_conquistas.length > 0) {
                vm.mostrarNotificacaoConquistas(dados.novas_conquistas);
              }
              
              // Recarregar dados
              vm.carregarEstatisticas();
              vm.carregarConquistas();
              vm.fecharModalDescarte();
            } else {
              vm.erro = response.message || 'Erro ao simular descarte';
            }
          })
          .catch(function(error) {
            console.error('Erro ao simular descarte:', error);
            vm.erro = 'Erro ao conectar com o servidor';
          })
          .finally(function() {
            vm.carregando = false;
          });
      };

      // Mostrar notificação
      vm.mostrarNotificacao = function(titulo, mensagem, tipo) {
        // Implementar sistema de notificações
        // REVIEW: Sistema de notificações não implementado
      };

      // Mostrar notificação de conquistas
      vm.mostrarNotificacaoConquistas = function(conquistas) {
        var mensagem = 'Parabéns! Você conquistou: ';
        conquistas.forEach(function(conquista, index) {
          if (index > 0) mensagem += ', ';
          mensagem += conquista.nome;
        });
        vm.mostrarNotificacao('Nova Conquista!', mensagem, 'success');
      };

      // Formatar número
      vm.formatarNumero = function(numero) {
        if (numero === undefined || numero === null) {
          return '0';
        }
        return pontuacaoService.formatarNumero(numero);
      };

      // Obter nome do material
      vm.obterNomeMaterial = function(codigo) {
        return pontuacaoService.obterNomeMaterial(codigo);
      };

      // Obter ícone do material
      vm.obterIconeMaterial = function(codigo) {
        return pontuacaoService.obterIconeMaterial(codigo);
      };

      // Obter cor do nível
      vm.obterCorNivel = function() {
        return pontuacaoService.obterCorNivel(vm.nivel);
      };

      // Obter emoji do nível
      vm.obterEmojiNivel = function() {
        return pontuacaoService.obterEmojiNivel(vm.nivel);
      };

      // Obter número de conquistas desbloqueadas
      vm.getConquistasDesbloqueadas = function() {
        if (!vm.conquistas || !Array.isArray(vm.conquistas)) {
          return 0;
        }
        return vm.conquistas.filter(function(conquista) {
          return conquista.desbloqueada;
        }).length;
      };

      // Verificar se usuário está logado
      vm.verificarLogin = function() {
        if (!AuthService.isLoggedIn()) {
          modalService.openModal('login');
          return false;
        }
        return true;
      };

      // Inicializar quando o controller for carregado
      vm.init();
    }]);
})();