(function () {
  'use strict';

  angular.module('reciclaFacilApp').config(['$routeProvider', '$locationProvider',
    function ($routeProvider, $locationProvider) {
      $routeProvider
        .when('/', { templateUrl: 'app/templates/views/home.html', controller: 'HomeController', controllerAs: 'vm' })
        .when('/solicitar-coleta', { templateUrl: 'app/templates/views/solicitar-coleta.html', controller: 'SolicitarColetaController', controllerAs: 'vm' })
        .when('/doacoes', { templateUrl: 'app/templates/views/doacoes.html', controller: 'DoacoesController', controllerAs: 'vm' })
        .when('/pontos', { templateUrl: 'app/templates/views/pontos-coleta.html', controller: 'PontosColetaController', controllerAs: 'vm' })
        .when('/pontos-coleta', { templateUrl: 'app/templates/views/pontos-coleta.html', controller: 'PontosColetaController', controllerAs: 'vm' })
        .when('/cronograma', { templateUrl: 'app/templates/views/cronograma.html', controller: 'CronogramaController', controllerAs: 'vm' })
        .when('/pontuacao', { templateUrl: 'app/templates/views/pontuacao.html', controller: 'PontuacaoController', controllerAs: 'pontuacaoCtrl' })
        .when('/recompensas', { templateUrl: 'app/templates/views/recompensas.html', controller: 'RecompensasController', controllerAs: 'vm' })
        .when('/perfil', { templateUrl: 'app/templates/views/perfil.html', controller: 'PerfilController', controllerAs: 'vm' })
        .otherwise({ redirectTo: '/' });

      // $locationProvider.html5Mode(true); // habilite se quiser URLs limpas
    }
  ]);
})();