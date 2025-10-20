(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .factory('AuthService', ['ApiClient', function(ApiClient){
      var profileCache = null;

      function getProfile(){
        if(profileCache){ return angular.copy(profileCache); }
        try {
          // try to read from local session if mock
          var sess = JSON.parse(localStorage.getItem('rf_session') || 'null');
          if(sess && sess.profile){ profileCache = sess.profile; }
        } catch(e){}
        return angular.copy(profileCache || { nome:'', email:'', telefone:'' });
      }

      return {
        login: function(email, senha){
          return ApiClient.request('POST', '/auth/login', { email: email, senha: senha })
            .then(function(p){ profileCache = p; return p; });
        },
        getProfile: getProfile,
        updateProfile: function(p){
          return ApiClient.request('PUT', '/auth/profile', p)
            .then(function(np){ profileCache = np; return np; });
        }
      };
    }]);
})();