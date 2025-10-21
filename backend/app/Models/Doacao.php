<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doacao extends Model
{
    protected $fillable = [
        'user_id',
        'material',
        'qtd',
        'contato',
        'entregue',
    ];

    protected $casts = [
        'entregue' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


