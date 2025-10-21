<?php

namespace App\Repositories;

use App\Models\Doacao;

class DoacaoRepository
{
    public function all()
    {
        return Doacao::orderByDesc('id')->get();
    }

    public function find($id)
    {
        return Doacao::find($id);
    }

    public function create(array $data)
    {
        return Doacao::create($data);
    }

    public function update($id, array $data)
    {
        $model = Doacao::find($id);
        if (!$model) {
            return null;
        }
        $model->update($data);
        return $model;
    }

    public function delete($id)
    {
        return Doacao::where('id', $id)->delete();
    }
}
