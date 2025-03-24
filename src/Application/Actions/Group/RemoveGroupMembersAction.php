<?php

declare(strict_types=1);

namespace App\Application\Actions\Group;

use App\Domain\UseCase\Group\AddGroupMember;
use App\Domain\UseCase\Group\RemoveGroupMember;
use Psr\Http\Message\ResponseInterface as Response;

class RemoveGroupMembersAction extends GroupAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $this->validateUserIsLoggedIn();

        $groupId = (int) $this->resolveArg('id');

        $data = $this->getFormData();
        $data['group_id'] = $groupId;
        $data['requested_by'] = $this->getUserId();

        (new removeGroupMember($data, $this->groupMemberRepository))->execute();

        $this->logger->info("Group members of id `{$groupId}` was viewed by user `{$this->getUserId()}`.");

        return $this->respondWithData(['User Removed.']);
    }
}
