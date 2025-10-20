<?php

namespace App\Repositories;

use App\Models\PontoColeta;

class PontoColetaRepository
{
    public function all()
    {
        return PontoColeta::orderBy('nome')->get();
    }
}


