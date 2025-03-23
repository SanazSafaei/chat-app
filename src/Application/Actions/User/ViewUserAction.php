<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpUnauthorizedException;

class ViewUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');

        if ($this->getUserId() != $userId) {
            throw new HttpUnauthorizedException($this->request);
        }

        $user = $this->userRepository->findUserOfId($userId);
        $userData = $user->jsonSerialize();
        unset($userData['password']);
        $this->logger->info("User of id `{$userId}` was viewed.");

        return $this->respondWithData($userData);
    }
}
