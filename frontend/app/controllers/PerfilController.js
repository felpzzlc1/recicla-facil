(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('PerfilController', ['AuthService', '$location', function(AuthService, $location){
      var vm = this;
      vm.form = angular.copy(AuthService.getProfile());
      vm.error = '';
      vm.success = '';

      vm.salvar = function(){
        if (!vm.form.nome || !vm.form.telefone) {
          vm.error = 'Nome e telefone são obrigatórios';
          return;
        }

        var updateData = {
          nome: vm.form.nome,
          telefone: vm.form.telefone
        };

        if (vm.form.senha && vm.form.senha.length > 0) {
          if (vm.form.senha.length < 6) {
            vm.error = 'A senha deve ter pelo menos 6 caracteres';
            return;
          }
          updateData.senha = vm.form.senha;
        }

        AuthService.updateProfile(updateData)
          .then(function(){
            vm.success = 'Perfil atualizado com sucesso!';
            vm.error = '';
            vm.form.senha = '';
            vm.form.confirmarSenha = '';
          })
          .catch(function(err){
            vm.error = err && err.message ? err.message : 'Erro ao atualizar perfil';
            vm.success = '';
          });
      };

      vm.logout = function() {
        AuthService.logout();
        $location.path('/login');
      };
    }]);
})();