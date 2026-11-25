<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function index(): string
    {
        $test = new \App\Models\Test();
        $tableMonsterinfo = $test->where('speciesid', 1)->findAll();

        return view('pages/home', [
            'title'             => lang('Site.brand') . ' — ' . lang('Site.photos'),
            'active'            => 'photos',
            'tableMonsterinfo'  => $tableMonsterinfo,
        ]);
    }

    public function photos(): string
    {
        return $this->index();
    }

    public function videos(): string
    {
        return view('pages/videos', [
            'title'  => lang('Site.brand') . ' — ' . lang('Site.videos'),
            'active' => 'videos',
        ]);
    }

    public function photoDetail(int $id): string
    {
        return view('pages/photo_detail', [
            'title'  => lang('Site.brand') . ' — ' . lang('Site.photos'),
            'active' => 'photos',
            'id'     => $id,
        ]);
    }

    public function videoDetail(int $id): string
    {
        return view('pages/video_detail', [
            'title'  => lang('Site.brand') . ' — ' . lang('Site.videos'),
            'active' => 'videos',
            'id'     => $id,
        ]);
    }

    public function about(): string
    {
        return view('pages/about', [
            'title'  => lang('Site.brand') . ' — ' . lang('Site.about'),
            'active' => 'about',
        ]);
    }

    public function contact(): string
    {
        return view('pages/contact', [
            'title'  => lang('Site.brand') . ' — ' . lang('Site.contact'),
            'active' => 'contact',
        ]);
    }

    public function contactSubmit()
    {
        $rules = [
            'name'    => 'required|min_length[2]',
            'email'   => 'required|valid_email',
            'message' => 'required|min_length[10]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', lang('Site.contactInvalid'));
        }

        return redirect()->to(site_url('contact'))->with('success', lang('Site.contactSuccess'));
    }
}
