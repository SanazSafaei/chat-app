<?php

declare(strict_types=1);

namespace App\Application\Actions\Group;

use App\Domain\UseCase\Group\AddGroupMember;
use App\Domain\UseCase\Group\CreateGroup;
use Psr\Http\Message\ResponseInterface as Response;

class CreateGroupAction extends GroupAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $this->validateUserIsLoggedIn();

        $data = $this->getFormData();

        $data['created_by'] = $this->getUserId();

        $group = (new CreateGroup($data, $this->groupRepository))->execute();

        (new AddGroupMember(
            [
                'group_id' => $group->getId(),
                'user_id' => $this->getUserId(),
                'role' => $this->groupMemberRepository::ROLE_ADMIN
            ],
            $this->groupMemberRepository
        ))->execute();

        return $this->respondWithData($group);
    }
}
