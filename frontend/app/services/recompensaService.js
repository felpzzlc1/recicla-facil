(function(){
  'use strict';
  angular.module('reciclaFacilApp')
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
