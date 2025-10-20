<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PontoColeta;

class PontoColetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pontos = [
            [
                'nome' => 'Cooperativa Recicla Vida',
                'tipo' => 'Cooperativa',
                'endereco' => 'Rua das Flores, 200 - Centro',
                'latitude' => -23.5505,
                'longitude' => -46.6333,
                'telefone' => '(11) 1234-5678',
                'horario' => 'Seg-Sex: 8h-18h | Sáb: 8h-12h',
                'materiais_aceitos' => ['Plástico', 'Metal', 'Vidro'],
                'ativo' => true
            ],
            [
                'nome' => 'Ecoponto Municipal',
                'tipo' => 'Ecoponto',
                'endereco' => 'Av. Central, 100 - Centro',
                'latitude' => -23.5515,
                'longitude' => -46.6343,
                'telefone' => '(11) 2345-6789',
                'horario' => 'Seg-Sex: 7h-19h | Sáb: 7h-13h',
                'materiais_aceitos' => ['Papel', 'Plástico', 'Metal', 'Vidro'],
                'ativo' => true
            ],
            [
                'nome' => 'Ponto Verde Bairro',
                'tipo' => 'Ponto Verde',
                'endereco' => 'Praça da Matriz, 50 - Bairro',
                'latitude' => -23.5495,
                'longitude' => -46.6323,
                'telefone' => '(11) 3456-7890',
                'horario' => 'Seg-Sex: 8h-17h | Sáb: 8h-12h',
                'materiais_aceitos' => ['Vidro', 'Papel', 'Plástico'],
                'ativo' => true
            ],
            [
                'nome' => 'Centro de Triagem Verde',
                'tipo' => 'Centro de Triagem',
                'endereco' => 'Rua da Reciclagem, 300 - Zona Norte',
                'latitude' => -23.5525,
                'longitude' => -46.6353,
                'telefone' => '(11) 4567-8901',
                'horario' => 'Seg-Sex: 6h-20h | Sáb: 6h-14h',
                'materiais_aceitos' => ['Metal', 'Vidro', 'Papel', 'Plástico', 'Orgânico'],
                'ativo' => true
            ],
            [
                'nome' => 'Coleta Seletiva Sul',
                'tipo' => 'Ponto Verde',
                'endereco' => 'Av. Sul, 500 - Zona Sul',
                'latitude' => -23.5485,
                'longitude' => -46.6313,
                'telefone' => '(11) 5678-9012',
                'horario' => 'Seg-Sex: 9h-17h | Sáb: 9h-13h',
                'materiais_aceitos' => ['Plástico', 'Papel'],
                'ativo' => true
            ]
        ];

        foreach ($pontos as $ponto) {
            PontoColeta::create($ponto);
        }
    }
}
