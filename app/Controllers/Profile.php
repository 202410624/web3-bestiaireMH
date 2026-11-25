<?php

namespace App\Controllers;

use App\Models\MonsterHuntModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Profile extends BaseController
{
    public function index(): ResponseInterface
    {
        $userId = (int) session()->get('user_id');
        $user = (new UserModel())->select('name')->find($userId);
        if ($user === null) {
            session()->destroy();
            return redirect()->to(site_url('login'));
        }

        $hunts = new MonsterHuntModel();
        return $this->response
            ->setHeader('Cache-Control', 'private, no-store')
            ->setBody(view('pages/profile', [
                'title' => lang('Site.brand') . ' — ' . lang('Site.profile'),
                'active' => 'profile',
                'hunterName' => $user['name'],
                'mostHunted' => $hunts->mostHunted($userId),
                'mostCarts' => $hunts->mostCarts($userId),
            ]));
    }
}
