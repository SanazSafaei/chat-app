<?php

namespace App\Domain\UseCase\Group;

use App\Domain\Objects\Group\Group;
use App\Domain\Objects\Group\GroupRepository;
use Webmozart\Assert\Assert;
use DateTime;

class CreateGroup
{
    private array $groupData;
    private GroupRepository $groupRepository;

    public function __construct(array $groupData, GroupRepository $groupRepository)
    {
        $this->groupData = $groupData;
        $this->groupRepository = $groupRepository;
        $this->validateData();
    }

    public function execute(): Group
    {
        $now = new DateTime();
        $group = new Group(
            null,
            $this->groupData['name'],
            $this->groupData['photo'] ?? '',
            $this->groupData['description'] ?? '',
            $this->groupData['created_by'],
            $now,
            $now
        );

        /** @var Group $group */
        $group = $this->groupRepository->insert($group);

        return $group;
    }

    private function validateData(): void
    {
        Assert::keyExists($this->groupData, 'name', 'Group name is required.');
        Assert::stringNotEmpty($this->groupData['name'], 'Group name cannot be empty.');
        Assert::keyExists($this->groupData, 'created_by', 'Creator ID is required.');
        Assert::integer($this->groupData['created_by'], 'Creator ID must be an integer.');
    }

}
