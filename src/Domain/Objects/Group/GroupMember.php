<?php

declare(strict_types=1);

namespace App\Domain\Objects\Group;

use App\Domain\Objects\DomainObject;
use App\Domain\Objects\User\User;
use App\Domain\Validators\GroupMemberValidator;
use JsonSerializable;

class GroupMember extends DomainObject implements JsonSerializable
{
    private int $userId;
    private int $groupId;
    private string $role;
    private User $user;

    public function __construct(
        ?int $id,
        int $userId,
        int $groupId,
        string $role
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->groupId = $groupId;
        $this->role = $role;
        GroupMemberValidator::validate($this);
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

    public function setUserData(User $data): void
    {
        $this->user = $data;
    }

    public function getUserData(): User
    {
        return $this->user;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'user' => $this->user ?? '',
            'group_id' => $this->groupId,
            'role' => $this->role,
        ];
    }

    public static function jsonDeserialize($values): DomainObject
    {
        return new self(
            $values['id'] ?? null,
            $values['user_id'],
            $values['group_id'],
            $values['role']
        );
    }
}
