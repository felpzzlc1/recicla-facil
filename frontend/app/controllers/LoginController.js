(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('LoginController', ['AuthService', '$location', function(AuthService, $location){
      var vm = this;
      vm.form = { email: '', senha: '' };
      vm.error = '';

      vm.login = function(){
        AuthService.login(vm.form.email, vm.form.senha)
          .then(function(){
            $location.path('/');
          })
          .catch(function(err){
            vm.error = err && err.message ? err.message : 'Falha ao autenticar';
          });
      };
    }]);
})();