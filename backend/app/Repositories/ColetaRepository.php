<?php

namespace App\Repositories;

use App\Models\Coleta;

class ColetaRepository
{
    public function all()
    {
        return Coleta::orderByDesc('id')->get();
    }

    public function find($id)
    {
        return Coleta::find($id);
    }

    public function create(array $data)
    {
        return Coleta::create($data);
    }

    public function update($id, array $data)
    {
        $coleta = Coleta::find($id);
        if (!$coleta) {
            return null;
        }
        $coleta->update($data);
        return $coleta;
    }

    public function delete($id)
    {
        return Coleta::where('id', $id)->delete();
    }
}
