<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recompensa;

class RecompensaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recompensas = [
            [
                'titulo' => 'Vale Compras R$ 50',
                'descricao' => 'Vale compras no valor de R$ 50,00 para usar em estabelecimentos parceiros',
                'icone' => '🛍️',
                'categoria' => 'Compras',
                'categoria_icone' => '✓',
                'pontos' => 5000,
                'disponivel' => 15,
                'ativo' => true
            ],
            [
                'titulo' => 'Café Grátis',
                'descricao' => 'Café grátis em estabelecimentos parceiros',
                'icone' => '☕',
                'categoria' => 'Gastronomia',
                'categoria_icone' => '☕',
                'pontos' => 500,
                'disponivel' => 50,
                'ativo' => true
            ],
            [
                'titulo' => 'Ingresso Cinema',
                'descricao' => 'Ingresso para cinema em qualquer filme em cartaz',
                'icone' => '🎬',
                'categoria' => 'Entretenimento',
                'categoria_icone' => '🎬',
                'pontos' => 3000,
                'disponivel' => 10,
                'ativo' => true
            ],
            [
                'titulo' => 'Kit Sustentável',
                'descricao' => 'Kit com produtos sustentáveis e ecológicos',
                'icone' => '🌱',
                'categoria' => 'Eco',
                'categoria_icone' => '🎁',
                'pontos' => 2000,
                'disponivel' => 25,
                'ativo' => true
            ],
            [
                'titulo' => 'Vale Compras R$ 100',
                'descricao' => 'Vale compras no valor de R$ 100,00 para usar em estabelecimentos parceiros',
                'icone' => '🛒',
                'categoria' => 'Compras',
                'categoria_icone' => '✓',
                'pontos' => 10000,
                'disponivel' => 8,
                'ativo' => true
            ],
            [
                'titulo' => 'Experiência Eco-Turismo',
                'descricao' => 'Passeio ecológico em parque natural com guia especializado',
                'icone' => '🏔️',
                'categoria' => 'Turismo',
                'categoria_icone' => '🧭',
                'pontos' => 15000,
                'disponivel' => 5,
                'ativo' => true
            ],
            [
                'titulo' => 'Desconto 20% Loja Verde',
                'descricao' => 'Desconto de 20% em produtos sustentáveis na Loja Verde',
                'icone' => '🌿',
                'categoria' => 'Desconto',
                'categoria_icone' => '💰',
                'pontos' => 1500,
                'disponivel' => 30,
                'ativo' => true
            ],
            [
                'titulo' => 'Livro Sustentabilidade',
                'descricao' => 'Livro sobre sustentabilidade e meio ambiente',
                'icone' => '📚',
                'categoria' => 'Educação',
                'categoria_icone' => '📖',
                'pontos' => 800,
                'disponivel' => 20,
                'ativo' => true
            ]
        ];

        foreach ($recompensas as $recompensa) {
            Recompensa::create($recompensa);
        }
    }
}
