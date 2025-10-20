<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CronogramaColeta;

class CronogramaColetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            ],
            [
                'material' => 'Orgânico',
                'dia_semana' => 'Terça-feira',
                'horario_inicio' => '07:30:00',
                'horario_fim' => '11:30:00',
                'endereco' => 'Rua da Consolação, 2000',
                'bairro' => 'Consolação',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'latitude' => -23.5558,
                'longitude' => -46.6396,
                'observacoes' => 'Coleta de resíduos orgânicos',
                'ativo' => true,
            ],
            [
                'material' => 'Eletrônicos',
                'dia_semana' => 'Quinta-feira',
                'horario_inicio' => '14:00:00',
                'horario_fim' => '18:00:00',
                'endereco' => 'Shopping Center Norte',
                'bairro' => 'Vila Guilherme',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'latitude' => -23.5074,
                'longitude' => -46.6256,
                'observacoes' => 'Coleta de eletrônicos no shopping',
                'ativo' => true,
            ]
        ];

        foreach ($cronogramas as $cronograma) {
            CronogramaColeta::create($cronograma);
        }
    }
}
