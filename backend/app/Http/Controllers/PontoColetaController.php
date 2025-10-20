<?php

namespace App\Http\Controllers;

use App\Repositories\PontoColetaRepository;
use App\Helpers\ApiResponse;

class PontoColetaController extends Controller
{
    private PontoColetaRepository $repo;

    public function __construct(PontoColetaRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        return ApiResponse::success($this->repo->all());
    }
}


