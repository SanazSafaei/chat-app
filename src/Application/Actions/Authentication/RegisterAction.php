<?php

namespace App\Application\Actions\Authentication;

use App\Application\Actions\Action;
use App\Domain\UseCase\Authentication\JwtManager;
use App\Domain\UseCase\User\CreateUser;
use Psr\Http\Message\ResponseInterface as Response;

class RegisterAction extends Action
{
    protected function action(): Response
    {
        $data = $this->getFormData();
        list($user, $token) = (new CreateUser($data))->execute();
        $baseUri = $_SERVER['HTTP_HOST'];
        return $this->response
            ->withHeader('Location', 'http://' . $baseUri . '/users/' . $user->getId())
            ->withHeader('Authorization', 'Bearer '.$token)
            ->withHeader('set-cookie', 'token='.$token. '; HttpOnly; Path=/')
            ->withStatus(302);
    }
}
