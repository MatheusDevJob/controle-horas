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
            "titulo"    => "Home",
            "turnoID"   => $this->session->get("turno_id")
        ]);
    }

    public function login()
    {
        try {
            $usuario            = $this->request->getPost("usuario");
            $senha              = $this->request->getPost("senha");
            $cnpj               = $this->request->getPost('cnpj');

            if ($cnpj) {
                $cnpj               = str_replace([".", "/", "-"], "", $cnpj);
                $cnpj               = $this->clienteM->getClienteByCnpj($cnpj);
                if (!$cnpj)         throw new \Exception("Empresa não registrada");
                set_cookie([
                    'name'     => 'cnpj',
                    'value'    => $cnpj["cnpj"],
                    'expire'   => 0,
                    'path'     => '/',
                    'secure'   => false,
                    'httponly' => false
                ]);
            } else {
                $cnpj               = get_cookie('cnpj');
                $cnpj               = $this->clienteM->getClienteByCnpj($cnpj);
                if (!$cnpj)         throw new \Exception("Empresa não registrada");
            }



            $user               = $this->contaM->getContaByUser($usuario);
            if (!$user)         throw new \Exception("Usuário não encontrado");

            $bool               = password_verify($senha, $user["senha"]);
            if (!$bool)         throw new \Exception("Senha inválida");

            $turno              = $this->contaM->get_turno_aberto($user["user_id"]);

            $token = bin2hex(random_bytes(32));
            $this->contaM->atualizar($user["user_id"], ["session_token" => $token]);


            $sessao             = [
                "user_id"           => $user["user_id"],
                "user_nome"         => $user["user_nome"],
                "usuario"           => $user["usuario"],
                "ativo"             => $user["ativo"],
                "tipo_usuario_fk"   => $user["tipo_usuario_fk"],
                "cnpj"              => $cnpj["cnpj"],
                "cliente_id"        => $cnpj["cliente_id"],
                "cliente_nome"      => $cnpj["cliente"],
                "turno_id"          => $turno["turno_id"] ?? null,
                "session_token"     => $token,
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
        $valorHora              = $this->request->getPost("valorHora");
        $clienteID              = base64_decode($cliente64);

        $senha                  = password_hash($senha, PASSWORD_BCRYPT);
        $valorHora              = str_replace(".", "", $valorHora);
        $valorHora              = str_replace(",", ".", $valorHora);


        try {
            $resposta           = $this->contaM->cadastrar($usuario, $senha, $userNome, $clienteID, $valorHora);
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
