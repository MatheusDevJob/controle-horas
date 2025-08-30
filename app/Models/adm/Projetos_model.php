<?php

namespace App\Models\adm;

use CodeIgniter\Model;

final class Projetos_model extends Model
{

    public function countAllProjetosCliente($clienteID, $search = null): int
    {
        $db = $this->db->table('projetos p');
        $db->where("p.cliente_fk", $clienteID);
        if (!empty($search)) $db->like('p.projeto', $search);
        return $db->countAllResults();
    }

    public function getAllProjetosCliente(array $params): array
    {
        $db = $this->db->table('projetos p');
        $db->select("
            TO_BASE64(p.projeto_id) as projeto_id,
            DATE_FORMAT(p.data_registro, '%d/%m/%Y') as data_registro,
            p.projeto,
            p.ativo
        ");
        $db->where('p.cliente_fk', $params['clienteID']);
        if (!empty($params['search'])) $db->like('p.projeto', $params["search"]);
        $db->orderBy($params['order_by'], $params['order_dir']);
        $db->limit($params['length'], $params['start']);
        return $db->get()->getResultArray();
    }

    function cadastrar($projeto, $clienteID, $dataRegistro, $userID, $userNome, $tipoUsuarioID)
    {
        $this->db->transStart();

        $db = $this->db->table("projetos")
            ->set([
                "projeto"               => $projeto,
                "cliente_fk"            => $clienteID,
                "data_registro"         => $dataRegistro,
            ]);
        $sql = $db->getCompiledInsert(false);
        $db->insert();

        $erro = $this->db->error();
        if (!empty($erro['code'])) {
            log_message("error", 'Erro: ' . $erro['message'] . " SQL => $sql " . " Código erro => " . $erro['code']);
            $this->db->transRollback();
            return false;
        }

        $auditoria = json_encode([
            "projeto"                       => $projeto,
            "cliente_fk"                    => $clienteID,
            "data_registro"                 => $dataRegistro,
        ], JSON_UNESCAPED_UNICODE);

        $db = $this->db->table("auditoria_projetos")
            ->set([
                "operacao"                  => "cadastro",
                "dados"                     => $auditoria,
                "quem_fez"                  => $userID,
                "tipo_quem_fez"             => $tipoUsuarioID,
                "projeto_fk"                => $this->db->insertID(),
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

    function mudaStatusProjeto($projetoID, $clienteID, $status, $data, $userID, $userNome, $tipoUsuarioID)
    {
        $this->db->transStart();

        $db = $this->db->table("projetos")->set("ativo", $status)->where("projeto_id", $projetoID);
        $sql = $db->getCompiledInsert(false);
        $db->update();

        $erro = $this->db->error();
        if (!empty($erro['code'])) {
            log_message("error", 'Erro: ' . $erro['message'] . " SQL => $sql " . " Código erro => " . $erro['code']);
            $this->db->transRollback();
            return false;
        }

        $auditoria = json_encode([
            "ativo"                         => $status,
            "cliente_id"                    => $clienteID,
        ], JSON_UNESCAPED_UNICODE);

        $db = $this->db->table("auditoria_projetos")
            ->set([
                "operacao"                  => "atualização",
                "dados"                     => $auditoria,
                "data_evento"               => $data,
                "quem_fez"                  => $userID,
                "tipo_quem_fez"             => $tipoUsuarioID,
                "projeto_fk"                => $projetoID,
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
