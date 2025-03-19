<?php

namespace App\Controllers;

use App\Models\Clientes_model;

final class Clientes extends BaseController
{
    private $clienteM;
    public function __construct()
    {
        $this->clienteM = new Clientes_model();
    }

    function getClientes()
    {
        try {
            $resposta = [
                "status"    => true,
                "data"      => $this->clienteM->getClientes()
            ];
        } catch (\Exception $e) {
            $resposta = [
                "status" => false,
                "msg" => $e->getMessage()
            ];
        }
        echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
    }
}
