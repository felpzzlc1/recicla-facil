(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('SolicitarColetaController', ['ColetaService', function(ColetaService){
      var vm = this;
      vm.form = { material:'', quantidade:null, endereco:'', data:null, obs:'' };
      vm.items = [];

      function load(){ 
        ColetaService.list().then(function(items){ vm.items = items; });
      }
      load();

      vm.salvar = function(){
        ColetaService.create(angular.copy(vm.form)).then(function(){
          vm.form = { material:'', quantidade:null, endereco:'', data:null, obs:'' };
          load();
        });
      };

      vm.marcarConcluida = function(item){
        ColetaService.update(item.id, { status: 'CONCLUIDA' }).then(load);
      };

      vm.remover = function(item){
        ColetaService.remove(item.id).then(load);
      };
    }]);
})();