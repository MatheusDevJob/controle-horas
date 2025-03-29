<?php

namespace App\Controllers;

use App\Models\Projetos_model;

final class Projetos extends BaseController
{
    protected $projetoM;
    public function __construct()
    {
        $this->projetoM = new Projetos_model();
    }

    function getProjetos()
    {
        try {
            $clienteID              = $this->session->get("cliente_id");
            $resposta               = ["status" => true, "data" => $this->projetoM->getProjetos($clienteID)];
        } catch (\Throwable $th) {
            $resposta               = ["status" => false, "msg" => $th->getMessage()];
        }

        echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
    }
}
