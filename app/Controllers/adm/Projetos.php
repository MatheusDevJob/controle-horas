<?php

namespace App\Controllers\adm;

use App\Controllers\BaseController;
use App\Models\adm\Projetos_model;

final class Projetos extends BaseController
{
    private $projM;
    public function __construct($var = null)
    {
        $this->projM = new Projetos_model();
    }

    function index()
    {
        return view("adm/projetos", [
            "titulo"                => "Projetos " . $this->session->get("cliente_nome"),
            "userID"                => $this->session->get("usuario_id")
        ]);
    }

    function getProjetosAjax()
    {
        $request                = $this->request;
        $columns                = ['p.projeto', 'p.data_registro'];
        $search                 = $request->getPost('search')['value'] ?? '';

        $orderColumnIndex       = $request->getPost('order')[0]['column'] ?? 0;
        $orderColumn            = $columns[$orderColumnIndex] ?? "p.projeto";
        $orderDir               = $request->getPost('order')[0]['dir'] ?? 'asc';

        $start                  = (int) $request->getPost('start');
        $length                 = (int) $request->getPost('length');
        $draw                   = (int) $request->getPost('draw');

        $clienteID              = $this->session->get("cliente_id");

        $total                  = $this->projM->countAllProjetosCliente($clienteID);

        $filtered               = $this->projM->countAllProjetosCliente($clienteID, $search);

        $data                   = $this->projM->getAllProjetosCliente([
            'search'            => $search,
            'order_by'          => $orderColumn,
            'order_dir'         => $orderDir,
            'start'             => $start,
            'length'            => $length,
            'clienteID'         => $clienteID
        ]);

        foreach ($data as &$val) {
            if ($val["ativo"]) {
                $val["acoes"] = "<button class='btn btn-sm btn-danger' onclick='mudaStatusProjeto($(this), \"{$val["projeto_id"]}\", false)'>Inativar</button>";
            } else {
                $val["acoes"] = "<button class='btn btn-sm btn-success' onclick='mudaStatusProjeto($(this), \"{$val["projeto_id"]}\", true)'>Ativar</button>";
            }
        }

        return $this->response->setJSON([
            'draw'              => $draw,
            'recordsTotal'      => $total,
            'recordsFiltered'   => $filtered,
            'data'              => $data
        ]);
    }

    function cadastrar()
    {


        $projeto                    = $this->request->getPost("projeto");

        $clienteID                  = $this->session->get("cliente_id");
        $userID                     = $this->session->get("user_id");
        $userNome                   = $this->session->get("user_nome");
        $tipoUsuarioID              = $this->session->get("tipo_usuario_fk");
        $dataRegistro               = date("Y-m-d");

        $status                     = $this->projM->cadastrar($projeto, $clienteID, $dataRegistro, $userID, $userNome, $tipoUsuarioID);

        $resposta = [
            "status"                => $status,
            "msg"                   => "Projeto $projeto " . ($status ? "" : "não ") . "registrado."
        ];

        return $this->response->setJSON($resposta, true);
    }

    function mudaStatusProjeto()
    {
        try {

            $projeto64                  = $this->request->getPost("projetoID");
            $status                     = $this->request->getPost("status");
            $status                     = $status == "true" ? 1 : 0;

            $projetoID                  = base64_decode($projeto64);

            $clienteID                  = $this->session->get("cliente_id");
            $userID                     = $this->session->get("user_id");
            $userNome                   = $this->session->get("user_nome");
            $tipoUsuarioID              = $this->session->get("tipo_usuario_fk");

            $data                       = date("Y-m-d H:i:s");

            $resultado                  = $this->projM->mudaStatusProjeto($projetoID, $clienteID, $status, $data, $userID, $userNome, $tipoUsuarioID);
            $resposta = [
                "status"                => $resultado,
                "msg"                   => "Projeto " . $status == 1 ? "Ativado." : "Inativado."
            ];
        } catch (\Throwable $th) {
            $resposta = [
                "status"                => false,
                "msg"                   => $th->getMessage()
            ];
        }
        return $this->response->setJSON($resposta, true);
    }
}
