<?php

namespace App\Controllers;

use App\Models\VideoModel;

class Videos extends BaseController
{
    public function index(): string
    {
        $model = new VideoModel();
        $query = $this->request->getGet('q') ?? '';
        $items = $query !== '' ? $model->search((string) $query) : $model->all();

        return view('pages/videos', [
            'title'  => lang('Site.brand') . ' — ' . lang('Site.videos'),
            'active' => 'videos',
            'items'  => $items,
        ]);
    }

    public function show(int $id): string
    {
        $model = new VideoModel();
        $item = $model->find($id);
        if ($item === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(lang('Site.videoNotFound'));
        }

        return view('pages/video_detail', [
            'title'   => lang('Site.brand') . ' — ' . $item['title'],
            'active'  => 'videos',
            'item'    => $item,
            'related' => $model->related($id, 8),
        ]);
    }
}