<?php

declare(strict_types=1);

namespace App\Domain\Objects\User;

use App\Domain\Objects\DomainObject;
use DateTime;
use JsonSerializable;
use ReturnTypeWillChange;

class User extends DomainObject implements JsonSerializable
{
    private string $username;

    private string $firstName;

    private string $lastName;
    private string $photo;
    private DateTime $lastSeen;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private string $password;
    private string $email;

    public function __construct(
        ?int $id,
        string $username,
        string $password,
        string $firstName,
        string $lastName,
        string $email,
        string $photo,
        ?DateTime $lastSeen,
        ?DateTime $createdAt,
        ?DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->username = strtolower($username);
        $this->password = $password;
        $this->firstName = ucfirst($firstName);
        $this->lastName = ucfirst($lastName);
        $this->email = $email;
        $this->photo = $photo;
        $this->lastSeen = $lastSeen;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
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

    #[ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
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
            $values['password'],
            $values['first_name'],
            $values['last_name'],
            $values['email'],
            $values['photo'],
            (new DateTime($values['last_seen'])),
            (new DateTime($values['created_at'])),
            (new DateTime($values['updated_at'])),
        )
        );
    }
}
