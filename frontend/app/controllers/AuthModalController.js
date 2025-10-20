(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('AuthModalController', ['AuthService', 'ModalService', '$location', '$scope', function(AuthService, ModalService, $location, $scope){
      var vm = this;
      
      // Usar o estado do serviço
      vm.modalState = ModalService.getState();

      // Função para abrir o modal
      vm.openModal = function(mode) {
        console.log('AuthModalController: Abrindo modal:', mode);
        ModalService.openModal(mode);
      };

      // Função para fechar o modal
      vm.closeModal = function() {
        ModalService.closeModal();
      };

      // Função para alternar entre login e cadastro
      vm.toggleMode = function() {
        ModalService.toggleMode();
      };

      // Função de login
      vm.login = function() {
        if (!vm.modalState.form.email || !vm.modalState.form.senha) {
          ModalService.setError('Email e senha são obrigatórios');
          return;
        }

        AuthService.login(vm.modalState.form.email, vm.modalState.form.senha)
          .then(function(response) {
            console.log('Login realizado com sucesso:', response);
            ModalService.closeModal();
            $location.path('/');
            $scope.$apply();
          })
          .catch(function(err) {
            console.error('Erro no login:', err);
            ModalService.setError(err && err.message ? err.message : 'Falha ao autenticar');
          });
      };

      // Função de cadastro
      vm.register = function() {
        console.log('Tentando cadastrar usuário...', vm.modalState.form);
        
        if (!vm.modalState.form.nome || !vm.modalState.form.email || !vm.modalState.form.telefone || !vm.modalState.form.senha) {
          ModalService.setError('Todos os campos são obrigatórios');
          return;
        }

        if (vm.modalState.form.senha !== vm.modalState.form.confirmarSenha) {
          ModalService.setError('As senhas não coincidem');
          return;
        }

        if (vm.modalState.form.senha.length < 6) {
          ModalService.setError('A senha deve ter pelo menos 6 caracteres');
          return;
        }

        var userData = {
          nome: vm.modalState.form.nome,
          email: vm.modalState.form.email,
          telefone: vm.modalState.form.telefone,
          senha: vm.modalState.form.senha
        };

        console.log('Dados do usuário:', userData);

        AuthService.register(userData)
          .then(function(response) {
            console.log('Cadastro realizado com sucesso:', response);
            ModalService.setSuccess('Cadastro realizado com sucesso! Agora você pode fazer login.');
            // Após 2 segundos, alternar para login
            setTimeout(function() {
              ModalService.toggleMode();
              ModalService.setSuccess('');
            }, 2000);
          })
          .catch(function(err) {
            console.error('Erro no cadastro:', err);
            ModalService.setError(err && err.message ? err.message : 'Falha ao criar usuário');
          });
      };

      // Função de logout
      vm.logout = function() {
        AuthService.logout();
        ModalService.closeModal();
        $location.path('/');
        $scope.$apply();
      };

      // Verificar se usuário está logado
      vm.isLoggedIn = function() {
        return AuthService.isLoggedIn();
      };

      // Obter dados do usuário logado
      vm.getUser = function() {
        return AuthService.getProfile();
      };

      // Expor funções globalmente para o header
      $scope.openLogin = function() { vm.openModal('login'); };
      $scope.openRegister = function() { vm.openModal('register'); };
      $scope.logout = vm.logout;
      $scope.isLoggedIn = vm.isLoggedIn;
      $scope.getUser = vm.getUser;
    }]);
})();
