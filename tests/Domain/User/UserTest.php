<?php

declare(strict_types=1);

namespace Tests\Domain\User;

use App\Domain\Objects\User\User;
use DateTime;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function userProvider(): array
    {
        $now = new DateTime();
        return [
            [1, 'bill.gates', "12345", 'Bill', 'Gates','bill@gate.com', "test/photo", $now, $now, $now],
            [2, 'steve.jobs', "12345", 'Steve', 'Jobs', 'bill@gate.com', "test/photo", $now, $now, $now],
            [3, 'mark.zuckerberg', "12345", 'Mark', 'Zuckerberg', 'bill@gate.com', "test/photo", $now, $now, $now],
            [4, 'evan.spiegel', "12345", 'Evan', 'Spiegel', 'bill@gate.com', "test/photo", $now, $now, $now],
            [5, 'jack.dorsey', "12345", 'Jack', 'Dorsey', 'bill@gate.com', "test/photo", $now, $now, $now],
        ];
    }

    /**
     * @dataProvider userProvider
     * @param int    $id
     * @param string $username
     * @param string $firstName
     * @param string $lastName
     */
    public function testGetters(
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
        $user = new User($id, $username, $password, $firstName, $lastName, $email, $photo, $lastSeen, $createdAt, $updatedAt);

        $this->assertEquals($id, $user->getId());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($firstName, $user->getFirstName());
        $this->assertEquals($lastName, $user->getLastName());
    }

    /**
     * @dataProvider userProvider
     * @param int    $id
     * @param string $username
     * @param string $firstName
     * @param string $lastName
     */
    public function testJsonSerialize(
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
        $user = new User(
            $id,
            $username,
            $password,
            $firstName,
            $lastName,
            $email,
            $photo,
            $lastSeen,
            $createdAt,
            $updatedAt
        );

        $expectedPayload = json_encode([
            'id' => $id,
            'username' => $username,
            'password' => $password,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'photo' => $photo,
            'last_seen' => $lastSeen->format('Y-m-d H:i:s'),
            'created_at' => $createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $updatedAt->format('Y-m-d H:i:s'),
        ]);

        $this->assertEquals($expectedPayload, json_encode($user));
    }
}
