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

echo "🚀 Executando migrations...\n\n";

try {
    // Executar todas as migrations
    $migrations = [
        'users' => function() {
            if (!Capsule::schema()->hasTable('users')) {
                Capsule::schema()->create('users', function (Blueprint $table) {
                    $table->id();
                    $table->string('nome');
                    $table->string('email')->unique();
                    $table->string('telefone');
                    $table->string('senha');
                    $table->timestamps();
                });
                echo "✅ Tabela 'users' criada\n";
            } else {
                echo "ℹ️  Tabela 'users' já existe\n";
            }
        },
        
        'sessions' => function() {
            if (!Capsule::schema()->hasTable('sessions')) {
                Capsule::schema()->create('sessions', function (Blueprint $table) {
                    $table->string('id')->primary();
                    $table->foreignId('user_id')->nullable()->index();
                    $table->string('ip_address', 45)->nullable();
                    $table->text('user_agent')->nullable();
                    $table->longText('payload');
                    $table->integer('last_activity')->index();
                });
                echo "✅ Tabela 'sessions' criada\n";
            } else {
                echo "ℹ️  Tabela 'sessions' já existe\n";
            }
        },
        
        'pontuacoes' => function() {
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
                echo "✅ Tabela 'pontuacoes' criada\n";
            } else {
                echo "ℹ️  Tabela 'pontuacoes' já existe\n";
            }
        },
        
        'conquistas' => function() {
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
                echo "✅ Tabela 'conquistas' criada\n";
            } else {
                echo "ℹ️  Tabela 'conquistas' já existe\n";
            }
        }
    ];
    
    foreach ($migrations as $name => $migration) {
        echo "Executando migration: $name...\n";
        $migration();
    }
    
    echo "\n🎯 Executando seeders...\n";
    
    // Criar usuário de teste se não existir
    $user = Capsule::table('users')->where('email', 'teste@recicla.com')->first();
    if (!$user) {
        $userId = Capsule::table('users')->insertGetId([
            'nome' => 'Usuário Teste',
            'email' => 'teste@recicla.com',
            'telefone' => '11999999999',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "✅ Usuário de teste criado (ID: $userId)\n";
    } else {
        $userId = $user->id;
        echo "ℹ️  Usuário de teste já existe (ID: $userId)\n";
    }
    
    // Criar pontuação para o usuário de teste
    $pontuacao = Capsule::table('pontuacoes')->where('user_id', $userId)->first();
    if (!$pontuacao) {
        $pontuacaoId = Capsule::table('pontuacoes')->insertGetId([
            'user_id' => $userId,
            'pontos' => 8350,
            'nivel' => 12,
            'nivel_nome' => 'Reciclador Expert',
            'descartes' => 156,
            'sequencia_dias' => 23,
            'badges_conquistadas' => 5,
            'pontos_semana_atual' => 250,
            'total_pontos_ganhos' => 8350,
            'ultima_atualizacao' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "✅ Pontuação criada para usuário de teste (ID: $pontuacaoId)\n";
        
        // Criar algumas conquistas
        $conquistas = [
            ['nome' => 'Iniciante', 'icone' => '🌱'],
            ['nome' => 'Reciclador', 'icone' => '♻️'],
            ['nome' => 'Eco Warrior', 'icone' => '☀️'],
            ['nome' => 'Primeiro Descarte', 'icone' => '🎯'],
            ['nome' => 'Sequência de 7 dias', 'icone' => '⚡']
        ];
        
        foreach ($conquistas as $conquista) {
            Capsule::table('conquistas')->insert([
                'pontuacao_id' => $pontuacaoId,
                'nome' => $conquista['nome'],
                'icone' => $conquista['icone'],
                'desbloqueada_em' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        echo "✅ Conquistas criadas\n";
    } else {
        echo "ℹ️  Pontuação já existe para usuário de teste\n";
    }
    
    echo "\n✨ Migrations e seeders executados com sucesso!\n";
    echo "\n📋 Para testar:\n";
    echo "1. Acesse: http://localhost:9161\n";
    echo "2. Faça login com: teste@recicla.com / 123456\n";
    echo "3. Vá para 'Minha Pontuação'\n";
    
} catch (Exception $e) {
    echo "❌ Erro ao executar migrations: " . $e->getMessage() . "\n";
    exit(1);
}