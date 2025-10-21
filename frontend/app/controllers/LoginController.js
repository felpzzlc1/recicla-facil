(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('LoginController', ['AuthService', '$location', function(AuthService, $location){
      var vm = this;
      vm.form = { nome: '', email: '', telefone: '', senha: '', confirmarSenha: '' };
      vm.error = '';
      vm.isLogin = true;

      vm.toggleMode = function() {
        vm.isLogin = !vm.isLogin;
        vm.error = '';
        vm.form = { nome: '', email: '', telefone: '', senha: '', confirmarSenha: '' };
      };

      vm.login = function(){
        if (!vm.form.email || !vm.form.senha) {
          vm.error = 'Email e senha são obrigatórios';
          return;
        }

        AuthService.login(vm.form.email, vm.form.senha)
          .then(function(){
            $location.path('/');
          })
          .catch(function(err){
            vm.error = err && err.message ? err.message : 'Falha ao autenticar';
          });
      };

      vm.register = function() {
        
        if (!vm.form.nome || !vm.form.email || !vm.form.telefone || !vm.form.senha) {
          vm.error = 'Todos os campos são obrigatórios';
          return;
        }

        if (vm.form.senha !== vm.form.confirmarSenha) {
          vm.error = 'As senhas não coincidem';
          return;
        }

        if (vm.form.senha.length < 6) {
          vm.error = 'A senha deve ter pelo menos 6 caracteres';
          return;
        }

        var userData = {
          nome: vm.form.nome,
          email: vm.form.email,
          telefone: vm.form.telefone,
          senha: vm.form.senha
        };

        AuthService.register(userData)
          .then(function(response){
            // Limpar o formulário
            vm.form = { nome: '', email: '', telefone: '', senha: '', confirmarSenha: '' };
            vm.error = '';
            // Redirecionar para home
            $location.path('/');
          })
          .catch(function(err){
            console.error('Erro no cadastro:', err);
            vm.error = err && err.message ? err.message : 'Falha ao criar usuário';
          });
      };
    }]);
})();