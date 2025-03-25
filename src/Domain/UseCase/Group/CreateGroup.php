<?php

namespace App\Domain\UseCase\Group;

use App\Domain\Objects\Group\Group;
use App\Domain\Objects\Group\GroupRepository;
use App\Domain\UseCase\UseCase;
use Webmozart\Assert\Assert;
use DateTime;

class CreateGroup extends UseCase
{
    private array $groupData;
    private GroupRepository $groupRepository;

    public function __construct(array $groupData, GroupRepository $groupRepository)
    {
        parent::__construct();
        $this->groupData = $groupData;
        $this->groupRepository = $groupRepository;
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

    protected function validateData(): void
    {
        Assert::keyExists($this->groupData, 'name', 'Group name is required.');
        Assert::stringNotEmpty($this->groupData['name'], 'Group name cannot be empty.');
        Assert::keyExists($this->groupData, 'created_by', 'Creator ID is required.');
        Assert::integer($this->groupData['created_by'], 'Creator ID must be an integer.');
    }

}
