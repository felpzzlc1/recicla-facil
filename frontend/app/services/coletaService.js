(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .factory('ColetaService', ['ApiClient', function(ApiClient){
      return {
        list: function(){ 
          return ApiClient.request('GET', '/coletas').then(function(response) {
            return response.success ? response.data : response;
          });
        },
        create: function(data){ 
          return ApiClient.request('POST', '/coletas', data).then(function(response) {
            return response.success ? response.data : response;
          });
        },
        update: function(id, data){ 
          return ApiClient.request('PUT', '/coletas/' + id, data).then(function(response) {
            return response.success ? response.data : response;
          });
        },
        remove: function(id){ 
          return ApiClient.request('DELETE', '/coletas/' + id).then(function(response) {
            return response.success ? response.data : response;
          });
        }
      };
    }]);
})();