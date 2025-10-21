<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResgateRecompensa extends Model
{
    protected $fillable = [
        'user_id',
        'recompensa_id',
        'pontos_gastos',
        'status',
        'data_resgate',
        'observacoes'
    ];

    protected $casts = [
        'pontos_gastos' => 'integer',
        'data_resgate' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recompensa()
    {
        return $this->belongsTo(Recompensa::class);
    }
}
