(function(){
  'use strict';

  angular.module('reciclaFacilApp')
    .factory('ApiClient', ['$http', '$q', 'APP_CONFIG', function($http, $q, APP_CONFIG){
      var USE_MOCK = !!APP_CONFIG.USE_MOCK;

      // LocalStorage helpers
      function lsGet(key, def){ try { return JSON.parse(localStorage.getItem(key)) || def; } catch(e){ return def; } }
      function lsSet(key, val){ localStorage.setItem(key, JSON.stringify(val)); }
      function uid(){ return Math.random().toString(36).slice(2) + Date.now().toString(36); }

      // Initialize mock "tables" if empty
      (function bootstrap(){
        if(!lsGet('rf_users')){
          lsSet('rf_users', [{ id: 'u1', email: 'demo@recicla.com', senha: '123', nome: 'Usuário Demo', telefone: '' }]);
        }
        if(!lsGet('rf_coletas')){ lsSet('rf_coletas', []); }
        if(!lsGet('rf_doacoes')){ lsSet('rf_doacoes', []); }
        if(!lsGet('rf_pontos')){
          lsSet('rf_pontos', [
            { id: uid(), nome:'Eco Ponto Centro', tipo:'Público', endereco:'Praça Central, 100 - Centro' },
            { id: uid(), nome:'Cooperativa Verde', tipo:'Cooperativa', endereco:'Av. Brasil, 2450 - Industrial' },
            { id: uid(), nome:'Mercado Bom Preço', tipo:'Privado', endereco:'Rua das Flores, 77 - Jardim' }
          ]);
        }
        if(!lsGet('rf_session')){ lsSet('rf_session', { logged: false, profile: null }); }
      })();

      // Real HTTP client
      function httpRequest(method, url, data, token){
        var cfg = { 
          method: method, 
          url: APP_CONFIG.API_BASE_URL + url, 
          data: data,
          headers: {}
        };
        
        // Adicionar token de autenticação se disponível
        if (token) {
          cfg.headers['Authorization'] = 'Bearer ' + token;
        } else {
          // Tentar obter token da sessão
          try {
            var session = JSON.parse(localStorage.getItem('rf_session') || 'null');
            if (session && session.token) {
              cfg.headers['Authorization'] = 'Bearer ' + session.token;
            }
          } catch(e) {}
        }
        
        return $http(cfg).then(function(res){ return res.data; });
      }

      // Mock client backed by localStorage
      function mockRequest(method, url, data){
        var defer = $q.defer();
        setTimeout(function(){
          try {
            var result = handleMock(method, url, data);
            defer.resolve(result);
          } catch(err){
            defer.reject(err);
          }
        }, 200);
        return defer.promise;
      }

      function handleMock(method, url, data){
        // AUTH
        if(url === '/auth/register' && method === 'POST'){
          var users = lsGet('rf_users', []);
          var existing = users.find(function(x){ return x.email === data.email; });
          if(existing) throw { message: 'Email já cadastrado' };
          var newUser = { id: uid(), email: data.email, senha: data.senha, nome: data.nome, telefone: data.telefone, pontuacao: 0 };
          users.push(newUser);
          lsSet('rf_users', users);
          var session = { logged: true, user: newUser, profile: { id:newUser.id, email:newUser.email, nome:newUser.nome, telefone:newUser.telefone, pontuacao: newUser.pontuacao } };
          lsSet('rf_session', session);
          return { success: true, data: session.profile };
        }
        if(url === '/auth/login' && method === 'POST'){
          var users = lsGet('rf_users', []);
          var u = users.find(function(x){ return x.email === data.email && x.senha === data.senha; });
          if(!u) throw { message: 'Credenciais inválidas' };
          var session = { logged: true, user: u, profile: { id:u.id, email:u.email, nome:u.nome, telefone:u.telefone, pontuacao: u.pontuacao } };
          lsSet('rf_session', session);
          return { success: true, data: session.profile };
        }
        if(url === '/auth/profile' && method === 'GET'){
          var s = lsGet('rf_session', { logged:false });
          if(!s.logged) throw { message: 'Não autenticado' };
          return { success: true, data: s.profile };
        }
        if(url === '/auth/profile' && method === 'PUT'){
          var sess = lsGet('rf_session', { logged:false });
          if(!sess.logged) throw { message: 'Não autenticado' };
          sess.profile = angular.extend({}, sess.profile, data);
          // persist also in users
          var users = lsGet('rf_users', []);
          var idx = users.findIndex(function(x){ return x.id === sess.profile.id; });
          if(idx >= 0){
            users[idx] = angular.extend({}, users[idx], sess.profile);
            lsSet('rf_users', users);
          }
          lsSet('rf_session', sess);
          return { success: true, data: sess.profile };
        }

        // COLETAS
        if(url === '/coletas' && method === 'GET'){
          return { success: true, data: lsGet('rf_coletas', []) };
        }
        if(url === '/coletas' && method === 'POST'){
          var list = lsGet('rf_coletas', []);
          var sess = lsGet('rf_session', { logged: false });
          var item = angular.extend({ id: uid(), user_id: sess.user ? sess.user.id : null, status: 'ABERTA', criadoEm: new Date().toISOString() }, data);
          list.push(item); lsSet('rf_coletas', list); 
          return { success: true, data: item };
        }
        if(url.startsWith('/coletas/') && method === 'PUT'){
          var id = url.split('/')[2];
          var items = lsGet('rf_coletas', []);
          var i = items.findIndex(function(x){ return x.id === id; });
          if(i >= 0){ items[i] = angular.extend({}, items[i], data); lsSet('rf_coletas', items); return { success: true, data: items[i] }; }
          throw { message: 'Coleta não encontrada' };
        }
        if(url.startsWith('/coletas/') && method === 'DELETE'){
          var idd = url.split('/')[2];
          var arr = lsGet('rf_coletas', []);
          arr = arr.filter(function(x){ return x.id !== idd; });
          lsSet('rf_coletas', arr); return { success: true, data: { ok:true } };
        }

        // DOACOES
        if(url === '/doacoes' && method === 'GET'){
          return { success: true, data: lsGet('rf_doacoes', []) };
        }
        if(url === '/doacoes' && method === 'POST'){
          var dl = lsGet('rf_doacoes', []);
          var sess = lsGet('rf_session', { logged: false });
          var d = angular.extend({ id: uid(), user_id: sess.user ? sess.user.id : null, criadoEm: new Date().toISOString(), entregue:false }, data);
          dl.push(d); lsSet('rf_doacoes', dl); 
          return { success: true, data: d };
        }
        if(url.startsWith('/doacoes/') && method === 'PUT'){
          var did = url.split('/')[2];
          var ds = lsGet('rf_doacoes', []);
          var di = ds.findIndex(function(x){ return x.id === did; });
          if(di >= 0){ ds[di] = angular.extend({}, ds[di], data); lsSet('rf_doacoes', ds); return { success: true, data: ds[di] }; }
          throw { message: 'Doação não encontrada' };
        }
        if(url.startsWith('/doacoes/') && method === 'DELETE'){
          var rid = url.split('/')[2];
          var da = lsGet('rf_doacoes', []);
          da = da.filter(function(x){ return x.id != rid; });
          lsSet('rf_doacoes', da); return { success: true, data: { ok:true } };
        }

        // PONTOS
        if(url === '/pontos' && method === 'GET'){
          return { success: true, data: lsGet('rf_pontos', []) };
        }

        throw { message: 'Rota mock não mapeada: ' + method + ' ' + url };
      }

      return {
        request: function(method, url, data, token){
          return USE_MOCK ? mockRequest(method, url, data) : httpRequest(method, url, data, token);
        }
      };
    }]);
})();