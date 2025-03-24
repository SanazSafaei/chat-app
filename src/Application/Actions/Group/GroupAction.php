<?php

declare(strict_types=1);

namespace App\Application\Actions\Group;

use App\Application\Actions\Action;
use App\Domain\Objects\Group\GroupMemberRepository;
use App\Domain\Objects\Group\GroupRepository;
use App\Domain\Objects\Message\MessageRepository;
use Psr\Log\LoggerInterface;

abstract class GroupAction extends Action
{
    protected GroupRepository $groupRepository;
    protected GroupMemberRepository $groupMemberRepository;

    public function __construct(LoggerInterface $logger, GroupRepository $groupRepository, GroupMemberRepository $groupMemberRepository)
    {
        parent::__construct($logger);
        $this->groupRepository = $groupRepository;
        $this->groupMemberRepository = $groupMemberRepository;
    }
}
