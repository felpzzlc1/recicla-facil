<?php

require_once __DIR__ . '/app/Repositories/PontuacaoRepository.php';

use App\Repositories\PontuacaoRepository;

echo "🧪 Testando PontuacaoRepository...\n\n";

try {
    $repository = new PontuacaoRepository();
    
    // Testar obter estatísticas
    echo "📊 Testando obterEstatisticasUsuario...\n";
    $estatisticas = $repository->obterEstatisticasUsuario(1);
    echo "Pontos: {$estatisticas['pontos']}\n";
    echo "Nível: {$estatisticas['nivel']} ({$estatisticas['nivel_nome']})\n";
    echo "Descartes: {$estatisticas['descartes']}\n";
    echo "Sequência: {$estatisticas['sequencia_dias']}\n";
    
    // Testar adicionar pontos
    echo "\n🎯 Testando adicionarPontos...\n";
    $resultado = $repository->adicionarPontos(1, 25, 'teste-repository');
    
    echo "Pontos após adição: {$resultado['pontuacao']->pontos}\n";
    echo "Descartes após adição: {$resultado['pontuacao']->descartes}\n";
    echo "Sequência após adição: {$resultado['pontuacao']->sequencia_dias}\n";
    
    // Testar ranking
    echo "\n🏆 Testando obterRanking...\n";
    $ranking = $repository->obterRanking(5);
    echo "Ranking (top 5):\n";
    foreach ($ranking as $posicao => $item) {
        echo ($posicao + 1) . ". {$item->nome} - {$item->pontos} pontos (Nível {$item->nivel})\n";
    }
    
    // Testar estatísticas gerais
    echo "\n📈 Testando obterEstatisticasGerais...\n";
    $gerais = $repository->obterEstatisticasGerais();
    echo "Total usuários: {$gerais['total_usuarios']}\n";
    echo "Total pontos: {$gerais['total_pontos']}\n";
    echo "Total descartes: {$gerais['total_descartes']}\n";
    echo "Média pontos: {$gerais['media_pontos']}\n";
    
    echo "\n🎉 Teste do Repository concluído com sucesso!\n";
    
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
