(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .service('AuthService', ['ApiClient', '$q', function(ApiClient, $q){
      var profileCache = null;
      var currentUser = null;

      function getProfile(){
        if(profileCache){ return angular.copy(profileCache); }
        try {
          // try to read from local session
          var sess = JSON.parse(localStorage.getItem('rf_session') || 'null');
          console.log('Carregando perfil da sessão:', sess);
          if(sess && sess.profile && sess.token){ 
            profileCache = sess.profile;
            currentUser = sess.user;
            console.log('Perfil carregado:', profileCache);
          }
        } catch(e){
          console.error('Erro ao carregar perfil:', e);
        }
        return angular.copy(profileCache || { nome:'', email:'', telefone:'', pontuacao: 0 });
      }

      function saveSession(user, profile) {
        var session = { user: user, profile: profile, token: user.token };
        localStorage.setItem('rf_session', JSON.stringify(session));
        profileCache = profile;
        currentUser = user;
        console.log('Sessão salva no localStorage:', session);
      }
      
      // Inicializar sessão ao carregar a aplicação
      function initializeSession() {
        try {
          var sess = JSON.parse(localStorage.getItem('rf_session') || 'null');
          if (sess && sess.user && sess.profile && sess.token) {
            profileCache = sess.profile;
            currentUser = sess.user;
            console.log('Sessão inicializada:', {
              user: currentUser,
              profile: profileCache,
              token: sess.token
            });
          }
        } catch(e) {
          console.error('Erro ao inicializar sessão:', e);
        }
      }
      
      // Inicializar imediatamente
      initializeSession();

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
          var self = this;
          return ApiClient.request('POST', '/auth/login', { email: email, senha: senha })
            .then(function(response) {
              console.log('Resposta do login:', response);
              if (response.success) {
                // Garantir que o token seja salvo
                var userData = response.data;
                if (!userData.token) {
                  console.warn('Token não encontrado na resposta do login');
                }
                saveSession(userData, userData);
                console.log('Sessão salva:', {
                  user: userData,
                  token: userData.token,
                  loggedIn: self.isLoggedIn()
                });
                return userData;
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
          var hasUser = !!currentUser;
          var hasToken = !!this.getToken();
          var hasSession = false;
          
          try {
            var session = JSON.parse(localStorage.getItem('rf_session') || 'null');
            hasSession = !!(session && session.user && session.token);
          } catch(e) {}
          
          console.log('Verificação de login:', {
            hasUser: hasUser,
            hasToken: hasToken,
            hasSession: hasSession,
            currentUser: currentUser,
            token: this.getToken()
          });
          
          return hasUser && hasToken && hasSession;
        },
        getToken: function() {
          try {
            var sess = JSON.parse(localStorage.getItem('rf_session') || 'null');
            return sess && sess.token ? sess.token : null;
          } catch(e) {
            return null;
          }
        },
        
        // Função para verificar e atualizar status de login
        refreshLoginStatus: function() {
          try {
            var sess = JSON.parse(localStorage.getItem('rf_session') || 'null');
            if (sess && sess.user && sess.profile && sess.token) {
              profileCache = sess.profile;
              currentUser = sess.user;
              console.log('Status de login atualizado:', {
                loggedIn: true,
                user: currentUser,
                token: sess.token
              });
              return true;
            } else {
              profileCache = null;
              currentUser = null;
              console.log('Usuário não logado');
              return false;
            }
          } catch(e) {
            console.error('Erro ao verificar status de login:', e);
            return false;
          }
        }
      };
    }]);
})();