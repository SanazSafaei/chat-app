<?php

namespace App\Domain\UseCase\Group;

use App\Domain\Objects\Group\GroupMember;
use App\Domain\Objects\Group\GroupMemberRepository;
use App\Domain\UseCase\UseCase;
use Webmozart\Assert\Assert;

class AddGroupMember extends UseCase
{
    private array $memberData;
    private GroupMemberRepository $groupMemberRepository;

    public function __construct(array $memberData, GroupMemberRepository $groupMemberRepository)
    {
        $this->memberData = $memberData;
        $this->groupMemberRepository = $groupMemberRepository;
        parent::__construct();
    }

    public function execute(): GroupMember
    {
        $groupMember = new GroupMember(
            null,
            $this->memberData['user_id'],
            $this->memberData['group_id'],
            $this->memberData['role']
        );

        /** @var GroupMember $groupMember */
        $groupMember = $this->groupMemberRepository->insert($groupMember);

        return $groupMember;
    }

    protected function validateData(): void
    {
        Assert::keyExists($this->memberData, 'group_id', 'Group ID is required.');
        Assert::integer($this->memberData['group_id'], 'Group ID must be an integer.');
        Assert::keyExists($this->memberData, 'user_id', 'User ID is required.');
        Assert::integer($this->memberData['user_id'], 'User ID must be an integer.');
        Assert::keyExists($this->memberData, 'role', 'Role is required.');
        Assert::inArray($this->memberData['role'], GroupMemberRepository::ROLES, 'Role is required.');
    }
}