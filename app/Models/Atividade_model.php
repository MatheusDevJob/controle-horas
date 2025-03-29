<?php

namespace App\Models;

use CodeIgniter\Model;

final class Atividade_model extends Model
{
    function iniciar_turno($clienteID, $projetoID, $userID)
    {
        try {
            $this->db->table("turnos")
                ->set("cliente_fk",                 $clienteID)
                ->set("projeto_fk",                 $projetoID)
                ->set("user_fk",                    $userID)
                ->set("inicio_turno",               date("Y-m-d H:i:s"))
                ->insert();

            $erro = $this->db->error();
            if (!empty($erro['code'])) throw new \Exception('Erro: ' . $erro['message'], $erro['code']);

            return [
                "status"        => true,
                "msg"           => "Turno inicado.",
                "id"            => $this->db->insertID()
            ];
        } catch (\Exception $e) {
            log_message("error", $e->getMessage(), [$clienteID, $projetoID, $userID]);
            return ["status" => false, "msg" => $e->getMessage()];
        }
    }

    function finalizar_turno($userID)
    {
        try {
            $this->db->table("turnos")
                ->set("aberto",                 0)
                ->set("fim_turno",              date("Y-m-d H:i:s"))
                ->where("turno_id",             $userID)
                ->update();

            $erro = $this->db->error();
            if (!empty($erro['code'])) throw new \Exception('Erro: ' . $erro['message'], $erro['code']);

            return ["status" => true, "msg" => "Turno finalizado."];
        } catch (\Exception $e) {
            log_message("error", $e->getMessage(), [$userID]);
            return ["status" => false, "msg" => $e->getMessage()];
        }
    }

    function iniciar_atividade($dataHora, $turnoID)
    {
        try {
            $this->db->table("atividades")
                ->set("turno_fk",               $turnoID)
                ->set("inicio_atividade",       $dataHora)
                ->insert();

            $erro = $this->db->error();
            if (!empty($erro['code'])) throw new \Exception('Erro: ' . $erro['message'], $erro['code']);

            return ["status" => true, "msg" => "Atividade iniciada."];
        } catch (\Exception $e) {
            log_message("error", $e->getMessage(), [$turnoID, $dataHora]);
            return ["status" => false, "msg" => $e->getMessage()];
        }
    }

    function concluir_atividade($dataHora, $desc, $turnoID)
    {
        try {
            $this->db->table("atividades")
                ->set("fim_atividade",          $dataHora)
                ->set("descricao",              $desc)
                ->where("turno_fk",             $turnoID)
                ->update();

            $erro = $this->db->error();
            if (!empty($erro['code'])) throw new \Exception('Erro: ' . $erro['message'], $erro['code']);

            return ["status" => true, "msg" => "Atividade finalizada."];
        } catch (\Exception $e) {
            log_message("error", $e->getMessage(), [$turnoID, $dataHora]);
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
}
