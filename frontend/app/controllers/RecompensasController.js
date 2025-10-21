(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('RecompensasController', function($scope, RecompensaService, AuthService, pontuacaoService, $timeout) {
      var vm = this;
      
      // Estado inicial
      vm.recompensas = [];
      vm.carregando = true;
      vm.erro = null;
      vm.pontosUsuario = 0;
      vm.resgatando = false;
      vm.mostrarModalConfirmacao = false;
      vm.recompensaSelecionada = null;
      vm.pontosAposResgate = 0;
      vm.mostrarNotificacao = false;
      vm.tipoNotificacao = '';
      vm.mensagemNotificacao = '';

      // Carregar dados iniciais
      vm.init = function() {
        carregarRecompensas();
        carregarPontosUsuario();
      };

      // Carregar recompensas do servidor
      function carregarRecompensas() {
        vm.carregando = true;
        vm.erro = null;
        
        RecompensaService.obterRecompensas()
          .then(function(recompensas) {
            vm.recompensas = recompensas;
            vm.carregando = false;
          })
          .catch(function(error) {
            console.error('Erro ao carregar recompensas:', error);
            vm.erro = 'Erro ao carregar recompensas. Tente novamente.';
            vm.carregando = false;
          });
      }

      // Carregar pontos do usuário
      function carregarPontosUsuario() {
        // Verificar se está logado
        if (!AuthService.isLoggedIn()) {
          vm.pontosUsuario = 0;
          return;
        }

        // Buscar pontos reais da API
        pontuacaoService.obterEstatisticas()
          .then(function(response) {
            if (response.success) {
              vm.pontosUsuario = response.data.pontos || 0;
            } else {
              console.error('Erro ao obter estatísticas:', response.message);
              vm.pontosUsuario = 0;
            }
          })
          .catch(function(error) {
            console.error('Erro ao carregar pontos do usuário:', error);
            // Fallback para dados mockados se a API falhar
            try {
              var session = JSON.parse(localStorage.getItem('rf_session') || 'null');
              if (session && session.profile) {
                vm.pontosUsuario = 8350; // Valor mockado para desenvolvimento
              } else {
                vm.pontosUsuario = 0;
              }
            } catch(e) {
              vm.pontosUsuario = 0;
            }
          });
      }

      // Resgatar uma recompensa
      vm.resgatarRecompensa = function(recompensa) {
        if (vm.resgatando) return;
        
        // Verificar se tem pontos suficientes
        if (vm.pontosUsuario < recompensa.pontos) {
          alert('Você não tem pontos suficientes para esta recompensa!');
          return;
        }

        // Verificar se está disponível
        if (recompensa.disponivel <= 0) {
          alert('Esta recompensa não está mais disponível!');
          return;
        }

        // Mostrar modal de confirmação customizado
        vm.mostrarModalConfirmacao = true;
        vm.recompensaSelecionada = recompensa;
        vm.pontosAposResgate = vm.pontosUsuario - recompensa.pontos;
      };

      // Verificar se pode resgatar uma recompensa
      vm.podeResgatar = function(recompensa) {
        return vm.pontosUsuario >= recompensa.pontos && 
               recompensa.disponivel > 0 && 
               !vm.resgatando;
      };

      // Obter classe do botão baseada na disponibilidade
      vm.getBotaoClass = function(recompensa) {
        if (vm.resgatando) return 'btn-secondary';
        if (!vm.podeResgatar(recompensa)) return 'btn-secondary';
        return 'btn-primary';
      };

      // Obter texto do botão
      vm.getBotaoTexto = function(recompensa) {
        if (vm.resgatando) return 'Processando...';
        if (vm.pontosUsuario < recompensa.pontos) return 'Pontos insuficientes';
        if (recompensa.disponivel <= 0) return 'Indisponível';
        return 'Resgatar';
      };

      // Confirmar resgate
      vm.confirmarResgate = function() {
        if (!vm.recompensaSelecionada) return;
        
        vm.resgatando = true;
        vm.mostrarModalConfirmacao = false;

        RecompensaService.resgatarRecompensa(vm.recompensaSelecionada.id)
          .then(function(resgate) {
            // Mostrar notificação de sucesso
            vm.mostrarNotificacao = true;
            vm.tipoNotificacao = 'success';
            vm.mensagemNotificacao = 'Recompensa resgatada com sucesso!';
            
            // Recarregar pontos do usuário da API
            carregarPontosUsuario();
            
            // Atualizar disponibilidade da recompensa
            vm.recompensaSelecionada.disponivel--;
            
            // Recarregar dados
            carregarRecompensas();
            
            // Esconder notificação após 3 segundos
            $timeout(function() {
              vm.mostrarNotificacao = false;
            }, 3000);
          })
          .catch(function(error) {
            console.error('Erro ao resgatar recompensa:', error);
            vm.mostrarNotificacao = true;
            vm.tipoNotificacao = 'error';
            vm.mensagemNotificacao = 'Erro ao resgatar recompensa: ' + (error.message || 'Erro desconhecido');
            
            // Esconder notificação após 5 segundos
            $timeout(function() {
              vm.mostrarNotificacao = false;
            }, 5000);
          })
          .finally(function() {
            vm.resgatando = false;
            vm.recompensaSelecionada = null;
          });
      };

      // Cancelar resgate
      vm.cancelarResgate = function() {
        vm.mostrarModalConfirmacao = false;
        vm.recompensaSelecionada = null;
        vm.pontosAposResgate = 0;
      };

      // Fechar notificação
      vm.fecharNotificacao = function() {
        vm.mostrarNotificacao = false;
      };

      // Inicializar controller
      vm.init();
    });
})();