<?php

namespace App\Models;

use CodeIgniter\Model;

final class Conta_model extends Model
{
    function cadastrar($usuario, $senha, $userNome, $clienteID)
    {
        try {
            $this->db->table("usuarios")
                ->set("usuario",            $usuario)
                ->set("senha",              $senha)
                ->set("user_nome",          $userNome)
                ->set("cliente_fk",         $clienteID)
                ->set("data_registro",      date("Y-m-d H:i:s"))
                ->insert();

            $erro = $this->db->error();
            if (!empty($erro['code'])) throw new \Exception('Erro: ' . $erro['message'], $erro['code']);

            return ["status" => true, "msg" => "Usuário $usuario cadastrado com sucesso."];
        } catch (\Exception $e) {
            log_message("error", $e->getMessage(), [$usuario, $senha, $userNome, $clienteID]);
            return ["status" => false, "msg" => $e->getMessage()];
        }
    }

    function getUserByID($userID)
    {
        return $this->db->table("usuarios")
            ->select("
                user_id,
                user_nome,
                usuario,
                senha,
                ativo,
                tipo_usuario_fk,
                session_token
            ")
            ->where("user_id", $userID)
            ->where("ativo", 1)
            ->get()->getRowArray();
    }

    function getContaByUser($usuario)
    {
        return $this->db->table("usuarios")
            ->select("
                user_id,
                user_nome,
                usuario,
                senha,
                ativo,
                tipo_usuario_fk,
                session_token
            ")
            ->where("usuario", $usuario)
            ->where("ativo", 1)
            ->get()->getRowArray();
    }

    function get_turno_aberto($userID)
    {
        return $this->db->table("turnos")
            ->select("turno_id")
            ->where("aberto", 1)
            ->where("user_fk", $userID)
            ->get()->getRowArray();
    }

    function atualizar(int $userID, array $novos_dados): bool
    {
        return $this->db->table("usuarios")
            ->set($novos_dados)
            ->where("user_id", $userID)
            ->update();
    }
}
