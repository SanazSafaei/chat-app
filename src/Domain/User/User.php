<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\DomainObject;
use DateTime;
use JsonSerializable;

class User extends DomainObject implements JsonSerializable
{
    private ?int $id;

    private string $username;

    private string $firstName;

    private string $lastName;
    private string $photo;
    private DateTime $lastSeen;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        ?int $id,
        string $username,
        string $firstName,
        string $lastName,
        string $photo,
        ?DateTime $lastSeen,
        ?DateTime $createdAt,
        ?DateTime $updatedAt
    )
    {
        $this->id = $id;
        $this->username = strtolower($username);
        $this->firstName = ucfirst($firstName);
        $this->lastName = ucfirst($lastName);
        $this->photo = $photo;
        $this->lastSeen = $lastSeen;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function getLastSeen(): string
    {
        return $this->lastSeen->format('Y-m-d H:i:s');
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt->format('Y-m-d H:i:s');
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt->format('Y-m-d H:i:s');
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'photo' => $this->photo,
            'last_seen' => $this->lastSeen->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }

    public static function jsonDeserialize($values): User
    {
        return (new User(
                $values['id'],
                $values['username'],
                $values['first_name'],
                $values['last_name'],
                $values['photo'],
                (new DateTime($values['last_seen'])),
                (new DateTime($values['created_at'])),
                (new DateTime($values['updated_at'])),
            )
        );
    }
}
