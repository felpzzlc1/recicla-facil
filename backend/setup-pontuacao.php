<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Configuração do banco de dados
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'database' => $_ENV['DB_DATABASE'] ?? 'recicla_facil',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🚀 Configurando sistema de pontuação...\n\n";

try {
    // Executar migrations
    echo "📊 Executando migrations...\n";
    
    // Migration para pontuações
    if (!Capsule::schema()->hasTable('pontuacoes')) {
        Capsule::schema()->create('pontuacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('pontos')->default(0);
            $table->integer('nivel')->default(1);
            $table->string('nivel_nome')->default('Iniciante');
            $table->integer('descartes')->default(0);
            $table->integer('sequencia_dias')->default(0);
            $table->integer('badges_conquistadas')->default(0);
            $table->integer('pontos_semana_atual')->default(0);
            $table->integer('total_pontos_ganhos')->default(0);
            $table->timestamp('ultima_atualizacao')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'pontos']);
            $table->index('nivel');
        });
        echo "✅ Tabela 'pontuacoes' criada com sucesso!\n";
    } else {
        echo "ℹ️  Tabela 'pontuacoes' já existe.\n";
    }
    
    // Migration para conquistas
    if (!Capsule::schema()->hasTable('conquistas')) {
        Capsule::schema()->create('conquistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pontuacao_id')->constrained()->onDelete('cascade');
            $table->string('nome');
            $table->string('icone');
            $table->timestamp('desbloqueada_em');
            $table->timestamps();
            
            $table->index(['pontuacao_id', 'nome']);
        });
        echo "✅ Tabela 'conquistas' criada com sucesso!\n";
    } else {
        echo "ℹ️  Tabela 'conquistas' já existe.\n";
    }
    
    echo "\n🎯 Sistema de pontuação configurado com sucesso!\n";
    echo "📋 Funcionalidades disponíveis:\n";
    echo "   • Sistema de pontos e níveis\n";
    echo "   • Conquistas e badges\n";
    echo "   • Ranking de usuários\n";
    echo "   • Simulação de descarte\n";
    echo "   • Estatísticas detalhadas\n";
    echo "   • API REST completa\n\n";
    
    echo "🔗 Rotas da API:\n";
    echo "   GET  /api/pontuacao/estatisticas\n";
    echo "   POST /api/pontuacao/adicionar\n";
    echo "   GET  /api/pontuacao/ranking\n";
    echo "   GET  /api/pontuacao/conquistas\n";
    echo "   GET  /api/pontuacao/estatisticas-gerais\n";
    echo "   POST /api/pontuacao/simular-descarte\n";
    echo "   POST /api/pontuacao/resetar-semanais\n\n";
    
    echo "✨ O sistema está pronto para uso!\n";
    
} catch (Exception $e) {
    echo "❌ Erro ao configurar sistema de pontuação: " . $e->getMessage() . "\n";
    exit(1);
}
