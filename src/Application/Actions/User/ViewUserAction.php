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
        $this->validateUserIsLoggedIn();

        $userId = (int) $this->resolveArg('id');

        $user = $this->userRepository->findUserOfId($userId);
        $userData = $user->jsonSerialize();
        $userData = $this->sanitiseUserData($userData);
        $this->logger->info("User of id `{$userId}` was viewed.");

        return $this->respondWithData($userData);
    }

    public static function sanitiseUserData(array $userData): array
    {
        unset($userData['password']);
        return $userData;
    }
}
