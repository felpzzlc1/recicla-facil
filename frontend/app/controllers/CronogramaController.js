(function(){
  'use strict';
  angular.module('reciclaFacilApp')
    .controller('CronogramaController', ['CronogramaService', 'AuthService', 'ModalService', '$scope', function(CronogramaService, AuthService, ModalService, $scope) {
      var vm = this;
      
      // Estado da aplicação
      vm.coletas = [];
      vm.loading = false;
      vm.error = null;
      vm.userLocation = null;
      vm.cidade = null;
      
      // Modal de cadastro
      vm.showModal = false;
      vm.formData = {
        material: '',
        dia_semana: '',
        horario_inicio: '',
        horario_fim: '',
        endereco: '',
        bairro: '',
        cidade: '',
        estado: '',
        latitude: null,
        longitude: null,
        observacoes: ''
      };
      vm.salvando = false;
      vm.localizacaoObtida = false;
      vm.mensagem = '';
      vm.mensagemTipo = '';
      
      // Opções para formulário
      vm.materiaisDisponiveis = CronogramaService.getMateriaisDisponiveis();
      vm.diasSemana = CronogramaService.getDiasSemana();
      
      // Função para obter localização do usuário
      vm.getUserLocation = function() {
        vm.loading = true;
        vm.error = null;
        
        if (!navigator.geolocation) {
          vm.error = 'Geolocalização não é suportada por este navegador.';
          vm.loading = false;
          vm.loadCronogramas();
          return;
        }
        
        navigator.geolocation.getCurrentPosition(
          function(position) {
            vm.userLocation = {
              latitude: position.coords.latitude,
              longitude: position.coords.longitude
            };
            vm.loadCronogramas();
          },
          function(error) {
            console.error('Erro ao obter localização:', error);
            vm.error = 'Não foi possível obter sua localização. Verifique as permissões do navegador.';
            vm.loading = false;
            vm.loadCronogramas();
          },
          {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000
          }
        );
      };
      
      // Função para carregar cronogramas
      vm.loadCronogramas = function() {
        vm.loading = true;
        
        // Carrega todos os cronogramas (simplificado - sem latitude/longitude)
        CronogramaService.list().then(function(dados) {
          vm.coletas = vm.formatarCronogramas(dados || []);
          vm.loading = false;
        }).catch(function(err) {
          console.error('Erro ao carregar cronogramas:', err);
          vm.error = 'Erro ao carregar cronograma de coleta.';
          vm.loading = false;
        });
      };
      
      // Função para formatar cronogramas para exibição
      vm.formatarCronogramas = function(cronogramas) {
        if (!cronogramas || cronogramas.length === 0) {
          return [];
        }
        
        return cronogramas.map(function(cronograma) {
          var data = new Date();
          var diaSemana = cronograma.dia_semana;
          
          // Encontrar próxima ocorrência do dia da semana
          var proximaData = vm.encontrarProximaData(diaSemana);
          
          var formatado = {
            id: cronograma.id,
            dia: proximaData.getDate(),
            mes: proximaData.toLocaleDateString('pt-BR', { month: 'short' }),
            diaSemana: diaSemana,
            material: cronograma.material,
            cor: CronogramaService.getCorMaterial(cronograma.material),
            horario: CronogramaService.formatarHorario(cronograma.horario_inicio, cronograma.horario_fim),
            localizacao: cronograma.bairro + ', ' + cronograma.cidade,
            endereco: cronograma.endereco,
            observacoes: cronograma.observacoes
          };
          return formatado;
        });
      };
      
      // Função para encontrar próxima data do dia da semana
      vm.encontrarProximaData = function(diaSemana) {
        var diasSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
        var indiceDia = diasSemana.indexOf(diaSemana);
        var hoje = new Date();
        var diasParaAdicionar = (indiceDia - hoje.getDay() + 7) % 7;
        
        if (diasParaAdicionar === 0) {
          diasParaAdicionar = 7; // Se for hoje, pega a próxima semana
        }
        
        var proximaData = new Date(hoje);
        proximaData.setDate(hoje.getDate() + diasParaAdicionar);
        return proximaData;
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
        
        // Recarregar a página para atualizar a lista
        setTimeout(function() {
          window.location.reload();
        }, 100);
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
      
      // Cadastrar cronograma via modal
      vm.cadastrarCronograma = function() {
        // Validações
        if (!vm.formData.material || !vm.formData.dia_semana || !vm.formData.horario_inicio || !vm.formData.horario_fim || !vm.formData.endereco || !vm.formData.bairro || !vm.formData.cidade || !vm.formData.estado) {
          vm.mostrarMensagem('Preencha todos os campos obrigatórios.', 'erro');
          return;
        }
        
        vm.salvando = true;
        vm.mensagem = '';
        
        // Preparar dados para envio
        var dadosEnvio = {
          material: vm.formData.material,
          dia_semana: vm.formData.dia_semana,
          horario_inicio: vm.formData.horario_inicio,
          horario_fim: vm.formData.horario_fim,
          endereco: vm.formData.endereco,
          bairro: vm.formData.bairro,
          cidade: vm.formData.cidade,
          estado: vm.formData.estado,
          latitude: vm.formData.latitude ? parseFloat(vm.formData.latitude) : null,
          longitude: vm.formData.longitude ? parseFloat(vm.formData.longitude) : null,
          observacoes: vm.formData.observacoes || null
        };
        
        // Enviar para API
        CronogramaService.criar(dadosEnvio).then(function(response) {
          vm.mostrarMensagem('Cronograma de coleta cadastrado com sucesso!', 'sucesso');
          
          // Recarregar cronogramas após sucesso
          setTimeout(function() {
            vm.fecharModal();
            vm.loadCronogramas();
          }, 2000);
          
        }).catch(function(err) {
          console.error('Erro ao cadastrar cronograma:', err);
          vm.mostrarMensagem('Erro ao cadastrar cronograma: ' + (err.message || 'Erro desconhecido'), 'erro');
        }).finally(function() {
          vm.salvando = false;
        });
      };
      
      // Limpar formulário
      vm.limparFormulario = function() {
        vm.formData = {
          material: '',
          dia_semana: '',
          horario_inicio: '',
          horario_fim: '',
          endereco: '',
          bairro: '',
          cidade: '',
          estado: '',
          latitude: null,
          longitude: null,
          observacoes: ''
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
      
      // Funções de autenticação
      vm.isLoggedIn = function() {
        return AuthService.isLoggedIn();
      };
      
      vm.openLoginModal = function() {
        ModalService.openModal('login');
      };
      
      // Inicialização
      vm.init = function() {
        vm.loadCronogramas();
      };
      
      // Inicia automaticamente
      vm.init();
    }]);
})();