<?php

namespace App\Models;

use CodeIgniter\Model;

final class Atividade_model extends Model
{
    function get_turno_aberta($turnoID)
    {
        return $this->db->table("turnos")
            ->where("turno_id",             $turnoID)
            ->where("fim_turno IS NULL")
            ->get()->getRowArray();
    }

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

    function finalizar_turno($userID, $data, $horasTrabalhadas, $valorTurno)
    {
        try {
            $this->db->table("turnos")
                ->set("aberto",                 0)
                ->set("fim_turno",              $data)
                ->set("horas_trabalhadas",      $horasTrabalhadas)
                ->set("valor_turno",            $valorTurno)
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

    function get_atividade_aberta($turnoID)
    {
        return $this->db->table("atividades")
            ->where("turno_fk",             $turnoID)
            ->where("fim_atividade IS NULL")
            ->get()->getRowArray();
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

    function concluir_atividade($dataHora, $desc, $turnoID, $horasTrabalhadas, $valorHora, $valorAtividade)
    {
        try {
            $this->db->table("atividades")
                ->set("fim_atividade",          $dataHora)
                ->set("descricao",              $desc)
                ->set("valor_hora",             $valorHora)
                ->set("horas_trabalhadas",      $horasTrabalhadas)
                ->set("valor_atividade",        $valorAtividade)
                ->where("turno_fk",             $turnoID)
                ->where("fim_atividade IS NULL")
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

    function get_atividades_turno($turnoID)
    {
        return $this->db->table("atividades")
            ->select("
                atividade_id,
                descricao,
                DATE_FORMAT(inicio_atividade, '%d/%m/%Y %H:%i:%s') as inicio_atividade,
                DATE_FORMAT(fim_atividade, '%d/%m/%Y %H:%i:%s') as fim_atividade,
                turno_fk
            ")
            ->where("turno_fk",         $turnoID)
            ->get()->getResultArray();
    }
}
