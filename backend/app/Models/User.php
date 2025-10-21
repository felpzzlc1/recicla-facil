<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'senha',
        'pontuacao',
    ];

    protected $hidden = [
        'senha',
    ];

    protected $casts = [
        'pontuacao' => 'integer',
    ];

    public function setSenhaAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['senha'] = Hash::make($value);
        }
    }

    public function checkPassword($password)
    {
        return Hash::check($password, $this->senha);
    }
}
