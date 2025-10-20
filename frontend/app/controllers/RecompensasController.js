angular.module('reciclaFacilApp')
  .controller('RecompensasController', function($scope) {
    var vm = this;
    
    // Dados mockados das recompensas
    vm.recompensas = [
      {
        titulo: 'Vale Compras R$ 50',
        icone: '🛍️',
        categoria: 'Compras',
        categoriaIcone: '✓',
        pontos: 5000,
        disponivel: 15
      },
      {
        titulo: 'Café Grátis',
        icone: '☕',
        categoria: 'Gastronomia',
        categoriaIcone: '☕',
        pontos: 500,
        disponivel: 50
      },
      {
        titulo: 'Ingresso Cinema',
        icone: '🎬',
        categoria: 'Entretenimento',
        categoriaIcone: '🎬',
        pontos: 3000,
        disponivel: 10
      },
      {
        titulo: 'Kit Sustentável',
        icone: '🌱',
        categoria: 'Eco',
        categoriaIcone: '🎁',
        pontos: 2000,
        disponivel: 25
      },
      {
        titulo: 'Vale Compras R$ 100',
        icone: '🛒',
        categoria: 'Compras',
        categoriaIcone: '✓',
        pontos: 10000,
        disponivel: 8
      },
      {
        titulo: 'Experiência Eco-Turismo',
        icone: '🏔️',
        categoria: 'Turismo',
        categoriaIcone: '🧭',
        pontos: 15000,
        disponivel: 5
      }
    ];
  });
