<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\User;

use App\Domain\Objects\User\User;
use App\Domain\Objects\User\UserNotFoundException;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DateTime;
use Tests\Infrastructure\Persistence\FakeDB;
use Tests\TestCase;

class InMemoryUserRepositoryTest extends TestCase
{

    public function tearDown(): void
    {
        (new FakeDB())->deleteDB();
        parent::tearDown();
    }
    public function testFindAll()
    {
        $now = new DateTime('2025-03-22T10:01:04');
        $user = new User(1, "bill.gates", "12345", "Bill", "Gates", 'bill@gate.com', "test/photo", $now, $now, $now);
        (new FakeDB())->deleteDB();
        $userRepository = new InMemoryUserRepository(new FakeDB());
        $userRepository->insert($user);

        $this->assertEquals([$user], $userRepository->findAll());
    }

    public function testFindAllUsersByDefault()
    {
        $now = new DateTime('2025-03-22T10:01:04');
        $users = [
            1 => new User(1, 'bill.gates', "12345", 'Bill', 'Gates', 'bill@gate.com', "test/photo", $now, $now, $now),
            2 => new User(2, 'steve.jobs', "12345", 'Steve', 'Jobs', 'bill@gate.com', "test/photo", $now, $now, $now),
            3 => new User(3, 'mark.zuckerberg', "12345", 'Mark', 'Zuckerberg', 'bill@gate.com', "test/photo", $now, $now, $now),
            4 => new User(4, 'evan.spiegel', "12345", 'Evan', 'Spiegel', 'bill@gate.com', "test/photo", $now, $now, $now),
            5 => new User(5, 'jack.dorsey', "12345", 'Jack', 'Dorsey', 'bill@gate.com', "test/photo", $now, $now, $now),
        ];
        (new FakeDB())->deleteDB();

        $userRepository = new InMemoryUserRepository(new FakeDB());
        foreach ($users as $user) {
            $userRepository->insert($user);
        }

        $this->assertEquals(array_values($users), $userRepository->findAll());
    }

    public function testFindUserOfId()
    {
        $now = new DateTime('2025-03-22T10:01:04');
        $user = new User(1, 'bill.gates', "12345", 'Bill', 'Gates', 'bill@gate.com', "test/photo", $now, $now, $now);

        $userRepository = new InMemoryUserRepository(new FakeDB());
        $userRepository->insert($user);

        $this->assertEquals($user, $userRepository->findUserOfId(1));
    }

    public function testFindUserOfIdThrowsNotFoundException()
    {
        $userRepository = new InMemoryUserRepository(new FakeDB());
        $this->expectException(UserNotFoundException::class);
        $userRepository->findUserOfId(1);
    }
}
