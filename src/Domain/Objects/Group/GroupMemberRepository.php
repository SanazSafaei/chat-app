<?php

namespace App\Domain\Objects\Group;

interface GroupMemberRepository
{
    public const ROLE_MEMBER = 'member';
    public const ROLE_ADMIN = 'admin';

    public const ROLES = [
        self::ROLE_MEMBER,
        self::ROLE_ADMIN
    ];

    public function findGroupMembers(int $groupId): array;
    public function findUserGroups(int $userId): array;
}