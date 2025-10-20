(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('PontosColetaController', ['PontoColetaService', function(PontoColetaService){
      var vm = this;
      vm.pontos = [];
      PontoColetaService.list().then(function(items){ 
        console.log('Pontos carregados:', items);
        vm.pontos = Array.isArray(items) ? items : [];
      }).catch(function(err) {
        console.error('Erro ao carregar pontos:', err);
        vm.pontos = [];
      });
    }]);
})();