<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    public function connexion(): string|RedirectResponse
    {
        if (session()->has('user_id')) {
            return redirect()->to(site_url('profil'));
        }

        return view('pages/login', ['title' => lang('Site.brand') . ' — ' . lang('Site.login'), 'active' => 'login']);
    }

    public function traiterConnexion(): RedirectResponse
    {
        $identifiant = trim($this->postString('identifier'));
        $estUnCourriel = str_contains($identifiant, '@');
        $motDePasse = $this->postString('password');
        $regleIdentifiant = $estUnCourriel ? 'required|valid_email|max_length[254]' : 'required|max_length[100]';
        $formulaireValide = $this->validateData(
            ['identifier' => $identifiant],
            ['identifier' => $regleIdentifiant],
        );

        if ($formulaireValide && $this->isSupportedPassword($motDePasse)) {
            $utilisateur = (new UserModel())->trouverParIdentifiant($identifiant, $estUnCourriel);

            if ($utilisateur !== null && password_verify($motDePasse, $utilisateur['password_hash'])) {
                session()->regenerate(true);
                session()->set('user_id', (int) $utilisateur['id']);

                return redirect()->to(site_url('profil'));
            }
        }

        return redirect()->to(site_url('login'))->with('error', lang('Site.invalidLogin'));
    }

    public function inscription(): string
    {
        return view('pages/register', ['title' => lang('Site.brand') . ' — ' . lang('Site.register'), 'active' => 'register']);
    }

    public function traiterInscription(): RedirectResponse
    {
        $data = [
            'name' => trim($this->postString('name')),
            'email' => strtolower(trim($this->postString('email'))),
            'password' => $this->postString('password'),
        ];
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[254]',
            'password' => 'required|min_length[8]',
        ];
        if (! $this->validateData($data, $rules)
            || str_contains($data['name'], '@')
            || ! $this->isSupportedPassword($data['password'])) {
            return redirect()->to(site_url('register'))->with('error', lang('Site.invalidRegistration'));
        }
        if (! (new UserModel())->register($data['name'], $data['email'], $data['password'])) {
            return redirect()->to(site_url('register'))->with('error', lang('Site.identifierTaken'));
        }

        return redirect()->to(site_url('login'))->with('success', lang('Site.accountCreated'));
    }

    public function deconnexion(): RedirectResponse
    {
        session()->destroy();

        return redirect()->to(site_url('/'));
    }

    private function isSupportedPassword(string $password): bool
    {
        return $password !== '' && strlen($password) <= 72 && ! str_contains($password, "\0");
    }

    private function postString(string $key): string
    {
        $value = $this->request->getPost($key);

        return is_string($value) ? $value : '';
    }
}
