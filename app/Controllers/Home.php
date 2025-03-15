<?php

namespace App\Controllers;

use App\Models\Conta_model;

class Home extends BaseController
{
    private $contaM;
    public function __construct()
    {
        $this->contaM = new Conta_model();
    }

    public function index()
    {
        return view('login');
    }

    public function criar_conta()
    {
        return view('criar_conta');
    }

    public function home()
    {
        return view('home');
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

            unset($user["senha"]);
            unset($user["usuario"]);
            $user["logado"]     = true;
            $this->session->set($user);
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

        $senha                  = password_hash($senha, PASSWORD_BCRYPT);


        try {
            $resposta           = $this->contaM->cadastrar($usuario, $senha, $userNome);
        } catch (\Exception $e) {
            $resposta           = ["status" => false, "msg" => $e->getMessage()];
        }

        echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
    }
}
