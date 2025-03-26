<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Group;

use App\Domain\Objects\Group\Group;
use App\Infrastructure\Persistence\Group\InMemoryGroupRepository;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use Slim\Logger;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Tests\Infrastructure\Persistence\FakeDB;
use function FastRoute\cachedDispatcher;

class InMemoryGroupRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        (new FilesystemAdapter())->reset();
    }
    public function tearDown(): void
    {
        (new FakeDB())->deleteDB();
        (new FilesystemAdapter())->reset();
        parent::tearDown();
    }

    public function testFindAll()
    {
        $now =  new \DateTime('2025-03-22T10:01:04');
        $group1 = new Group(1, 'Group 1', '', 'Description 1', 1, $now, $now);
        $group2 = new Group(2, 'Group 2', '', 'Description 2', 2, $now, $now);

        $repository = new InMemoryGroupRepository(new FakeDB());

        foreach ([$group1, $group2] as $group) {
            $repository->insert($group);
        }

        $this->assertCount(2, $repository->findAll());
    }

    public function testFindGroupById()
    {
        $now =  new \DateTime('2025-03-22T10:01:04');
        $group = new Group(1, 'Group 1', '', 'Description 1', 1, $now, $now);

        $repository = new InMemoryGroupRepository(new FakeDB());
        $repository->insert($group);
        $result = $repository->findById(1);

        $this->assertEquals($group, $result);
    }

    public function testFindGroupByIdThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        (new FakeDB())->deleteDB();
        $repository = (new InMemoryGroupRepository(new FakeDB()));
        $repository->resetCacheKeys('id', 1);
        (new FilesystemAdapter())->reset();
        $t = $repository->findById(1);
        $logger = new Logger();
        $logger->log('info', 'this ----> '.$t->getId());
    }

    public function testInsertGroup()
    {
        $repository = new InMemoryGroupRepository(new FakeDB());

        $now = new \DateTime('2025-03-22T10:01:04');
        $group = new Group(1, 'Group 1', '', 'Description 1', 1, $now, $now);
        $insertedGroup = $repository->insert($group);

        $this->assertNotNull($insertedGroup->getId());
        $this->assertSame('Group 1', $insertedGroup->getName());
    }
}