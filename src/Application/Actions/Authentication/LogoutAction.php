<?php

namespace App\Application\Actions\Authentication;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

class LogoutAction extends Action
{
    protected function action(): Response
    {
        $this->response->withHeader('Authorization', '');
        $this->response->withHeader('Cookie', '');
        return $this->response->withStatus(200);
    }
}