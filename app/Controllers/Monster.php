<?php

namespace App\Controllers;

use App\Libraries\MonsterCombatReference;
use App\Models\MonsterHuntModel;
use App\Models\Test;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Monster extends BaseController
{
    public function show(int $id): string
    {
        $test = new Test();
        $item = $test->findMonster($id);
        if ($item === null) {
            throw PageNotFoundException::forPageNotFound(lang('Site.monsterNotFound'));
        }

        // Chemins normalisés (sans /public/ ni .svg) -> servent sous public/img/
        // (les colonnes image/icon sont à la racine du résultat de findMonster)
        $image = $test->normalizeImage((string) ($item['image'] ?? ''));
        $userId = session()->get('user_id');
        $game = $this->request->getGet('game');

        return view('pages/monster_detail', [
            'title'   => lang('Site.brand') . ' — ' . $item['display_name'],
            'active'  => 'photos',
            'item'    => $item,
            'image'   => $image,
            'id'      => $id,
            'combat'  => MonsterCombatReference::forMonster($item['monster_name'], is_string($game) ? $game : null),
            'hunt'    => $userId === null ? null : (new MonsterHuntModel())->totals((int) $userId, $id),
            'huntActions' => MonsterHuntModel::ACTIONS,
        ]);
    }

    public function recordHunt(int $id): RedirectResponse
    {
        if ((new Test())->find($id) === null) {
            throw PageNotFoundException::forPageNotFound(lang('Site.monsterNotFound'));
        }

        $game = $this->request->getPost('game');
        $query = is_string($game) && MonsterCombatReference::supportsGame($game) ? '?game=' . rawurlencode($game) : '';
        $redirect = redirect()->to(site_url('monster/' . $id) . $query . '#suivi-chasse');
        $action = $this->request->getPost('action');
        if (! is_string($action) || ! isset(MonsterHuntModel::ACTIONS[$action])) {
            return $redirect->with('error', lang('Site.invalidAction'));
        }

        $saved = (new MonsterHuntModel())->insert([
            'user_id' => (int) session()->get('user_id'),
            'monster_id' => $id,
            'action' => $action,
        ], false);

        return $saved ? $redirect : $redirect->with('error', lang('Site.huntFailed'));
    }
}
