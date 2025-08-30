<?php

namespace App\Models\adm;

use CodeIgniter\Model;

final class Conta_model extends Model
{
    public function countAllUser($clienteID, $search = null): int
    {
        $db = $this->db->table('usuarios');
        $db->where("cliente_fk", $clienteID);
        if (!empty($search)) $db->like('user_nome', $search);
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
}
