<?php

declare(strict_types=1);

namespace App\Domain\Objects\Group;

use App\Domain\Objects\DomainObject;
use App\Domain\Objects\Message\MessageRepository;
use DateTime;
use Exception;
use JsonSerializable;
class GroupMember extends DomainObject implements JsonSerializable
{

    private int $userId;
    private int $groupId;
    private string $role;

    public function __construct(
        ?int $id,
        int $userId,
        int $groupId,
        string $role = GroupMemberRepository::ROLE_MEMBER
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->groupId = $groupId;
        $this->role = $role;
        $this->validateInput();
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'groupId' => $this->groupId,
            'role' => $this->role,
        ];
    }

    public static function jsonDeserialize($values): DomainObject
    {
        return new self(
            $values['id'] ?? null,
            $values['userId'],
            $values['groupId'],
            $values['role']
        );
    }

    public function validateInput(): void
    {
        if (!in_array($this->role, GroupMemberRepository::ROLES)) {
            throw new Exception('Invalid type value. Allowed values are: private_message, group_message');
        }
    }
}