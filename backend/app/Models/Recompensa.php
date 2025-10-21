<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recompensa extends Model
{
    protected $fillable = [
        'titulo',
        'descricao',
        'icone',
        'categoria',
        'categoria_icone',
        'pontos',
        'disponivel',
        'ativo',
        'imagem_url'
    ];

    protected $casts = [
        'pontos' => 'integer',
        'disponivel' => 'integer',
        'ativo' => 'boolean'
    ];

    public function resgates()
    {
        return $this->hasMany(ResgateRecompensa::class);
    }

    public function isDisponivel()
    {
        return $this->ativo && $this->disponivel > 0;
    }
}
