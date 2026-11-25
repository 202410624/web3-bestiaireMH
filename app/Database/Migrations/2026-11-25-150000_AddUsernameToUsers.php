<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsernameToUsers extends Migration
{
    public function up(): void
    {
        $table = $this->db->protectIdentifiers('users', true);
        $this->db->query(
            "ALTER TABLE {$table} ADD COLUMN username VARCHAR(100) NULL, ADD UNIQUE KEY users_username_unique (username)",
        );

        // Les comptes dont le nom est ambigu peuvent continuer à se connecter par courriel.
        $this->db->query(
            "UPDATE {$table} AS utilisateur
             JOIN (
                 SELECT MIN(id) AS id
                 FROM {$table}
                 WHERE CHAR_LENGTH(TRIM(name)) >= 2 AND name NOT LIKE '%@%'
                 GROUP BY TRIM(name)
                 HAVING COUNT(*) = 1
             ) AS noms_uniques ON utilisateur.id = noms_uniques.id
             SET utilisateur.username = TRIM(utilisateur.name)",
        );
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'username');
    }
}
