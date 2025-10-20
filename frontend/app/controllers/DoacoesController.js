(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('DoacoesController', ['DoacaoService', function(DoacaoService){
      var vm = this;
      vm.form = { material:'', qtd:null, contato:'' };
      vm.items = [];

      function load(){
        DoacaoService.list().then(function(items){ 
          console.log('Doações carregadas:', items);
          vm.items = Array.isArray(items) ? items : [];
        }).catch(function(err) {
          console.error('Erro ao carregar doações:', err);
          vm.items = [];
        });
      }
      load();

      vm.adicionar = function(){
        DoacaoService.create(angular.copy(vm.form)).then(function(){
          vm.form = { material:'', qtd:null, contato:'' };
          load();
        });
      };

      vm.marcarEntregue = function(d){
        DoacaoService.update(d.id, { entregue: true }).then(load);
      };

      vm.remover = function(d){
        DoacaoService.remove(d.id).then(load);
      };
    }]);
})();