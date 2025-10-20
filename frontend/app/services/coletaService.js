(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .factory('ColetaService', ['ApiClient', function(ApiClient){
      return {
        list: function(){ return ApiClient.request('GET', '/coletas'); },
        create: function(data){ return ApiClient.request('POST', '/coletas', data); },
        update: function(id, data){ return ApiClient.request('PUT', '/coletas/' + id, data); },
        remove: function(id){ return ApiClient.request('DELETE', '/coletas/' + id); }
      };
    }]);
})();