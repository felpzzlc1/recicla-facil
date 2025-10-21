(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('HomeController', [function(){
      var vm = this;
      vm.showModal = false;

      // Função para mostrar dialog "Como Funciona"
      vm.mostrarComoFunciona = function() {
        vm.showModal = true;
      };

      // Função para fechar dialog
      vm.fecharModal = function() {
        vm.showModal = false;
      };
    }]);
})();