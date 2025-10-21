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
          console.log('Service buscarProximos chamado com:', {latitude, longitude, raioKm, limite});
          
          var params = {
            lat: latitude,
            lng: longitude
          };
          
          if (raioKm) params.raio = raioKm;
          if (limite) params.limite = limite;
          
          console.log('Parâmetros enviados:', params);
          
          return ApiClient.request('GET', '/pontos/proximos', params).then(function(response) {
            console.log('Resposta da API:', response);
            return response.success ? response.data : response;
          });
        },
        
        // Cadastra novo ponto de coleta
        cadastrar: function(dadosPonto) {
          console.log('Service cadastrar chamado com:', dadosPonto);
          
          return ApiClient.request('POST', '/pontos', dadosPonto).then(function(response) {
            console.log('Resposta do cadastro:', response);
            return response.success ? response.data : response;
          });
        }
      };
    }]);
})();