<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Group;

use App\Domain\Objects\Group\GroupMember;
use App\Domain\Objects\Group\GroupMemberRepository;
use App\Domain\Objects\Group\GroupMembersRepository;
use App\Domain\Objects\Group\GroupRepository;
use App\Domain\Objects\User\User;
use App\Infrastructure\Persistence\Group\InMemoryGroupMembersRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;
use Tests\Infrastructure\Persistence\FakeDB;

class InMemoryGroupMembersRepositoryTest extends TestCase
{
    public function tearDown(): void
    {
        (new FakeDB())->deleteDB();
        parent::tearDown();
    }

    public function testCountGroupMembers()
    {
        $member1 = new GroupMember(1, 1, 1, GroupMemberRepository::ROLE_MEMBER);
        $member2 = new GroupMember(2, 2, 1, GroupMemberRepository::ROLE_MEMBER);

        $repository = new InMemoryGroupMembersRepository(new FakeDB());

        foreach ([$member1, $member2] as $member) {
            $repository->insert($member);
        }

        $this->assertCount(2, $repository->findGroupMembers(1));
    }

    public function testFindMembersByGroupId()
    {
        $now = new \DateTime('2025-03-22T10:01:04');
        $user = new User(1, "bill.gates", "12345", "Bill", "Gates", 'bill@gate.com', "test/photo", $now, $now, $now);
        $userRepository = new InMemoryUserRepository(new FakeDB());
        $userRepository->insert($user);

        $member = new GroupMember(1, 1, 1,GroupMemberRepository::ROLE_MEMBER);
        $repository = new InMemoryGroupMembersRepository(new FakeDB());
        $repository->insert($member);
        $member->setUserData($user);
        unset($member->jsonSerialize()['user']->jsonSerialize()['password']);
        $result = $repository->findGroupMembers(1);

        $this->assertEquals($member, $result[0]);
    }

    public function testInsertMember()
    {
        $repository = new InMemoryGroupMembersRepository(new FakeDB());

        $member = new GroupMember(1, 1, 1, GroupMemberRepository::ROLE_MEMBER);
        $insertedMember = $repository->insert($member);

        $this->assertNotNull($insertedMember->getId());
        $this->assertSame(1, $insertedMember->getGroupId());
    }
}