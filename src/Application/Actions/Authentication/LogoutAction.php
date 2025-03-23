<?php

namespace App\Application\Actions\Authentication;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

class LogoutAction extends Action
{
    protected function action(): Response
    {
        $this->request->withHeader('Authorization', '');
        $this->request->withHeader('Cookie', '');
        return $this->response->withStatus(200);
    }
}