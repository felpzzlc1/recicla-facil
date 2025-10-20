(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('PontosColetaController', ['PontoColetaService', function(PontoColetaService){
      var vm = this;
      vm.pontos = [];
      PontoColetaService.list().then(function(items){ vm.pontos = items; });
    }]);
})();