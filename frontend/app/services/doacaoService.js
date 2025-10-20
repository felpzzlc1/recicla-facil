(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .factory('DoacaoService', ['ApiClient', function(ApiClient){
      return {
        list: function(){ 
          return ApiClient.request('GET', '/doacoes').then(function(response) {
            return response.success ? response.data : response;
          });
        },
        create: function(data){ 
          return ApiClient.request('POST', '/doacoes', data).then(function(response) {
            return response.success ? response.data : response;
          });
        },
        update: function(id, data){ 
          return ApiClient.request('PUT', '/doacoes/' + id, data).then(function(response) {
            return response.success ? response.data : response;
          });
        },
        remove: function(id){ 
          return ApiClient.request('DELETE', '/doacoes/' + id).then(function(response) {
            return response.success ? response.data : response;
          });
        }
      };
    }]);
})();