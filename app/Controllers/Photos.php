<?php

namespace App\Controllers;

use App\Models\PhotoModel;
use App\Models\Test;

class Photos extends BaseController
{
    public function index(): string
    {
        // Le formulaire de recherche de l'accueil envoie ?q=…
        $search = trim((string) ($this->request->getGet('q') ?? ''));

        $test = new Test();
        $tableMonsterinfo = $test->paginateMonsters(24, 'default', null, $search);

        return view('pages/home', [
            'title'            => $search !== '' ? lang('Site.brand') . ' — ' . $search : lang('Site.brand') . ' — ' . lang('Site.photos'),
            'active'           => 'photos',
            'tableMonsterinfo' => $tableMonsterinfo,
            'pager'            => $test->pager,
            'searchQuery'      => $search,
        ]);
    }

    public function show(int $id): string
    {
        $model = new PhotoModel();
        $item = $model->find($id);
        if ($item === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(lang('Site.photoNotFound'));
        }

        return view('pages/photo_detail', [
            'title'   => lang('Site.brand') . ' — ' . $item['title'],
            'active'  => 'photos',
            'item'    => $item,
            'related' => $model->related($id, 8),
        ]);
    }
}
