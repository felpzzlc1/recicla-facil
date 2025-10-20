(function(){
  'use strict';
  angular.module('reciclaFacilApp', ['ngRoute'])
    .constant('APP_CONFIG', window.APP_CONFIG || { API_BASE_URL: '', USE_MOCK: true })
    // Desabilitar cache de templates em desenvolvimento
    .run(['$templateCache', function($templateCache) {
      $templateCache.removeAll();
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
    }]);
})();