(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .factory('AuthService', ['ApiClient', '$q', function(ApiClient, $q){
      var profileCache = null;
      var currentUser = null;

      function getProfile(){
        if(profileCache){ return angular.copy(profileCache); }
        try {
          // try to read from local session
          var sess = JSON.parse(localStorage.getItem('rf_session') || 'null');
          if(sess && sess.profile && sess.token){ 
            profileCache = sess.profile;
            currentUser = sess.user;
          }
        } catch(e){}
        return angular.copy(profileCache || { nome:'', email:'', telefone:'', pontuacao: 0 });
      }

      function saveSession(user, profile) {
        var session = { user: user, profile: profile, token: user.token };
        localStorage.setItem('rf_session', JSON.stringify(session));
        profileCache = profile;
        currentUser = user;
      }

      function clearSession() {
        localStorage.removeItem('rf_session');
        profileCache = null;
        currentUser = null;
      }

      return {
        register: function(userData) {
          console.log('AuthService.register chamado com:', userData);
          return ApiClient.request('POST', '/auth/register', userData)
            .then(function(response) {
              console.log('Resposta do servidor:', response);
              if (response.success) {
                saveSession(response.data, response.data);
                return response.data;
              }
              throw new Error(response.message || 'Erro ao criar usuário');
            })
            .catch(function(error) {
              console.error('Erro no AuthService.register:', error);
              throw error;
            });
        },
        login: function(email, senha){
          return ApiClient.request('POST', '/auth/login', { email: email, senha: senha })
            .then(function(response) {
              if (response.success) {
                saveSession(response.data, response.data);
                return response.data;
              }
              throw new Error(response.message || 'Credenciais inválidas');
            });
        },
        getProfile: getProfile,
        getCurrentUser: function() {
          return currentUser;
        },
        updateProfile: function(p){
          return ApiClient.request('PUT', '/auth/profile', p)
            .then(function(response) {
              if (response.success) {
                saveSession(response.data, response.data);
                return response.data;
              }
              throw new Error(response.message || 'Erro ao atualizar perfil');
            });
        },
        logout: function() {
          var token = this.getToken();
          if (token) {
            ApiClient.request('POST', '/auth/logout', {}, token)
              .then(function() {
                clearSession();
              })
              .catch(function() {
                clearSession(); // Limpar mesmo se der erro
              });
          } else {
            clearSession();
          }
        },
        isLoggedIn: function() {
          return !!currentUser && !!this.getToken();
        },
        getToken: function() {
          try {
            var sess = JSON.parse(localStorage.getItem('rf_session') || 'null');
            return sess && sess.token ? sess.token : null;
          } catch(e) {
            return null;
          }
        }
      };
    }]);
})();