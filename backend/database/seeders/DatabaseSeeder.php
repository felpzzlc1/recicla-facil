<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PontoColeta;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);

        PontoColeta::query()->truncate();
        PontoColeta::insert([
            [ 'nome' => 'Eco Ponto Centro', 'tipo' => 'Papel/Plástico', 'endereco' => 'Av. Central, 100' ],
            [ 'nome' => 'Coleta Verde', 'tipo' => 'Vidro/Metal', 'endereco' => 'Rua das Flores, 200' ],
            [ 'nome' => 'Recicla Bairro', 'tipo' => 'Eletrônicos', 'endereco' => 'Praça da Matriz, 50' ],
        ]);
    }
}


