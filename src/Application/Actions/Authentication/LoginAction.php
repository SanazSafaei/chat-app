<?php

namespace App\Application\Actions\Authentication;

use App\Application\Actions\Action;
use App\Domain\UseCase\User\LoginUser;
use Psr\Http\Message\ResponseInterface as Response;

class LoginAction extends Action
{
    protected function action(): Response
    {
        if ($this->getUserId()) {
            $this->respondWithData(['Already logged in'], 400);
        }

        $data = $this->getFormData();
        list($user, $token) = (new LoginUser($data))->execute();
        $baseUri = $_SERVER['HTTP_HOST'];
        return $this->response
            ->withHeader('Location', 'http://' . $baseUri . '/users/' . $user->getId())
            ->withHeader('Authorization', 'Bearer '.$token)
            ->withHeader('set-cookie', 'token='.$token. '; HttpOnly; Path=/')
            ->withStatus(302);
    }
}