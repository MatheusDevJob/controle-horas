<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBaseTables extends Migration
{
    protected array $tableAttr = ['ENGINE' => 'InnoDB'];

    public function up()
    {
        /**
         * TIPO_USUARIO
         */
        $this->forge->addField([
            'tipo_id'   => ['type' => 'TINYINT', 'unsigned' => false, 'auto_increment' => true],
            'tipo_nome' => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => false],
        ]);
        $this->forge->addKey('tipo_id', true);
        $this->forge->createTable('tipo_usuario', true, $this->tableAttr);

        /**
         * CLIENTES
         */
        $this->forge->addField([
            'cliente_id'    => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'cnpj'          => ['type' => 'VARCHAR', 'constraint' => 14, 'null' => false],
            'cliente'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'ativo'         => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false],
            'data_cadastro' => ['type' => 'DATE', 'null' => false],
        ]);
        $this->forge->addKey('cliente_id', true);
        $this->forge->addUniqueKey('cnpj');
        $this->forge->createTable('clientes', true, $this->tableAttr);

        /**
         * USUARIOS
         */
        $this->forge->addField([
            'user_id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'usuario'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false], // login
            'user_nome'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'senha'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'valor_hora'      => ['type' => 'DECIMAL', 'constraint' => '10,2','null' => false],
            'ativo'           => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1, 'null' => false],
            'data_registro'   => ['type' => 'DATETIME', 'null' => false],
            'tipo_usuario_fk' => ['type' => 'TINYINT', 'null' => false, 'default' => 2],
            'cliente_fk'      => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'session_token'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addKey('user_id', true);
        // UNIQUE(usuario, cliente_fk)
        if (method_exists($this->forge, 'addUniqueKey')) {
            $this->forge->addUniqueKey(['usuario', 'cliente_fk']);
        }
        $this->forge->addKey('tipo_usuario_fk');
        $this->forge->createTable('usuarios', true, $this->tableAttr);

        // FK usuarios -> tipo_usuario / clientes
        $this->db->query('ALTER TABLE `usuarios`
            ADD CONSTRAINT `usuarios_tipo_usuario_FK` FOREIGN KEY (`tipo_usuario_fk`) REFERENCES `tipo_usuario` (`tipo_id`) ON UPDATE CASCADE,
            ADD CONSTRAINT `usuarios_clientes_FK`      FOREIGN KEY (`cliente_fk`)      REFERENCES `clientes` (`cliente_id`) ON UPDATE CASCADE
        ');

        /**
         * PROJETOS
         */
        $this->forge->addField([
            'projeto_id'   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'projeto'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'cliente_fk'   => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'data_registro' => ['type' => 'DATE', 'null' => false],
            'ativo'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1, 'null' => false],
        ]);
        $this->forge->addKey('projeto_id', true);
        $this->forge->addKey('projeto'); // índice auxiliar
        if (method_exists($this->forge, 'addUniqueKey')) {
            $this->forge->addUniqueKey(['projeto', 'cliente_fk']);
        }
        $this->forge->createTable('projetos', true, $this->tableAttr);

        // FK projetos -> clientes
        $this->db->query('ALTER TABLE `projetos`
            ADD CONSTRAINT `projetos_clientes_FK` FOREIGN KEY (`cliente_fk`) REFERENCES `clientes` (`cliente_id`) ON UPDATE CASCADE
        ');

        /**
         * TURNOS
         */
        $this->forge->addField([
            'turno_id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'cliente_fk'       => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'user_fk'          => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'projeto_fk'       => ['type' => 'INT',    'unsigned' => true, 'null' => false],
            'inicio_turno'     => ['type' => 'DATETIME', 'null' => false],
            'fim_turno'        => ['type' => 'DATETIME', 'null' => true],
            'aberto'           => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1, 'null' => false],
            'horas_trabalhadas' => ['type' => 'FLOAT', 'null' => true],
            'valor_turno'      => ['type' => 'FLOAT', 'null' => true],
        ]);
        $this->forge->addKey('turno_id', true);
        $this->forge->addKey(['cliente_fk', 'user_fk', 'projeto_fk']);
        $this->forge->createTable('turnos', true, $this->tableAttr);

        // FKs turnos
        $this->db->query('ALTER TABLE `turnos`
            ADD CONSTRAINT `turnos_clientes_FK` FOREIGN KEY (`cliente_fk`) REFERENCES `clientes` (`cliente_id`) ON UPDATE CASCADE,
            ADD CONSTRAINT `turnos_usuarios_FK` FOREIGN KEY (`user_fk`)    REFERENCES `usuarios` (`user_id`)  ON UPDATE CASCADE,
            ADD CONSTRAINT `turnos_projetos_FK` FOREIGN KEY (`projeto_fk`) REFERENCES `projetos` (`projeto_id`) ON UPDATE CASCADE
        ');

        /**
         * ATIVIDADES
         */
        $this->forge->addField([
            'atividade_id'     => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'descricao'        => ['type' => 'TEXT', 'null' => false],
            'inicio_atividade' => ['type' => 'DATETIME', 'null' => false],
            'fim_atividade'    => ['type' => 'DATETIME', 'null' => true],
            'turno_fk'         => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'valor_hora'       => ['type' => 'FLOAT', 'null' => true],
            'horas_trabalhadas' => ['type' => 'FLOAT', 'null' => true],
            'valor_atividade'  => ['type' => 'FLOAT', 'null' => true],
        ]);
        $this->forge->addKey('atividade_id', true);
        $this->forge->addKey('turno_fk');
        $this->forge->createTable('atividades', true, $this->tableAttr);

        // FK atividades -> turnos
        $this->db->query('ALTER TABLE `atividades`
            ADD CONSTRAINT `atividades_turnos_FK` FOREIGN KEY (`turno_fk`) REFERENCES `turnos` (`turno_id`) ON UPDATE CASCADE
        ');

        /**
         * AUDITORIA_USUARIOS
         */
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'operacao'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'dados'         => ['type' => 'LONGTEXT', 'null' => false],
            'quem_fez'      => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false, 'comment' => 'usuário que realizou a mudança'],
            'tipo_quem_fez' => ['type' => 'TINYINT', 'null' => false, 'comment' => 'tipo de conta que realizou a ação'],
            'user_fk'       => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false, 'comment' => 'usuário que sofreu as mudanças'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_fk', 'tipo_quem_fez', 'quem_fez']);
        $this->forge->createTable('auditoria_usuarios', true, $this->tableAttr);

        // FKs auditoria_usuarios
        $this->db->query('ALTER TABLE `auditoria_usuarios`
            ADD CONSTRAINT `auditoria_usuarios_tipo_usuario_FK` FOREIGN KEY (`tipo_quem_fez`) REFERENCES `tipo_usuario` (`tipo_id`) ON UPDATE CASCADE,
            ADD CONSTRAINT `auditoria_usuarios_usuarios_FK`     FOREIGN KEY (`user_fk`)      REFERENCES `usuarios` (`user_id`)   ON UPDATE CASCADE,
            ADD CONSTRAINT `auditoria_usuarios_usuarios_FK_1`   FOREIGN KEY (`quem_fez`)     REFERENCES `usuarios` (`user_id`)   ON UPDATE CASCADE
        ');

        /**
         * Seed mínimo de tipos de usuário
         */
        $this->db->table('tipo_usuario')->ignore(true)->insertBatch([
            ['tipo_id' => 1, 'tipo_nome' => 'Admin'],
            ['tipo_id' => 2, 'tipo_nome' => 'Usuário'],
        ]);
    }

    public function down()
    {
        // drop na ordem inversa
        $this->forge->dropTable('auditoria_usuarios', true);
        $this->forge->dropTable('atividades', true);
        $this->forge->dropTable('turnos', true);
        $this->forge->dropTable('projetos', true);
        $this->forge->dropTable('usuarios', true);
        $this->forge->dropTable('clientes', true);
        $this->forge->dropTable('tipo_usuario', true);
    }
}
