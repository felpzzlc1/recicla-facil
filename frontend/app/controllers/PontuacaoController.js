angular.module('reciclaFacilApp')
  .controller('PontuacaoController', function($scope) {
    var vm = this;
    
    // Dados mockados da pontuação
    vm.pontos = 8350;
    vm.nivel = 12;
    vm.nivelNome = 'Reciclador Expert';
    vm.pontosProximoNivel = 10000;
    vm.pontosRestantes = 1650;
    vm.aumentoSemanal = 250;
    
    vm.estatisticas = {
      descartes: 156,
      sequencia: 23,
      badges: 5
    };
    
    vm.conquistas = [
      { nome: 'Iniciante', icone: '🌱', desbloqueada: true },
      { nome: 'Reciclador', icone: '♻️', desbloqueada: true },
      { nome: 'Eco Warrior', icone: '☀️', desbloqueada: true },
      { nome: 'Guardião Verde', icone: '🌳', desbloqueada: false },
      { nome: 'Mestre Sustentável', icone: '🏆', desbloqueada: false }
    ];
  });
