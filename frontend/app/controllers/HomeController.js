(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('HomeController', ['ColetaService','DoacaoService','PontoColetaService', function(ColetaService, DoacaoService, PontoColetaService){
      var vm = this;
      vm.metrics = { coletasAbertas: 0, coletasConcluidas: 0, doacoesAtivas: 0, doacoesEntregues: 0, pontos: 0 };

      ColetaService.list().then(function(items){
        vm.metrics.coletasAbertas = items.filter(function(i){ return i.status === 'ABERTA'; }).length;
        vm.metrics.coletasConcluidas = items.filter(function(i){ return i.status === 'CONCLUIDA'; }).length;
      });

      DoacaoService.list().then(function(items){
        vm.metrics.doacoesAtivas = items.filter(function(i){ return !i.entregue; }).length;
        vm.metrics.doacoesEntregues = items.filter(function(i){ return !!i.entregue; }).length;
      });

      PontoColetaService.list().then(function(items){
        vm.metrics.pontos = items.length;
      });
    }]);
})();