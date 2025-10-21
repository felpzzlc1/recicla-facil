(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('HomeController', ['ColetaService','DoacaoService','PontoColetaService', function(ColetaService, DoacaoService, PontoColetaService){
      var vm = this;
      vm.metrics = { coletasAbertas: 0, coletasConcluidas: 0, doacoesAtivas: 0, doacoesEntregues: 0, pontos: 0 };

      ColetaService.list().then(function(items){
        if (Array.isArray(items)) {
          vm.metrics.coletasAbertas = items.filter(function(i){ return i.status === 'ABERTA'; }).length;
          vm.metrics.coletasConcluidas = items.filter(function(i){ return i.status === 'CONCLUIDA'; }).length;
        } else {
          vm.metrics.coletasAbertas = 0;
          vm.metrics.coletasConcluidas = 0;
        }
      }).catch(function(err) {
        console.error('Erro ao carregar coletas:', err);
        vm.metrics.coletasAbertas = 0;
        vm.metrics.coletasConcluidas = 0;
      });

      DoacaoService.list().then(function(items){
        if (Array.isArray(items)) {
          vm.metrics.doacoesAtivas = items.filter(function(i){ return !i.entregue; }).length;
          vm.metrics.doacoesEntregues = items.filter(function(i){ return !!i.entregue; }).length;
        } else {
          vm.metrics.doacoesAtivas = 0;
          vm.metrics.doacoesEntregues = 0;
        }
      }).catch(function(err) {
        console.error('Erro ao carregar doações:', err);
        vm.metrics.doacoesAtivas = 0;
        vm.metrics.doacoesEntregues = 0;
      });

      PontoColetaService.list().then(function(items){
        if (Array.isArray(items)) {
          vm.metrics.pontos = items.length;
        } else {
          vm.metrics.pontos = 0;
        }
      }).catch(function(err) {
        console.error('Erro ao carregar pontos:', err);
        vm.metrics.pontos = 0;
      });
    }]);
})();