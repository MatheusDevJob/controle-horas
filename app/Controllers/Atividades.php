<?php

namespace App\Controllers;

use App\Models\Atividade_model;
use App\Models\Conta_model;
use App\Models\Projetos_model;
use CodeIgniter\I18n\Time;

final class Atividades extends BaseController
{
    private $atvM;
    private $contaM;
    protected $projetoM;
    public function __construct()
    {
        $this->atvM                 = new Atividade_model();
        $this->contaM               = new Conta_model();
        $this->projetoM             = new Projetos_model();
    }

    function index()
    {
        $clienteID                  = $this->session->get("cliente_id");
        return view("atividades_usuario", [
            "titulo"                => "Visualizar Usuários",
            "projetos"              => $this->projetoM->getProjetos($clienteID)
        ]);
    }


    public function getAtividadesUsuariosAjax()
    {
        $request                = $this->request;
        $columns                = ['p.projeto', 'a.descricao', 'a.inicio_atividade', 'a.fim_atividade', 't.inicio_turno', 't.fim_turno'];
        $search                 = $request->getPost('search')['value'] ?? '';

        $orderColumnIndex       = $request->getPost('order')[0]['column'] ?? 0;
        $orderColumn            = $columns[$orderColumnIndex] ?? "p.projeto";
        $orderDir               = $request->getPost('order')[0]['dir'] ?? 'asc';

        $start                  = (int) $request->getPost('start');
        $length                 = (int) $request->getPost('length');
        $draw                   = (int) $request->getPost('draw');
        $dataI                  = $request->getPost("dataI");
        $dataF                  = $request->getPost("dataF");
        $projeto64              = $request->getPost("projeto");
        $projetoID              = base64_decode($projeto64);

        $clienteID              = $this->session->get("cliente_id");
        $userID                 = $this->session->get("user_id");

        $total                  = $this->contaM->countAllAtividadesUser($clienteID, $userID, null, $dataI, $dataF, $projetoID);

        $filtered               = $this->contaM->countAllAtividadesUser($clienteID, $userID, $search, $dataI, $dataF, $projetoID);

        $data                   = $this->contaM->getAllAtividadesUser([
            'search'            => $search,
            'order_by'          => $orderColumn,
            'order_dir'         => $orderDir,
            'start'             => $start,
            'length'            => $length,
            'clienteID'         => $clienteID,
            'userID'            => $userID,
            'dataI'             => $dataI,
            'dataF'             => $dataF,
            'projetoID'         => $projetoID,
        ]);

        return $this->response->setJSON([
            'draw'              => $draw,
            'recordsTotal'      => $total,
            'recordsFiltered'   => $filtered,
            'data'              => $data
        ]);
    }

    function iniciar_turno()
    {
        try {
            $projeto64                          = $this->request->getPost("projeto");
            $projetoID                          = base64_decode($projeto64);

            $clienteID                          = $this->session->get("cliente_id");
            $userID                             = $this->session->get("user_id");

            $resposta                           = $this->atvM->iniciar_turno($clienteID, $projetoID, $userID);
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
            $valorHora              = $this->session->get("valor_hora");

            $data                   = date("Y-m-d H:i:s");

            $atividade              = $this->atvM->get_turno_aberta($turnoID);

            $retornoCalculo         = calcularValorHoras($atividade["inicio_turno"], $data, $valorHora);

            $resposta               = $this->atvM->finalizar_turno(
                $turnoID,
                $data,
                $retornoCalculo["horasTrabalhadas"],
                $retornoCalculo["valorFaturado"]
            );
            if ($resposta["status"]) $this->session->remove("turno_id");
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
            $valorHora              = $this->session->get("valor_hora");

            $atividade              = $this->atvM->get_atividade_aberta($turnoID);

            $retornoCalculo         = calcularValorHoras($atividade["inicio_atividade"], $dataHora, $valorHora);

            $resposta               = $this->atvM->concluir_atividade(
                $dataHora,
                $desc,
                $turnoID,
                $retornoCalculo["horasTrabalhadas"],
                $valorHora,
                $retornoCalculo["valorFaturado"]
            );
        } catch (\Exception $e) {
            $resposta = [
                "status"            => false,
                "msg"               => $e->getMessage()
            ];
        }
        echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
    }

    function get_ativdades_turno()
    {
        try {
            $turnoID                = $this->session->get("turno_id");

            $resposta = [
                "status"            => true,
                "data"              => $this->atvM->get_atividades_turno($turnoID)
            ];
        } catch (\Exception $e) {
            $resposta = [
                "status"            => false,
                "msg"               => $e->getMessage()
            ];
        }
        echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
    }
}
