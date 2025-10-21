(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('PontosColetaController', ['PontoColetaService', '$scope', function(PontoColetaService, $scope){
      var vm = this;
      vm.pontos = [];
      vm.loading = false;
      vm.userLocation = null;
      vm.error = null;
      vm.selectedPoint = null;
      vm.atribuicao = null;
      vm.searchTimeout = null;
      vm.cidade = null;
      
      // Modal de cadastro
      vm.showModal = false;
      vm.formData = {
        nome: '',
        tipo: 'Cooperativa',
        endereco: '',
        latitude: null,
        longitude: null,
        telefone: '',
        horario: '',
        materiais: {}
      };
      vm.materiaisDisponiveis = ['Papel', 'Plástico', 'Metal', 'Vidro', 'Orgânico', 'Eletrônicos'];
      vm.salvando = false;
      vm.localizacaoObtida = false;
      vm.mensagem = '';
      vm.mensagemTipo = '';
      
      // Função para obter localização do usuário
      vm.getUserLocation = function() {
        vm.loading = true;
        vm.error = null;
        
        if (!navigator.geolocation) {
          vm.error = 'Geolocalização não é suportada por este navegador.';
          vm.loading = false;
          return;
        }
        
        navigator.geolocation.getCurrentPosition(
          function(position) {
            vm.userLocation = {
              latitude: position.coords.latitude,
              longitude: position.coords.longitude
            };
            console.log('Localização obtida:', vm.userLocation);
            vm.loadNearbyPoints();
          },
          function(error) {
            console.error('Erro ao obter localização:', error);
            vm.error = 'Não foi possível obter sua localização. Verifique as permissões do navegador.';
            vm.loading = false;
            
            // Fallback: usar coordenadas de São Paulo para teste
            vm.userLocation = {
              latitude: -23.5505,
              longitude: -46.6333
            };
            console.log('Usando coordenadas de fallback:', vm.userLocation);
            vm.loadNearbyPoints();
          },
          {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000
          }
        );
      };
      
      // Função para carregar pontos próximos
      vm.loadNearbyPoints = function() {
        vm.loading = true;
        
        if (vm.userLocation) {
          // Busca pontos próximos usando a nova API
          vm.searchNearbyPoints();
        } else {
          // Carrega todos os pontos se não há localização
          PontoColetaService.list().then(function(response) {
            console.log('Pontos carregados:', response);
            vm.pontos = response.pontos || [];
            vm.loading = false;
          }).catch(function(err) {
            console.error('Erro ao carregar pontos:', err);
            vm.error = 'Erro ao carregar pontos de coleta.';
            vm.loading = false;
          });
        }
      };
      
      // Função para buscar pontos próximos usando a API do backend
      vm.searchNearbyPoints = function() {
        // Debounce: cancela busca anterior se ainda estiver pendente
        if (vm.searchTimeout) {
          clearTimeout(vm.searchTimeout);
        }
        
        vm.searchTimeout = setTimeout(function() {
          console.log('Buscando pontos próximos para:', vm.userLocation);
          
          if (!vm.userLocation || !vm.userLocation.latitude || !vm.userLocation.longitude) {
            console.error('Localização não disponível:', vm.userLocation);
            vm.error = 'Localização não disponível.';
            vm.loading = false;
            return;
          }
          
          // Busca pontos próximos
          PontoColetaService.buscarProximos(
            vm.userLocation.latitude, 
            vm.userLocation.longitude, 
            50, // raio de 50km
            20  // limite de 20 pontos
          ).then(function(response) {
            console.log('Pontos próximos encontrados:', response);
            
            if (response.pontos && response.pontos.length > 0) {
              vm.pontos = response.pontos;
              vm.cidade = response.cidade;
              vm.atribuicao = response.atribuicao;
            } else {
              vm.pontos = [];
            }
            vm.loading = false;
          }).catch(function(err) {
            console.error('Erro ao buscar pontos próximos:', err);
            vm.error = 'Erro ao buscar pontos de coleta.';
            vm.loading = false;
          });
        }, 500); // Debounce de 500ms
      };
      
      
      // Função para selecionar um ponto
      vm.selectPoint = function(ponto) {
        vm.selectedPoint = ponto;
        console.log('Ponto selecionado:', ponto);
        
        // Aqui você pode implementar ações como:
        // - Abrir mapa com direções
        // - Mostrar detalhes adicionais
        // - Salvar como favorito
        // - Solicitar coleta
      };
      
      // Abrir modal de cadastro
      vm.abrirModalCadastro = function() {
        vm.showModal = true;
        vm.limparFormulario();
      };
      
      // Fechar modal
      vm.fecharModal = function() {
        vm.showModal = false;
        vm.limparFormulario();
      };
      
      // Obter localização atual para o modal
      vm.obterLocalizacaoAtual = function() {
        if (!navigator.geolocation) {
          vm.mostrarMensagem('Geolocalização não é suportada por este navegador.', 'erro');
          return;
        }
        
        navigator.geolocation.getCurrentPosition(
          function(position) {
            vm.formData.latitude = position.coords.latitude;
            vm.formData.longitude = position.coords.longitude;
            vm.localizacaoObtida = true;
            vm.mostrarMensagem('Localização obtida com sucesso!', 'sucesso');
          },
          function(error) {
            console.error('Erro ao obter localização:', error);
            vm.mostrarMensagem('Não foi possível obter sua localização. Verifique as permissões do navegador.', 'erro');
          },
          {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000
          }
        );
      };
      
      // Cadastrar ponto via modal
      vm.cadastrarPonto = function() {
        // Validações
        if (!vm.formData.nome || !vm.formData.endereco) {
          vm.mostrarMensagem('Preencha todos os campos obrigatórios (nome e endereço).', 'erro');
          return;
        }
        
        vm.salvando = true;
        vm.mensagem = '';
        
        // Preparar dados para envio
        var dadosEnvio = {
          nome: vm.formData.nome,
          tipo: vm.formData.tipo,
          endereco: vm.formData.endereco,
          latitude: parseFloat(vm.formData.latitude),
          longitude: parseFloat(vm.formData.longitude),
          telefone: vm.formData.telefone || null,
          horario: vm.formData.horario || null,
          materiais: Object.keys(vm.formData.materiais).filter(function(material) {
            return vm.formData.materiais[material] === true;
          })
        };
        
        console.log('Enviando dados:', dadosEnvio);
        
        // Enviar para API
        PontoColetaService.cadastrar(dadosEnvio).then(function(response) {
          console.log('Ponto cadastrado:', response);
          vm.mostrarMensagem('Ponto de coleta cadastrado com sucesso!', 'sucesso');
          
          // Recarregar pontos após sucesso
          setTimeout(function() {
            vm.fecharModal();
            vm.loadNearbyPoints();
          }, 2000);
          
        }).catch(function(err) {
          console.error('Erro ao cadastrar ponto:', err);
          vm.mostrarMensagem('Erro ao cadastrar ponto: ' + (err.message || 'Erro desconhecido'), 'erro');
        }).finally(function() {
          vm.salvando = false;
        });
      };
      
      // Limpar formulário
      vm.limparFormulario = function() {
        vm.formData = {
          nome: '',
          tipo: 'Cooperativa',
          endereco: '',
          latitude: null,
          longitude: null,
          telefone: '',
          horario: '',
          materiais: {}
        };
        vm.localizacaoObtida = false;
        vm.mensagem = '';
      };
      
      // Mostrar mensagem
      vm.mostrarMensagem = function(texto, tipo) {
        vm.mensagem = texto;
        vm.mensagemTipo = tipo;
        
        // Limpar mensagem após 5 segundos
        setTimeout(function() {
          vm.mensagem = '';
          vm.mensagemTipo = '';
          if (!$scope.$$phase) {
            $scope.$apply();
          }
        }, 5000);
      };
      
      // Inicialização
      vm.init = function() {
        vm.getUserLocation();
      };
      
      // Inicia automaticamente
      vm.init();
    }]);
})();