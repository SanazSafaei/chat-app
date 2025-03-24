<?php

declare(strict_types=1);

namespace App\Application\Actions\Group;

use App\Application\Actions\User\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpUnauthorizedException;

class ViewGroupAction extends GroupAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $this->validateUserIsLoggedIn();

        $groupId = (int) $this->resolveArg('id');

        $group = $this->groupRepository->findById($groupId);

        $this->logger->info("User of id `{$groupId}` was viewed.");

        return $this->respondWithData($group);
    }
}
