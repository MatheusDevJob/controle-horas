<?php

namespace App\Controllers;

use App\Models\adm\Conta_model;

final class Excel extends BaseController
{
    private $contaM;
    public function __construct()
    {
        $this->contaM = new Conta_model();
    }
}
