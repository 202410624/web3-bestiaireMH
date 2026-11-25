<?php

namespace App\Commands;

use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SetAdministrator extends BaseCommand
{
    protected $group = 'Bestiaire';
    protected $name = 'bestiaire:admin';
    protected $description = 'Accorde ou retire le rôle administrateur à un compte existant.';
    protected $usage = 'bestiaire:admin <courriel> <ajouter|retirer>';

    public function run(array $params): int
    {
        [$email, $action] = array_pad($params, 2, '');
        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || ! in_array($action, ['ajouter', 'retirer'], true)) {
            CLI::error('Utilisation : php spark ' . $this->usage);
            return EXIT_ERROR;
        }

        $model = new UserModel();
        $user = $model->select('id')->where('email', strtolower($email))->first();
        if ($user === null) {
            CLI::error('Aucun compte ne correspond à ce courriel.');
            return EXIT_ERROR;
        }

        if (! $model->setAdministrator((int) $user['id'], $action === 'ajouter')) {
            CLI::error('Impossible de modifier le rôle administrateur.');
            return EXIT_ERROR;
        }

        CLI::write($action === 'ajouter' ? 'Rôle administrateur accordé.' : 'Rôle administrateur retiré.', 'green');
        return EXIT_SUCCESS;
    }
}
