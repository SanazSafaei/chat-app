<?php

declare(strict_types=1);

namespace App\Domain\Validators;

use App\Domain\Objects\Group\GroupMember;
use App\Domain\DomainException\InvalidTypeError;
use App\Domain\Objects\Group\GroupMemberRepository;

class GroupMemberValidator
{
    public static function validate(GroupMember $groupMember): void
    {
        if (!in_array($groupMember->getRole(), GroupMemberRepository::ROLES)) {
            throw new InvalidTypeError('Invalid role value. Allowed values are: ' . implode(', ', GroupMemberRepository::ROLES));
        }
    }
}