<?php

declare(strict_types=1);

namespace App\Application\Actions\Group;

use Psr\Http\Message\ResponseInterface as Response;

class ViewGroupMembersAction extends GroupAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $this->validateUserIsLoggedIn();

        $groupId = (int) $this->resolveArg('id');

        $groupMembers = $this->groupMemberRepository->findGroupMembers($groupId);

        $this->logger->info("Group members of id `{$groupId}` was viewed by user `{$this->getUserId()}`.");

        return $this->respondWithData($groupMembers);
    }
}
