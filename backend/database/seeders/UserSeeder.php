<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nome' => 'Usuário Demo',
            'email' => 'demo@recicla.com',
            'telefone' => '(11) 99999-9999',
            'senha' => Hash::make('123456'),
            'pontuacao' => 150,
        ]);

        User::create([
            'nome' => 'João Silva',
            'email' => 'joao@email.com',
            'telefone' => '(11) 88888-8888',
            'senha' => Hash::make('123456'),
            'pontuacao' => 75,
        ]);
    }
}
