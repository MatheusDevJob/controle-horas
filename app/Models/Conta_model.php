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
        return $this->db->table("usuarios u")
            ->select("
                TO_BASE64(u.user_id) as user_id,
                u.user_nome,
                u.usuario,
                u.tipo_usuario_fk,
                u.session_token,
                u.ativo,
                u.valor_hora
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
                valor_hora,
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

    function atualizarToken(int $userID, array $novos_dados): bool
    {
        return $this->db->table("usuarios")
            ->set($novos_dados)
            ->where("user_id", $userID)
            ->update();
    }

    public function countAllAtividadesUser($clienteID, $userID, $search = null, $dataI = null, $dataF = null, $projetoID = null): int
    {
        $db = $this->db->table('atividades a');
        $db->join('turnos t', 't.turno_id = a.turno_fk');
        $db->join('projetos p', 't.projeto_fk = p.projeto_id');
        $db->where("t.cliente_fk", $clienteID);
        $db->where('t.user_fk', $userID);
        if ($dataI && $dataF) {
            $db->where("a.inicio_atividade >= ", $dataI);
            $db->where("a.inicio_atividade <= ", $dataF);
        }

        if ($projetoID)
            $db->where("t.projeto_fk", $projetoID);

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
            TIME_FORMAT(SEC_TO_TIME(a.horas_trabalhadas * 3600), '%H:%i:%s') AS horas_trabalhadas,
            CONCAT('R$ ', valor_hora) as valor_hora,
            CONCAT('R$ ', valor_atividade) as valor_atividade,
        ");
        $db->join('turnos t', 't.turno_id = a.turno_fk');
        $db->join('projetos p', 't.projeto_fk = p.projeto_id');
        $db->where('t.cliente_fk', $params['clienteID']);
        $db->where('t.user_fk', $params['userID']);
        if (!empty($params['dataI']) && !empty($params["dataF"])) {
            $db->where("a.inicio_atividade >= ", "{$params['dataI']} 00:00:00");
            $db->where("a.inicio_atividade <= ", "{$params["dataF"]} 23:59:59");
        }
        if (!empty($params["projetoID"]))
            $db->where("t.projeto_fk", $params["projetoID"]);
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

    function atualizaratualizar($userID, $userNome, $valorHora, $clienteID, $tipoUsuarioID): bool
    {
        $this->db->transStart();
        $db = $this->db->table("usuarios")
            ->set([
                "user_nome"                 => $userNome,
                "valor_hora"                => $valorHora,
            ])
            ->where("user_id", $userID);
        $sql = $db->getCompiledUpdate(false);
        $db->update();

        $erro = $this->db->error();
        if (!empty($erro['code'])) {
            log_message("error", 'Erro: ' . $erro['message'] . " SQL => $sql " . " Código erro => " . $erro['code']);
            $this->db->transRollback();
            return false;
        }

        $auditoria = json_encode([
            "user_nome"                     => $userNome,
            "valor_hora"                    => $valorHora,
            "cliente_id"                    => $clienteID
        ], JSON_UNESCAPED_UNICODE);

        $db = $this->db->table("auditoria_registro_horas.auditoria_usuarios")
            ->set([
                "operacao"                  => "atualização",
                "dados"                     => $auditoria,
                "usuario_fk"                => $userID,
                "usuario_nome"              => $userNome,
                "usuario_tipo_fk"           => $tipoUsuarioID,
                "user_fk"                => $this->db->insertID(),
            ]);
        $sql = $db->getCompiledInsert(false);
        $db->insert();

        $erro = $this->db->error();
        if (!empty($erro['code'])) {
            log_message("error", 'Erro: ' . $erro['message'] . " SQL => $sql " . " Código erro => " . $erro['code']);
            $this->db->transRollback();
            return false;
        }

        return $this->db->transComplete();
    }
}
