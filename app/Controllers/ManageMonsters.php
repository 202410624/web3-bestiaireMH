<?php

namespace App\Controllers;

use App\Models\Test;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class ManageMonsters extends BaseController
{
    private const PATH = 'gestion/monstres';

    public function index(): string
    {
        $search = $this->request->getGet('q');
        $search = is_string($search) ? trim($search) : '';
        $model = new Test();
        $monsters = $model->paginateMonsters(24, 'default', null, $search);

        return view('monsters/index', [
            'title' => lang('Site.management'),
            'active' => 'management',
            'monsters' => $monsters,
            'search' => $search,
            'pager' => $model->pager,
        ]);
    }

    public function create(): string
    {
        return $this->form();
    }

    public function store(): RedirectResponse
    {
        return $this->save();
    }

    public function edit(int $id): string
    {
        return $this->form($this->findMonster($id));
    }

    public function update(int $id): RedirectResponse
    {
        return $this->save($this->findMonster($id));
    }

    public function confirmDelete(int $id): string
    {
        return view('monsters/delete', [
            'title' => lang('Site.deleteMonster'),
            'active' => 'management',
            'monster' => $this->findMonster($id),
        ]);
    }

    public function delete(int $id): RedirectResponse
    {
        $this->findMonster($id);
        if (! (new Test())->deleteMonster($id)) {
            return redirect()->to(site_url(self::PATH . '/' . $id . '/supprimer'))
                ->with('error', lang('Site.deleteFailed'));
        }

        return redirect()->to(site_url(self::PATH))->with('success', lang('Site.deleted'));
    }

    private function findMonster(int $id): array
    {
        $monster = (new Test())->findForManagement($id);
        if ($monster === null) {
            throw PageNotFoundException::forPageNotFound(lang('Site.monsterNotFound'));
        }

        return $monster;
    }

    private function form(?array $monster = null): string
    {
        return view('monsters/form', [
            'title' => $monster === null ? lang('Site.addMonster') : lang('Site.editMonster'),
            'active' => 'management',
            'monster' => $monster,
            'species' => (new Test())->speciesOptions(),
        ]);
    }

    private function save(?array $existing = null): RedirectResponse
    {
        $id = $existing === null ? null : (int) $existing['id'];
        $name = $this->request->getPost('name');
        $speciesId = $this->request->getPost('speciesid');
        $data = [
            'name' => is_string($name) ? trim($name) : '',
            'speciesid' => is_string($speciesId) ? $speciesId : '',
        ];
        $rules = [
            'name' => 'required|max_length[100]',
            'speciesid' => 'required|is_natural_no_zero|is_not_unique[monsterspecies.id]',
        ];
        $formPath = self::PATH . ($id === null ? '/ajouter' : '/' . $id . '/modifier');
        if (! $this->validateData($data, $rules)) {
            return redirect()->to(site_url($formPath))->with('monsterInput', $data)
                ->with('error', lang('Site.invalidMonster'));
        }
        $storedName = $data['name'] === ($existing['display_name'] ?? null) ? $existing['name'] : $data['name'];
        if (! (new Test())->saveMonster($storedName, (int) $data['speciesid'], $id)) {
            return redirect()->to(site_url($formPath))->with('monsterInput', $data)
                ->with('error', lang('Site.saveFailed'));
        }

        $result = $id === null ? lang('Site.monsterAdded') : lang('Site.monsterUpdated');
        return redirect()->to(site_url(self::PATH . '?q=' . rawurlencode($data['name'])))->with('success', $result);
    }
}
