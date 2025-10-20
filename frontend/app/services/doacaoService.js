(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .factory('DoacaoService', ['ApiClient', function(ApiClient){
      return {
        list: function(){ return ApiClient.request('GET', '/doacoes'); },
        create: function(data){ return ApiClient.request('POST', '/doacoes', data); },
        update: function(id, data){ return ApiClient.request('PUT', '/doacoes/' + id, data); },
        remove: function(id){ return ApiClient.request('DELETE', '/doacoes/' + id); }
      };
    }]);
})();