angular.module('reciclaFacilApp')
  .controller('CronogramaController', function($scope) {
    var vm = this;
    
    // Dados mockados do cronograma
    vm.coletas = [
      {
        dia: '21',
        mes: 'Out',
        diaSemana: 'Segunda-feira',
        material: 'Papel e Papelão',
        cor: 'blue',
        horario: '7h - 12h',
        localizacao: 'Centro, Jardins'
      },
      {
        dia: '23',
        mes: 'Out',
        diaSemana: 'Quarta-feira',
        material: 'Plástico e Metal',
        cor: 'red',
        horario: '8h - 13h',
        localizacao: 'Zona Norte, Vila Nova'
      },
      {
        dia: '25',
        mes: 'Out',
        diaSemana: 'Sexta-feira',
        material: 'Vidro',
        cor: 'green',
        horario: '7h - 12h',
        localizacao: 'Zona Sul, Centro'
      },
      {
        dia: '26',
        mes: 'Out',
        diaSemana: 'Sábado',
        material: 'Coleta Especial',
        cor: 'purple',
        horario: '8h - 11h',
        localizacao: 'Todos os bairros'
      }
    ];
  });
