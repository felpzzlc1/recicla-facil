<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conquista extends Model
{
    use HasFactory;

    protected $fillable = [
        'pontuacao_id',
        'nome',
        'icone',
        'desbloqueada_em'
    ];

    protected $casts = [
        'desbloqueada_em' => 'datetime'
    ];

    public function pontuacao()
    {
        return $this->belongsTo(Pontuacao::class);
    }
}
