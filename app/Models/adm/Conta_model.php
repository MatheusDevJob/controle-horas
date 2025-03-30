<?php

namespace App\Models\adm;

use CodeIgniter\Model;

final class Conta_model extends Model
{
    public function countAllUser($clienteID, $search = null): int
    {
        $db = $this->db->table('usuarios');
        $db->where("cliente_fk", $clienteID);
        if (!empty($search)) $db->like('nome', $search);
        return $db->countAllResults();
    }

    public function getAllUser(array $params): array
    {
        $db = $this->db->table('usuarios u');
        $db->select("
            TO_BASE64(u.user_id) as user_id,
            u.user_nome,
            IF(MAX(t.aberto) = 1, 'Sim', 'Não') AS turno
        ");
        $db->join('turnos t', 't.user_fk = u.user_id', 'left');
        $db->where('u.cliente_fk', $params['clienteID']);
        if (!empty($params['search']))
            $db->like('u.user_nome', $params['search']);
        $db->groupBy('u.user_id');

        if ($params['order_by'] === 'turno')
            $db->orderBy("turno", $params['order_dir']);
        else
            $db->orderBy("u." . $params['order_by'], $params['order_dir']);

        $db->limit($params['length'], $params['start']);
        return $db->get()->getResultArray();
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
