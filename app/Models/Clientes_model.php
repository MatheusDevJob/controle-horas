<?php

namespace App\Models;

use CodeIgniter\Model;

final class Clientes_model extends Model
{
    function cadastrar($cliente)
    {
        try {
            $this->db->table("clientes")
                ->set("cliente", $cliente)
                ->set("data_registro", date("Y-m-d H:i:s"))
                ->insert();

            $erro = $this->db->error();
            if (!empty($erro['code'])) throw new \Exception('Erro: ' . $erro['message'], $erro['code']);

            return ["status" => true, "msg" => "Cliente $cliente cadastrado com sucesso."];
        } catch (\Exception $e) {
            log_message("error", $e->getMessage(), [$cliente]);
            return ["status" => false, "msg" => $e->getMessage()];
        }
    }

    function getClientes()
    {
        return $this->db->table("clientes")
            ->select("TO_BASE64(cliente_id) as cliente_id, cliente")
            ->where("ativo", 1)
            ->get()->getResultArray();
    }

    function getClienteByCnpj($cnpj): array
    {
        return $this->db->table("clientes")
            ->select("TO_BASE64(cliente_id) as cliente_id, cliente")
            ->where("cnpj",     $cnpj)
            ->where("ativo",    1)
            ->get()->getRowArray();
    }
}
