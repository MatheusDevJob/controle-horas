<?php

namespace App\Controllers;

use App\Models\Clientes_model;
use App\Models\Conta_model;

class Home extends BaseController
{
    private $contaM;
    private $clienteM;
    public function __construct()
    {
        $this->contaM           = new Conta_model();
        $this->clienteM         = new Clientes_model();
    }

    public function index()
    {
        return view('login');
    }

    public function criar_conta()
    {
        return view('criar_conta', [
            "clientes"          => $this->clienteM->getClientes()
        ]);
    }

    public function home()
    {
        return view('home', [
            "titulo"    => "Home"
        ]);
    }

    public function login()
    {
        try {
            $usuario            = $this->request->getPost("usuario");
            $senha              = $this->request->getPost("senha");

            $user               = $this->contaM->getContaByUser($usuario);
            if (!$user)         throw new \Exception("Usuário não encontrado");

            $bool               = password_verify($senha, $user["senha"]);
            if (!$bool)         throw new \Exception("Senha inválida");




            $sessao             = [
                "user_id"           => $user["user_id"],
                "user_nome"         => $user["user_nome"],
                "usuario"           => $user["usuario"],
                "ativo"             => $user["ativo"],
                "tipo_usuario_fk"   => $user["tipo_usuario_fk"],
                "logado"            => true
            ];

            $this->session->set($sessao);
            $resposta           = ["status" => true, "msg" => "Acesso liberado."];
        } catch (\Exception $e) {
            $resposta           = ["status" => false, "msg" => $e->getMessage()];
        }

        echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
    }

    public function cadastrar_usuario()
    {
        $usuario                = $this->request->getPost("usuario");
        $senha                  = $this->request->getPost("senha");
        $userNome               = $this->request->getPost("userNome");
        $cliente64              = $this->request->getPost("cliente");
        $clienteID              = base64_decode($cliente64);

        $senha                  = password_hash($senha, PASSWORD_BCRYPT);


        try {
            $resposta           = $this->contaM->cadastrar($usuario, $senha, $userNome, $clienteID);
        } catch (\Exception $e) {
            $resposta           = ["status" => false, "msg" => $e->getMessage()];
        }

        echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
    }

    function logout()
    {
        echo json_encode($this->session->destroy());
    }
}
