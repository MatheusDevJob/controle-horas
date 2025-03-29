<?php

namespace App\Models;

use CodeIgniter\Model;

final class Projetos_model extends Model
{
    function getProjetos($clienteID)
    {
        return $this->db->table("projetos p")
            ->select("
                p.projeto_id,
                p.projeto,
            ")
            ->where("p.cliente_fk",             $clienteID)
            ->get()->getResultArray();
    }
}
