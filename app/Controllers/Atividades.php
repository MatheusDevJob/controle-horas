<?php

namespace App\Controllers;

use App\Models\Atividade_model;

final class Atividades extends BaseController
{
    private $atvM;
    public function __construct()
    {
        $this->atvM = new Atividade_model();
    }

    function iniciar_turno()
    {
        try {
            $cliente64                          = $this->request->getPost("cliente");
            $clienteID                          = base64_decode($cliente64);

            $userID                             = $this->session->get("user_id");

            $resposta                           = $this->atvM->iniciar_turno($clienteID, $userID);
            $this->session->set("turno_id",     $resposta["id"]);
            unset($resposta["id"]);
        } catch (\Exception $e) {
            $resposta = [
                "status"                        => false,
                "msg"                           => $e->getMessage()
            ];
        }
        echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
    }

    function concluir_turno()
    {
        try {
            $turnoID                = $this->session->get("turno_id");

            $resposta               = $this->atvM->finalizar_turno($turnoID);
        } catch (\Exception $e) {
            $resposta = [
                "status"            => false,
                "msg"               => $e->getMessage()
            ];
        }
        echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
    }

    function iniciar_atividade()
    {
        try {
            $dataHora               = $this->request->getPost("dataHoraAtual");

            $turnoID                = $this->session->get("turno_id");

            $resposta               = $this->atvM->iniciar_atividade($dataHora, $turnoID);
        } catch (\Exception $e) {
            $resposta = [
                "status"            => false,
                "msg"               => $e->getMessage()
            ];
        }
        echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
    }

    function concluir_atividade()
    {
        try {
            $dataHora               = $this->request->getPost("dataHoraAtual");
            $desc                   = $this->request->getPost("desc");

            $turnoID                = $this->session->get("turno_id");

            $resposta               = $this->atvM->concluir_atividade($dataHora, $desc, $turnoID);
        } catch (\Exception $e) {
            $resposta = [
                "status"            => false,
                "msg"               => $e->getMessage()
            ];
        }
        echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
    }
}
