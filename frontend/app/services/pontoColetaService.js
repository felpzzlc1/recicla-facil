(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .factory('PontoColetaService', ['ApiClient', function(ApiClient){
      return {
        list: function(){ 
          return ApiClient.request('GET', '/pontos').then(function(response) {
            return response.success ? response.data : response;
          });
        }
      };
    }]);
})();