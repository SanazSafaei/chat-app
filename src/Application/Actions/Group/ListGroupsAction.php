<?php

declare(strict_types=1);

namespace App\Application\Actions\Group;

use Psr\Http\Message\ResponseInterface as Response;

class ListGroupsAction extends GroupAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $this->validateUserIsLoggedIn();

        $groups = $this->groupRepository->findAll();

        $this->logger->info("Users list was viewed.");

        return $this->respondWithData($groups);
    }
}
