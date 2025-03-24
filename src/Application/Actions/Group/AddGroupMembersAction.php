<?php

declare(strict_types=1);

namespace App\Application\Actions\Group;

use App\Domain\UseCase\Group\AddGroupMember;
use Psr\Http\Message\ResponseInterface as Response;

class AddGroupMembersAction extends GroupAction
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
        $data['role'] = $data['role'] ?? $this->groupMemberRepository::ROLE_MEMBER;

        (new AddGroupMember($data, $this->groupMemberRepository))->execute();

        $this->logger->info("Group members of id `{$groupId}` was viewed by user `{$this->getUserId()}`.");

        return $this->respondWithData(['User added.']);
    }
}
