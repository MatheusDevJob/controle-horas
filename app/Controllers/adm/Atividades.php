<?php

namespace App\Controllers\adm;

use App\Controllers\BaseController;
use App\Models\adm\Conta_model;
use App\Models\Atividade_model;

final class Atividades extends BaseController
{
    private $contaM;
    public function __construct()
    {
        $this->contaM = new Conta_model();
    }

    function index()
    {
        return view("adm/atividades_usuario", [
            "titulo"                => "Visualizar Usuários"
        ]);
    }

    public function getUsuariosAjax()
    {
        $request                = $this->request;
        $columns                = ['user_nome', 'turno'];
        $search                 = $request->getPost('search')['value'] ?? '';

        $orderColumnIndex       = $request->getPost('order')[0]['column'] ?? 0;
        $orderColumn            = $columns[$orderColumnIndex] ?? "user_nome";
        $orderDir               = $request->getPost('order')[0]['dir'] ?? 'asc';

        $start                  = (int) $request->getPost('start');
        $length                 = (int) $request->getPost('length');
        $draw                   = (int) $request->getPost('draw');

        $clienteID              = $this->session->get("cliente_id");

        $total                  = $this->contaM->countAllUser($clienteID);

        $filtered               = $this->contaM->countAllUser($clienteID, $search);

        $data                   = $this->contaM->getAllUser([
            'search'            => $search,
            'order_by'          => $orderColumn,
            'order_dir'         => $orderDir,
            'start'             => $start,
            'length'            => $length,
            'clienteID'         => $clienteID,
        ]);

        foreach ($data as &$val) {
            $val["acoes"] = "<button class=\"btn btn-sm btn-info\" onclick=\"selecionarUsuario($(this), '{$val["user_id"]}')\"><i class='fa-solid fa-eye'></i></button>";
        }

        return $this->response->setJSON([
            'draw'              => $draw,
            'recordsTotal'      => $total,
            'recordsFiltered'   => $filtered,
            'data'              => $data
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
        $user64                 = $request->getPost('userID');
        $userID                 = base64_decode($user64);

        $clienteID              = $this->session->get("cliente_id");

        $total                  = $this->contaM->countAllAtividadesUser($clienteID, $userID);

        $filtered               = $this->contaM->countAllAtividadesUser($clienteID, $userID, $search);

        $data                   = $this->contaM->getAllAtividadesUser([
            'search'            => $search,
            'order_by'          => $orderColumn,
            'order_dir'         => $orderDir,
            'start'             => $start,
            'length'            => $length,
            'clienteID'         => $clienteID,
            'userID'         => $userID,
        ]);

        return $this->response->setJSON([
            'draw'              => $draw,
            'recordsTotal'      => $total,
            'recordsFiltered'   => $filtered,
            'data'              => $data
        ]);
    }
}
