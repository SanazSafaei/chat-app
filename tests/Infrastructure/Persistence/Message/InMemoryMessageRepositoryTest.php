<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Message;

use App\Domain\Objects\Message\Message;
use App\Domain\Objects\Message\MessageRepository;
use App\Infrastructure\Persistence\Message\InMemoryMessageRepository;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use Slim\Logger;
use Tests\Infrastructure\Persistence\FakeDB;
use TypeError;

class InMemoryMessageRepositoryTest extends TestCase
{
    public function tearDown(): void
    {
        (new FakeDB())->deleteDB();
        parent::tearDown();
    }
    public function testFindAll()
    {
        $now = new \DateTime();
        $message1 = new Message(1, 1, 2, MessageRepository::TYPE_PRIVATE,'Hello World', '', $now);
        $message2 = new Message(2, 2, 1, MessageRepository::TYPE_PRIVATE,'How are you?', '', $now);

        $repository = new InMemoryMessageRepository(new FakeDB());

        foreach ([$message1, $message2] as $message) {
            $repository->insert($message);
        }

        $this->assertCount(2, $repository->findAll());
    }

    public function testFindMessageFromToId()
    {
        $now = new \DateTime('2025-03-22T10:01:04');
        $message = new Message(1, 1, 2, MessageRepository::TYPE_PRIVATE,'Hello World', '', $now);

        $repository = new InMemoryMessageRepository(new FakeDB());
        $repository->insert($message);
        $result = $repository->findMessagesFromToId(2, 1, MessageRepository::TYPE_PRIVATE);

        $this->assertEquals($message, $result[0]);
    }

    public function testInsertMessage()
    {
        $repository = new InMemoryMessageRepository(new FakeDB());

        $now = new \DateTime();
        $message = new Message(1, 1, 2, MessageRepository::TYPE_PRIVATE,'Hello World', '', $now);
        $insertedMessage = $repository->insert($message);

        $this->assertNotNull($insertedMessage->getId());
        $this->assertSame('Hello World', $insertedMessage->getMessage());
    }
}