<?php

namespace App\Controllers;

use App\Models\Test;

class Home extends BaseController
{
	public function index(): string
	{
		$test = new Test();

		return view('pages/home', [
			'title'            => lang('Site.brand') . ' — ' . lang('Site.monsters'),
			'active'           => 'photos',
			// 24 cartes = 6 lignes de 4 en xl
			'tableMonsterinfo' => $test->paginateMonsters(24),
			// $test->pager est rempli par paginate() : la vue s'en sert pour les liens.
			// $test->pager est rempli par paginate() : la vue s'en sert pour les liens.
			'pager'            => $test->pager,
		]);
	}
}
