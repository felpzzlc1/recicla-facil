(function() {
  'use strict';
  
  angular.module('reciclaFacilApp')
    .factory('CronogramaService', ['ApiClient', function(ApiClient) {
      
      return {
        // Listar todos os cronogramas (sempre usa mock)
        list: function() {
          return ApiClient.request('GET', '/cronograma', null, null, true).then(function(response) {
            return response.success ? response.data : [];
          });
        },
        
        // Buscar cronogramas próximos (simplificado - sem latitude/longitude)
        buscarProximos: function(latitude, longitude, raio) {
          // Por enquanto, retorna todos os cronogramas sem filtro de localização
          return ApiClient.request('GET', '/cronograma-simples').then(function(response) {
            return response.success ? response.data : response;
          });
        },
        
        // Buscar por material
        buscarPorMaterial: function(material) {
          return ApiClient.request('GET', '/cronograma/material/' + encodeURIComponent(material))
            .then(function(response) {
              return response.success ? response.data : response;
            });
        },
        
        // Buscar por dia da semana
        buscarPorDiaSemana: function(diaSemana) {
          return ApiClient.request('GET', '/cronograma/dia/' + encodeURIComponent(diaSemana))
            .then(function(response) {
              return response.success ? response.data : response;
            });
        },
        
        // Buscar por cidade
        buscarPorCidade: function(cidade) {
          return ApiClient.request('GET', '/cronograma/cidade/' + encodeURIComponent(cidade))
            .then(function(response) {
              return response.success ? response.data : response;
            });
        },
        
        // Buscar com filtros
        buscarComFiltros: function(filtros) {
          return ApiClient.request('GET', '/cronograma', filtros).then(function(response) {
            return response.success ? response.data : response;
          });
        },
        
        // Obter cronograma por ID
        get: function(id) {
          return ApiClient.request('GET', '/cronograma/' + id)
            .then(function(response) {
              return response.success ? response.data : response;
            });
        },
        
        // Criar novo cronograma (sempre usa mock)
        criar: function(dados) {
          return ApiClient.request('POST', '/cronograma', dados, null, true).then(function(response) {
            return response.success ? response.data : response;
          });
        },
        
        // Atualizar cronograma
        atualizar: function(id, dados) {
          return ApiClient.request('PUT', '/cronograma/' + id, dados).then(function(response) {
            return response.success ? response.data : response;
          });
        },
        
        // Remover cronograma
        remover: function(id) {
          return ApiClient.request('DELETE', '/cronograma/' + id).then(function(response) {
            return response.success ? response.data : response;
          });
        },
        
        // Obter materiais disponíveis
        getMateriaisDisponiveis: function() {
          return [
            'Papel',
            'Plástico', 
            'Metal',
            'Vidro',
            'Orgânico',
            'Eletrônicos',
            'Coleta Especial'
          ];
        },
        
        // Obter dias da semana
        getDiasSemana: function() {
          return [
            'Segunda-feira',
            'Terça-feira',
            'Quarta-feira',
            'Quinta-feira',
            'Sexta-feira',
            'Sábado',
            'Domingo'
          ];
        },
        
        // Formatar horário para exibição
        formatarHorario: function(horarioInicio, horarioFim) {
          try {
            // Se já são strings no formato HH:MM, usar diretamente
            if (typeof horarioInicio === 'string' && horarioInicio.includes(':')) {
              return horarioInicio + ' - ' + horarioFim;
            }
            
            // Se são timestamps, converter
            var inicio = new Date(horarioInicio);
            var fim = new Date(horarioFim);
            
            // Verificar se as datas são válidas
            if (isNaN(inicio.getTime()) || isNaN(fim.getTime())) {
              return 'Horário não disponível';
            }
            
            return inicio.toLocaleTimeString('pt-BR', { 
              hour: '2-digit', 
              minute: '2-digit' 
            }) + ' - ' + fim.toLocaleTimeString('pt-BR', { 
              hour: '2-digit', 
              minute: '2-digit' 
            });
          } catch (e) {
            console.error('Erro ao formatar horário:', e);
            return 'Horário não disponível';
          }
        },
        
        // Obter cor do material
        getCorMaterial: function(material) {
          var cores = {
            'Papel': 'blue',
            'Plástico': 'red',
            'Metal': 'yellow',
            'Vidro': 'green',
            'Orgânico': 'brown',
            'Eletrônicos': 'purple',
            'Coleta Especial': 'purple'
          };
          return cores[material] || 'gray';
        }
      };
    }]);
})();
