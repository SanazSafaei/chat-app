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
        $user = (new CreateUser($data))->execute();
        $baseUri = $_SERVER['HTTP_HOST'];
        $logger = new \Slim\Logger();
        $logger->log('info', 'USER ID : ' . $user->getId());
        $token = JwtManager::encode(JwtManager::getPayload($user->getId(), $user->getUsername()));
        return $this->response
            ->withHeader('Location', 'http://' . $baseUri . '/users/' . $user->getId())
            ->withHeader('Authorization', 'Bearer '.$token)
            ->withStatus(302);
    }
}
