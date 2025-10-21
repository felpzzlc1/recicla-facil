(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .service('ModalService', ['$rootScope', function($rootScope){
      var modalState = {
        showModal: false,
        isLogin: true,
        form: { nome: '', email: '', telefone: '', senha: '', confirmarSenha: '' },
        error: '',
        success: ''
      };

      return {
        getState: function() {
          return modalState;
        },
        
        openModal: function(mode) {
          modalState.isLogin = mode === 'login';
          modalState.showModal = true;
          modalState.error = '';
          modalState.success = '';
          modalState.form = { nome: '', email: '', telefone: '', senha: '', confirmarSenha: '' };
          $rootScope.$broadcast('modalStateChanged');
        },
        
        closeModal: function() {
          modalState.showModal = false;
          modalState.error = '';
          modalState.success = '';
          modalState.form = { nome: '', email: '', telefone: '', senha: '', confirmarSenha: '' };
          $rootScope.$broadcast('modalStateChanged');
        },
        
        toggleMode: function() {
          modalState.isLogin = !modalState.isLogin;
          modalState.error = '';
          modalState.success = '';
          modalState.form = { nome: '', email: '', telefone: '', senha: '', confirmarSenha: '' };
          $rootScope.$broadcast('modalStateChanged');
        },
        
        setError: function(error) {
          modalState.error = error;
          modalState.success = '';
          $rootScope.$broadcast('modalStateChanged');
        },
        
        setSuccess: function(success) {
          modalState.success = success;
          modalState.error = '';
          $rootScope.$broadcast('modalStateChanged');
        },
        
        setForm: function(form) {
          modalState.form = form;
          $rootScope.$broadcast('modalStateChanged');
        }
      };
    }]);
})();
