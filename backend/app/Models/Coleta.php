<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coleta extends Model
{
    use HasFactory;

    protected $fillable = [
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
}


