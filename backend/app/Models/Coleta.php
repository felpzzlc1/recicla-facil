<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coleta extends Model
{
    protected $fillable = [
        'user_id',
        'material',
        'quantidade',
        'endereco',
        'data_preferida',
        'obs',
        'status',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'data_preferida' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


