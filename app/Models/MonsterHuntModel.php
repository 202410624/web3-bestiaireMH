<?php

namespace App\Models;

use CodeIgniter\Model;

class MonsterHuntModel extends Model
{
    public const ACTIONS = [
        'cart' => 'addCart',
        'capture' => 'addCapture',
        'kill' => 'addKill',
    ];

    protected $table = 'monster_hunts';
    protected $allowedFields = ['user_id', 'monster_id', 'action'];
    protected $validationRules = ['action' => 'required|in_list[cart,capture,kill]'];

    public function totals(int $userId, int $monsterId): array
    {
        $totals = array_fill_keys(array_keys(self::ACTIONS), 0);
        $rows = $this->select('action')->selectCount('id', 'total')
            ->where(['user_id' => $userId, 'monster_id' => $monsterId])
            ->groupBy('action')
            ->findAll();

        foreach ($rows as $row) {
            if (isset($totals[$row['action']])) {
                $totals[$row['action']] = (int) $row['total'];
            }
        }

        return $totals;
    }

    public function mostHunted(int $userId): array
    {
        return $this->ranking($userId, ['kill', 'capture']);
    }

    public function mostCarts(int $userId): array
    {
        return $this->ranking($userId, ['cart']);
    }

    private function ranking(int $userId, array $actions): array
    {
        return $this->select('monster_hunts.monster_id, monstername.name')
            ->selectCount('monster_hunts.id', 'total')
            ->select("SUM(CASE WHEN monster_hunts.action = 'kill' THEN 1 ELSE 0 END) AS kills", false)
            ->select("SUM(CASE WHEN monster_hunts.action = 'capture' THEN 1 ELSE 0 END) AS captures", false)
            ->join('monsterinfos', 'monsterinfos.id = monster_hunts.monster_id')
            ->join('monstername', 'monstername.id = monsterinfos.nameid')
            ->where('monster_hunts.user_id', $userId)
            ->whereIn('monster_hunts.action', $actions)
            ->groupBy(['monster_hunts.monster_id', 'monstername.name'])
            ->orderBy('total', 'DESC')
            ->orderBy('monstername.name', 'ASC')
            ->orderBy('monster_hunts.monster_id', 'ASC')
            ->findAll(10);
    }
}
