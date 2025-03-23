<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class ViewUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');

        if ($this->getUserId() != $userId) {
            return $this->respondWithData(['error' => 'Unauthorized'], 401);
        }

        $user = $this->userRepository->findUserOfId($userId);

        $this->logger->info("User of id `{$userId}` was viewed.");

        return $this->respondWithData($user->jsonSerialize());
    }
}
