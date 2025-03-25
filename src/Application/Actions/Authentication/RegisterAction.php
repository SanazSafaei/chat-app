<?php

namespace App\Application\Actions\Authentication;

use App\Application\Actions\User\UserAction;
use App\Domain\UseCase\User\CreateUser;
use Psr\Http\Message\ResponseInterface as Response;

class RegisterAction extends UserAction
{
    protected function action(): Response
    {
        $data = $this->getFormData();
        list($user, $token) = (new CreateUser($data, $this->userRepository))->execute();
        $baseUri = $_SERVER['HTTP_HOST'];
        return $this->response
            ->withHeader('Location', 'http://' . $baseUri . '/users/' . $user->getId())
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->withHeader('set-cookie', 'token=' . $token . '; HttpOnly; Path=/')
            ->withStatus(302);
    }
}
