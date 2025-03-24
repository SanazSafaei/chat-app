<?php

namespace App\Domain\Objects\Group;

use App\Domain\Objects\DomainObject;

interface GroupMemberRepository
{
    public const string ROLE_MEMBER = 'member';
    public const string ROLE_ADMIN = 'admin';

    public const array ROLES = [
        self::ROLE_MEMBER,
        self::ROLE_ADMIN
    ];

    public function findGroupMembers(int $groupId): array;
    public function findUserGroups(int $userId): array;
    public function getByUserIdAndGroupId(int $userId, int $groupId): ?DomainObject;

    public function deleteByUserIdAndGroupId(int $userId, int $groupId): void;

    public function insert(DomainObject $groupMember): DomainObject;
}
