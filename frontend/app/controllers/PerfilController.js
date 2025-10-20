(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('PerfilController', ['AuthService', function(AuthService){
      var vm = this;
      vm.form = angular.copy(AuthService.getProfile());
      vm.salvar = function(){
        AuthService.updateProfile(angular.copy(vm.form));
        alert('Perfil atualizado!');
      };
    }]);
})();