(function(){
  'use strict';
  angular.module('reciclaFacilApp', ['ngRoute'])
    .constant('APP_CONFIG', window.APP_CONFIG || { API_BASE_URL: '', USE_MOCK: false })
    // Desabilitar cache de templates em desenvolvimento
    .run(['$templateCache', 'AuthService', function($templateCache, AuthService) {
      $templateCache.removeAll();
      
      // Inicializar status de autenticação
      console.log('Inicializando aplicação...');
      AuthService.refreshLoginStatus();
    }])
    // Configurar para não usar cache de templates
    .config(['$provide', function($provide) {
      $provide.decorator('$templateCache', ['$delegate', function($delegate) {
        var originalGet = $delegate.get;
        $delegate.get = function(key) {
          // Forçar reload de templates em desenvolvimento
          if (window.APP_CONFIG && window.APP_CONFIG.USE_MOCK === false) {
            $delegate.remove(key);
          }
          return originalGet.call(this, key);
        };
        return $delegate;
      }]);
    }])
    // Registrar serviços
    .service('pontuacaoService', function($http, ApiClient) {
      var service = this;
      var baseUrl = '/pontuacao';

      // Obter estatísticas do usuário
      service.obterEstatisticas = function() {
        return ApiClient.request('GET', baseUrl + '/estatisticas');
      };

      // Adicionar pontos
      service.adicionarPontos = function(pontos, motivo) {
        return ApiClient.request('POST', baseUrl + '/adicionar', {
          pontos: pontos,
          motivo: motivo || 'descarte'
        });
      };

      // Obter ranking
      service.obterRanking = function(limite) {
        return ApiClient.request('GET', baseUrl + '/ranking', { limite: limite || 10 });
      };

      // Obter conquistas
      service.obterConquistas = function() {
        return ApiClient.request('GET', baseUrl + '/conquistas');
      };

      // Obter estatísticas gerais
      service.obterEstatisticasGerais = function() {
        return ApiClient.request('GET', baseUrl + '/estatisticas-gerais');
      };

      // Simular descarte
      service.simularDescarte = function(material, peso) {
        return ApiClient.request('POST', baseUrl + '/simular-descarte', {
          material: material,
          peso: peso
        });
      };

      // Registrar descarte
      service.registrarDescarte = function(material, peso) {
        return ApiClient.request('POST', baseUrl + '/registrar-descarte', {
          material: material,
          peso: peso
        });
      };

      // Resetar pontos semanais (admin)
      service.resetarPontosSemanais = function() {
        return ApiClient.request('POST', baseUrl + '/resetar-semanais');
      };

      // Calcular pontos por material
      service.calcularPontosPorMaterial = function(material, peso) {
        var pontosPorKg = {
          'papel': 10,
          'plastico': 15,
          'vidro': 20,
          'metal': 25,
          'organico': 5
        };
        
        return Math.round(peso * (pontosPorKg[material] || 10));
      };

      // Obter nome do material em português
      service.obterNomeMaterial = function(material) {
        var nomes = {
          'papel': 'Papel',
          'plastico': 'Plástico',
          'vidro': 'Vidro',
          'metal': 'Metal',
          'organico': 'Orgânico'
        };
        
        return nomes[material] || material;
      };

      // Obter ícone do material
      service.obterIconeMaterial = function(material) {
        var icones = {
          'papel': '📄',
          'plastico': '♻️',
          'vidro': '🍶',
          'metal': '🔧',
          'organico': '🍃'
        };
        
        return icones[material] || '♻️';
      };

      // Formatar número com separadores
      service.formatarNumero = function(numero) {
        if (numero === undefined || numero === null || isNaN(numero)) {
          return '0';
        }
        return numero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      };

      // Calcular progresso da barra
      service.calcularProgressoBarra = function(pontosAtuais, pontosProximoNivel) {
        return Math.min(100, Math.max(0, (pontosAtuais / pontosProximoNivel) * 100));
      };

      // Obter cor baseada no nível
      service.obterCorNivel = function(nivel) {
        if (nivel <= 2) return '#4CAF50'; // Verde
        if (nivel <= 4) return '#2196F3'; // Azul
        if (nivel <= 6) return '#FF9800'; // Laranja
        if (nivel <= 8) return '#9C27B0'; // Roxo
        return '#F44336'; // Vermelho
      };

      // Obter emoji do nível
      service.obterEmojiNivel = function(nivel) {
        if (nivel <= 2) return '🌱';
        if (nivel <= 4) return '♻️';
        if (nivel <= 6) return '🏆';
        if (nivel <= 8) return '👑';
        return '🌟';
      };
    })
    // Serviço de recompensas
    .service('RecompensaService', ['ApiClient', '$q', function(ApiClient, $q){
      
      return {
        // Obter todas as recompensas disponíveis
        obterRecompensas: function() {
          return ApiClient.request('GET', '/recompensas')
            .then(function(response) {
              if (response.success) {
                return response.data;
              }
              throw new Error(response.message || 'Erro ao buscar recompensas');
            })
            .catch(function(error) {
              console.error('Erro no RecompensaService.obterRecompensas:', error);
              throw error;
            });
        },

        // Obter recompensa por ID
        obterRecompensa: function(id) {
          return ApiClient.request('GET', '/recompensas/' + id)
            .then(function(response) {
              if (response.success) {
                return response.data;
              }
              throw new Error(response.message || 'Erro ao buscar recompensa');
            })
            .catch(function(error) {
              console.error('Erro no RecompensaService.obterRecompensa:', error);
              throw error;
            });
        },

        // Resgatar uma recompensa
        resgatarRecompensa: function(recompensaId) {
          return ApiClient.request('POST', '/recompensas/resgatar', {
            recompensa_id: recompensaId
          })
            .then(function(response) {
              if (response.success) {
                return response.data;
              }
              throw new Error(response.message || 'Erro ao resgatar recompensa');
            })
            .catch(function(error) {
              console.error('Erro no RecompensaService.resgatarRecompensa:', error);
              throw error;
            });
        },

        // Obter resgates do usuário
        obterMeusResgates: function() {
          return ApiClient.request('GET', '/recompensas/meus-resgates')
            .then(function(response) {
              if (response.success) {
                return response.data;
              }
              throw new Error(response.message || 'Erro ao buscar resgates');
            })
            .catch(function(error) {
              console.error('Erro no RecompensaService.obterMeusResgates:', error);
              throw error;
            });
        }
      };
    }]);
})();