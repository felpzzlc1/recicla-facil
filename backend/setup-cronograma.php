<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Configuração do banco de dados
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'recicla_facil',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🚀 Configurando cronograma de coleta...\n";

try {
    // Executar migration
    echo "📋 Executando migration da tabela cronograma_coletas...\n";
    
    if (!Capsule::schema()->hasTable('cronograma_coletas')) {
        Capsule::schema()->create('cronograma_coletas', function (Blueprint $table) {
            $table->id();
            $table->string('material');
            $table->string('dia_semana');
            $table->time('horario_inicio');
            $table->time('horario_fim');
            $table->string('endereco');
            $table->string('bairro');
            $table->string('cidade');
            $table->string('estado');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
        echo "✅ Tabela cronograma_coletas criada com sucesso!\n";
    } else {
        echo "ℹ️  Tabela cronograma_coletas já existe.\n";
    }

    // Executar seeder
    echo "🌱 Executando seeder de cronograma...\n";
    
    $cronogramas = [
        [
            'material' => 'Papel',
            'dia_semana' => 'Segunda-feira',
            'horario_inicio' => '08:00:00',
            'horario_fim' => '12:00:00',
            'endereco' => 'Rua das Flores, 123',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'latitude' => -23.5505,
            'longitude' => -46.6333,
            'observacoes' => 'Coleta de papel e papelão no centro da cidade',
            'ativo' => true,
        ],
        [
            'material' => 'Plástico',
            'dia_semana' => 'Quarta-feira',
            'horario_inicio' => '08:00:00',
            'horario_fim' => '13:00:00',
            'endereco' => 'Avenida Paulista, 1000',
            'bairro' => 'Bela Vista',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'latitude' => -23.5613,
            'longitude' => -46.6565,
            'observacoes' => 'Coleta de plástico na região da Paulista',
            'ativo' => true,
        ],
        [
            'material' => 'Metal',
            'dia_semana' => 'Sexta-feira',
            'horario_inicio' => '07:00:00',
            'horario_fim' => '12:00:00',
            'endereco' => 'Rua Augusta, 456',
            'bairro' => 'Consolação',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'latitude' => -23.5558,
            'longitude' => -46.6396,
            'observacoes' => 'Coleta de metais na região da Augusta',
            'ativo' => true,
        ],
        [
            'material' => 'Vidro',
            'dia_semana' => 'Sábado',
            'horario_inicio' => '08:00:00',
            'horario_fim' => '11:00:00',
            'endereco' => 'Rua Oscar Freire, 789',
            'bairro' => 'Jardins',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'latitude' => -23.5676,
            'longitude' => -46.6734,
            'observacoes' => 'Coleta de vidro nos Jardins',
            'ativo' => true,
        ],
        [
            'material' => 'Coleta Especial',
            'dia_semana' => 'Domingo',
            'horario_inicio' => '09:00:00',
            'horario_fim' => '12:00:00',
            'endereco' => 'Parque Ibirapuera',
            'bairro' => 'Vila Mariana',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'latitude' => -23.5874,
            'longitude' => -46.6576,
            'observacoes' => 'Coleta especial de todos os materiais no parque',
            'ativo' => true,
        ]
    ];

    foreach ($cronogramas as $cronograma) {
        Capsule::table('cronograma_coletas')->insert($cronograma);
    }
    
    echo "✅ Seeder executado com sucesso! " . count($cronogramas) . " cronogramas inseridos.\n";
    
    echo "\n🎉 Configuração do cronograma concluída com sucesso!\n";
    echo "📅 Agora você pode acessar a página de cronograma e adicionar novos horários de coleta.\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
