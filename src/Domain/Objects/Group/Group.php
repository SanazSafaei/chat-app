<?php

declare(strict_types=1);

namespace App\Domain\Objects\Group;

use App\Domain\Objects\DomainObject;
use DateTime;
use JsonSerializable;

class Group extends DomainObject implements JsonSerializable
{
    private string $name;
    private string $photo;
    private string $description;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private int $creatorId;

    public function __construct(
        ?int $id,
        string $name,
        string $photo,
        string $description,
        int $creatorIid,
        ?DateTime $createdAt,
        ?DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->photo = $photo;
        $this->description = $description;
        $this->creatorId = $creatorIid;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatorId(): int
    {
        return $this->creatorId;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt->format('Y-m-d H:i:s');
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt->format('Y-m-d H:i:s');
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'photo' => $this->photo,
            'description' => $this->description,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }

    public static function jsonDeserialize($values): Group
    {
        return new Group(
            $values['id'],
            $values['name'],
            $values['photo'],
            $values['description'],
            $values['creator_id'],
            new DateTime($values['created_at']),
            new DateTime($values['updated_at'])
        );
    }
}