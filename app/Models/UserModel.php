<?php

namespace App\Models;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $allowedFields = ['name', 'username', 'email', 'password_hash'];

    public function register(string $name, string $email, string $password): bool
    {
        try {
            $result = $this->insert([
                'name' => $name,
                'username' => $name,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);
        } catch (DatabaseException $exception) {
            if ((int) $this->db->error()['code'] === 1062) {
                return false;
            }

            throw $exception;
        }

        if ($result !== false) {
            return true;
        }
        if ((int) $this->db->error()['code'] === 1062) {
            return false;
        }

        throw new DatabaseException('Impossible de créer le compte.');
    }

    public function trouverParIdentifiant(string $identifiant, bool $estUnCourriel): ?array
    {
        $colonne = $estUnCourriel ? 'email' : 'username';
        $valeur = $estUnCourriel ? strtolower($identifiant) : $identifiant;

        return $this->where($colonne, $valeur)->first();
    }

    public function isAdministrator(int $userId): bool
    {
        return $this->where(['id' => $userId, 'is_admin' => 1])->countAllResults() > 0;
    }

    public function setAdministrator(int $userId, bool $enabled): bool
    {
        // Le rôle est modifié depuis Spark. Les formulaires utilisent les champs autorisés du modèle.
        return $this->builder()->where('id', $userId)->update(['is_admin' => (int) $enabled]);
    }
}
