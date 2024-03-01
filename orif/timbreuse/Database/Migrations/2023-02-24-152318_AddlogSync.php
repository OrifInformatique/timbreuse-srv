<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddlogSync extends Migration
{
	public function up()
	{
		$field['date']['type'] = 'DATETIME';
		$field['date']['null'] = false;

		$field['id_badge']['type'] = 'BIGINT';
		$field['id_badge']['null'] = true;

		$field['inside']['type'] = 'TINYINT';
		$field['inside']['null'] = false;

		$field['id_log']['type'] = 'INT';
		$field['id_log']['auto_increment'] = true;
		$field['id_log']['null'] = false;

		$field['id_user']['type'] = 'INT';
		$field['id_user']['null'] = false;

		$field['date_badge']['type'] = 'DATETIME';
		$field['date_badge']['null'] = true;

		$field[0] = 'date_modif DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';

		$field['date_delete']['type'] = 'DATETIME';
		$field['date_delete']['null'] = true;

		$field['id_ci_user']['type'] = 'INT';
		$field['id_ci_user']['null'] = true;

		$this->forge->addField($field);
		$this->forge->addPrimaryKey('id_log');
		$this->forge->addForeignKey('id_badge', 'badge_sync', 'id_badge');
		$this->forge->addForeignKey('id_user', 'user_sync', 'id_user');
        $this->forge->createTable('log_sync');

        /* Be sure that procedure doesn't allready exists before creating it */
        $this->drop_procedure_insert_badge_and_user();
        $this->add_procedure_insert_badge_and_user();
	}

	public function down()
	{
        $this->drop_procedure_insert_badge_and_user();
		$this->forge->dropTable('log_sync');
	}

    public function add_procedure_insert_badge_and_user() {
        $sql = 
        'CREATE PROCEDURE `insert_badge_and_user`(_id_badge bigint,'
        .' '.'    _name text CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,'
        .' '.'    _surname text CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci) '
        .' '.' MODIFIES SQL DATA'
        .' '.'BEGIN'
        .' '.'    DECLARE EXIT HANDLER FOR SQLEXCEPTION'
        .' '.'    BEGIN'
        .' '.'        ROLLBACK;'
        .' '.'    END;'
        .' '.'    START TRANSACTION;'
        .' '.'    INSERT INTO `user_sync` (`name`, `surname`) VALUES (_name, '
        .'_surname);'
        .' '.'    INSERT INTO `badge_sync` (`id_badge`, `id_user`) VALUES'
        .' '.'    (_id_badge, (SELECT `id_user` FROM `user_sync` WHERE '
        .'`name` = _name AND'
        .' '.'    `surname` = _surname ORDER BY `id_user` DESC)) '
        .' '.'    ON DUPLICATE KEY UPDATE `id_badge`=_id_badge, `id_user`= '
        . '(SELECT `id_user`'
        .' '.'    FROM `user_sync` WHERE `name` = _name AND `surname` = '
        .'_surname'
        .' '.'    ORDER BY `id_user` DESC);'
        .' '.'    COMMIT;'
        .' '.'END';
        $this->db->query($sql);
    }

    public function drop_procedure_insert_badge_and_user() {
        $sql = 'DROP PROCEDURE IF EXISTS `insert_badge_and_user`;';
        $this->db->query($sql);
    }

}
