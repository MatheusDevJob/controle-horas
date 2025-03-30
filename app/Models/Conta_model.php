<?php

namespace App\Models;

use CodeIgniter\Model;

final class Conta_model extends Model
{
    function cadastrar($usuario, $senha, $userNome, $clienteID, $valorHora)
    {
        try {
            $this->db->table("usuarios")
                ->set("usuario",            $usuario)
                ->set("senha",              $senha)
                ->set("user_nome",          $userNome)
                ->set("cliente_fk",         $clienteID)
                ->set("valor_hora",         $valorHora)
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

    public function countAllAtividadesUser($clienteID, $userID, $search = null): int
    {
        $db = $this->db->table('atividades a');
        $db->join('turnos t', 't.turno_id = a.turno_fk');
        $db->join('projetos p', 't.projeto_fk = p.projeto_id');
        $db->where("t.cliente_fk", $clienteID);
        $db->where('t.user_fk', $userID);
        if (!empty($search)) {
            $db->groupStart();
            $db->like('a.descricao', $search);
            $db->orLike('p.projeto', $search);
            $db->groupEnd();
        }
        return $db->countAllResults();
    }

    public function getAllAtividadesUser(array $params): array
    {
        $db = $this->db->table('atividades a');
        $db->select("
            TO_BASE64(a.atividade_id) as atividade_id,
            COALESCE(a.descricao, '-') as descricao,
            DATE_FORMAT(a.inicio_atividade, '%d/%m/%Y %H:%i:%s') as inicio_atividade,
            COALESCE(DATE_FORMAT(a.fim_atividade, '%d/%m/%Y %H:%i:%s'), '-') as fim_atividade,
            p.projeto,
            DATE_FORMAT(t.inicio_turno, '%d/%m/%Y %H:%i:%s') as inicio_turno,
            COALESCE(DATE_FORMAT(t.fim_turno, '%d/%m/%Y %H:%i:%s'), '-') as fim_turno,
        ");
        $db->join('turnos t', 't.turno_id = a.turno_fk');
        $db->join('projetos p', 't.projeto_fk = p.projeto_id');
        $db->where('t.cliente_fk', $params['clienteID']);
        $db->where('t.user_fk', $params['userID']);
        if (!empty($params['search'])) {
            $db->groupStart();
            $db->like('a.descricao', $params['search']);
            $db->orLike('p.projeto', $params['search']);
            $db->groupEnd();
        }
        $db->orderBy($params['order_by'], $params['order_dir']);
        $db->limit($params['length'], $params['start']);
        return $db->get()->getResultArray();
    }
}
