<?php

namespace App\Models;

use CodeIgniter\Model;

final class Conta_model extends Model
{
    function cadastrar($usuario, $senha, $userNome)
    {
        try {
            $this->db->table("usuarios")
                ->set("usuario", $usuario)
                ->set("senha", $senha)
                ->set("user_nome", $userNome)
                ->insert();

            $erro = $this->db->error();
            if (!empty($erro['code'])) throw new \Exception('Erro: ' . $erro['message'], $erro['code']);

            return ["status" => true, "msg" => "Usuário $usuario cadastrado com sucesso."];
        } catch (\Exception $e) {
            log_message("error", $e->getMessage(), [$usuario, $senha, $userNome]);
            return ["status" => false, "msg" => $e->getMessage()];
        }
    }
}
