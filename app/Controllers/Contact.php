<?php

namespace App\Controllers;

use App\Models\TeamModel;

class Contact extends BaseController
{
    public function index(): string
    {
        $model = new TeamModel();
        return view('pages/contact', [
            'title'  => lang('Site.brand') . ' — ' . lang('Site.contact'),
            'active' => 'contact',
            'team'   => $model->all(),
        ]);
    }

    public function submit()
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