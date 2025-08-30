<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AuditoriaProjetos extends Migration
{
    protected array $tableAttr = ['ENGINE' => 'InnoDB'];
    public function up()
    {
        /**
         * AUDITORIA_projetos
         */
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'operacao'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'dados'         => ['type' => 'LONGTEXT', 'null' => false],
            'data_evento'   => ['type' => 'DATETIME', 'null' => true],
            'quem_fez'      => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false, 'comment' => 'usuário que realizou a mudança'],
            'tipo_quem_fez' => ['type' => 'TINYINT', 'null' => false, 'comment' => 'tipo de conta que realizou a ação'],
            'projeto_fk'    => ['type' => 'INT', 'unsigned' => true, 'null' => false, 'comment' => 'projeto que sofreu as mudanças'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_fk', 'tipo_quem_fez', 'quem_fez']);
        $this->forge->createTable('auditoria_projetos', true, $this->tableAttr);

        // FKs auditoria_projetos
        $this->db->query('ALTER TABLE `auditoria_projetos`
            ADD CONSTRAINT `auditoria_projetos_tipo_usuario_FK` FOREIGN KEY (`tipo_quem_fez`) REFERENCES `tipo_usuario` (`tipo_id`) ON UPDATE CASCADE,
            ADD CONSTRAINT `auditoria_projetos_projeto_FK`      FOREIGN KEY (`projeto_fk`)    REFERENCES `projetos` (`projeto_id`)  ON UPDATE CASCADE,
            ADD CONSTRAINT `auditoria_projetos_usuarios_FK`     FOREIGN KEY (`quem_fez`)      REFERENCES `usuarios` (`user_id`)     ON UPDATE CASCADE
        ');
    }

    public function down()
    {
        $this->forge->dropTable('auditoria_projetos', true);
    }
}
