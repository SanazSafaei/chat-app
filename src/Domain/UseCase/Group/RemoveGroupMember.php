<?php

namespace App\Domain\UseCase\Group;

use App\Domain\Objects\Group\GroupMember;
use App\Domain\Objects\Group\GroupMemberRepository;
use Webmozart\Assert\Assert;

class RemoveGroupMember
{
    private array $memberData;
    private GroupMemberRepository $groupMemberRepository;

    public function __construct(array $memberData, GroupMemberRepository $groupMemberRepository)
    {
        $this->memberData = $memberData;
        $this->groupMemberRepository = $groupMemberRepository;
        $this->validateData();
    }

    public function execute(): void
    {
        $this->groupMemberRepository->deleteByUserIdAndGroupId($this->memberData['user_id'], $this->memberData['group_id']);
    }

    private function validateData(): void
    {
        Assert::keyExists($this->memberData, 'group_id', 'Group ID is required.');
        Assert::integer($this->memberData['group_id'], 'Group ID must be an integer.');
        Assert::keyExists($this->memberData, 'user_id', 'User ID is required.');
        Assert::integer($this->memberData['user_id'], 'User ID must be an integer.');

        Assert::keyExists($this->memberData, 'requested_by', 'Requested User ID is required.');
        /** @var GroupMember $groupMember */
        $groupMember = $this->groupMemberRepository->getByUserIdAndGroupId($this->memberData['requested_by'], $this->memberData['group_id']);
        Assert::eq($groupMember->getRole(), GroupMemberRepository::ROLE_ADMIN, 'Only admin can remove group members.');
    }
}