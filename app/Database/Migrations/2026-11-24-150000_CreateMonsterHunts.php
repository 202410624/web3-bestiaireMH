<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMonsterHunts extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'monster_id' => ['type' => 'INT'],
            'action' => ['type' => 'VARCHAR', 'constraint' => 12],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'monster_id', 'action']);
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('monster_id', 'monsterinfos', 'id', '', 'CASCADE');
        $this->forge->createTable('monster_hunts');
    }

    public function down()
    {
        $this->forge->dropTable('monster_hunts', true);
    }
}
