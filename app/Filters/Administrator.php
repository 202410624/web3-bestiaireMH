<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Administrator implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! (new UserModel())->isAdministrator((int) session()->get('user_id'))) {
            return service('response')
                ->setStatusCode(403)
                ->setContentType('text/plain')
                ->setBody(lang('Site.adminRequired'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
