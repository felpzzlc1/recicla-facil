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
      vm.pontosAtuaisNivel = 0;
      vm.pontosNecessariosNivel = 100;
      
      // Estatísticas serão inicializadas na função init
      
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
        
        // Inicializar variáveis
        vm.estatisticas = {
          descartes: 0,
          sequencia: 0,
          badges: 0
        };
        
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
              
              // Calcular pontos do nível atual
              vm.pontosAtuaisNivel = vm.calcularPontosNivelAtual(dados.pontos, dados.nivel);
              vm.pontosNecessariosNivel = vm.calcularPontosNecessariosNivel(dados.nivel);
              
              vm.estatisticas = {
                descartes: dados.descartes || 0,
                sequencia: dados.sequencia_dias || 0,
                badges: dados.badges_conquistadas || 0
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

      // Registrar descarte
      vm.registrarDescarte = function() {
        if (!vm.materialSelecionado || !vm.pesoDescarte || vm.pesoDescarte <= 0) {
          alert('Por favor, selecione um material e informe um peso válido.');
          return;
        }

        vm.carregando = true;
        
        pontuacaoService.registrarDescarte(vm.materialSelecionado, vm.pesoDescarte)
          .then(function(response) {
            if (response.success) {
              var dados = response.data;
              
              // Atualizar dados localmente sem recarregar
              if (dados.pontuacao) {
                vm.pontos = dados.pontuacao.pontos;
                vm.nivel = dados.pontuacao.nivel;
                vm.nivelNome = dados.pontuacao.nivel_nome;
                vm.aumentoSemanal = dados.pontuacao.pontos_semana_atual;
                vm.estatisticas.descartes = dados.pontuacao.descartes;
                vm.estatisticas.sequencia = dados.pontuacao.sequencia_dias;
                vm.estatisticas.badges = dados.pontuacao.badges_conquistadas;
                
                // Recalcular progresso do nível
                vm.pontosAtuaisNivel = vm.calcularPontosNivelAtual(dados.pontuacao.pontos, dados.pontuacao.nivel);
                vm.pontosNecessariosNivel = vm.calcularPontosNecessariosNivel(dados.pontuacao.nivel);
                vm.progressoNivel = Math.min(100, (vm.pontosAtuaisNivel / vm.pontosNecessariosNivel) * 100);
                vm.pontosRestantes = Math.max(0, vm.pontosNecessariosNivel - vm.pontosAtuaisNivel);
              } else {
                // Se não há dados de pontuação, recarregar estatísticas
                vm.carregarEstatisticas();
              }
              
              // Mostrar notificação de sucesso
              vm.mostrarNotificacao(
                'Descarte registrado!', 
                'Você ganhou ' + (dados.pontos_ganhos || 0) + ' pontos!',
                'success'
              );
              
              // Verificar se há novas conquistas
              if (dados.novas_conquistas && dados.novas_conquistas.length > 0) {
                vm.mostrarNotificacaoConquistas(dados.novas_conquistas);
              }
              
              // Recarregar apenas conquistas e ranking
              vm.carregarConquistas();
              vm.carregarRanking();
              vm.fecharModalDescarte();
            } else {
              vm.erro = response.message || 'Erro ao registrar descarte';
            }
          })
          .catch(function(error) {
            console.error('Erro ao registrar descarte:', error);
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

      // Calcular pontos do nível atual
      vm.calcularPontosNivelAtual = function(pontos, nivel) {
        var pontosNivel = [0, 100, 500, 1000, 2500, 5000, 10000, 25000, 50000, 100000];
        var pontosInicioNivel = pontosNivel[nivel - 1] || 0;
        return Math.max(0, pontos - pontosInicioNivel);
      };

      // Calcular pontos necessários para o nível atual
      vm.calcularPontosNecessariosNivel = function(nivel) {
        var pontosNivel = [0, 100, 500, 1000, 2500, 5000, 10000, 25000, 50000, 100000];
        var pontosInicioNivel = pontosNivel[nivel - 1] || 0;
        var pontosFimNivel = pontosNivel[nivel] || 100000;
        return pontosFimNivel - pontosInicioNivel;
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