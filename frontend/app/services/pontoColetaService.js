(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .factory('PontoColetaService', ['ApiClient', function(ApiClient){
      return {
        // Lista todos os pontos (sem filtro de localização)
        list: function(){ 
          return ApiClient.request('GET', '/pontos').then(function(response) {
            return response.success ? response.data : response;
          });
        },
        
        // Busca pontos próximos a uma localização
        buscarProximos: function(latitude, longitude, raioKm, limite) {
          var params = {
            lat: latitude,
            lng: longitude
          };
          
          if (raioKm) params.raio = raioKm;
          if (limite) params.limite = limite;
          
          return ApiClient.request('GET', '/pontos/proximos', params).then(function(response) {
            return response.success ? response.data : response;
          });
        },
        
        // Cadastra novo ponto de coleta
        cadastrar: function(dadosPonto) {
          return ApiClient.request('POST', '/pontos', dadosPonto).then(function(response) {
            return response.success ? response.data : response;
          });
        }
      };
    }]);
})();