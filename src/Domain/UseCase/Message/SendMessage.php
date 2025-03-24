<?php

namespace App\Domain\UseCase\Message;

use App\Domain\Objects\Message\Message;
use App\Domain\Objects\Message\MessageRepository;
use App\Infrastructure\Persistence\Message\InMemoryMessageRepository;
use DateTime;
use Exception;
use Webmozart\Assert\Assert;

class SendMessage
{
    private array $messageData;

    public function __construct(array $messageData)
    {
        $this->messageData = $messageData;
        $this->validateData();
    }

    public function execute(): array
    {
        $now = new DateTime();

        //username should be unique!
        $message = new Message(
            null,
            $this->messageData['from'],
            $this->messageData['to'],
            $this->messageData['type'],
            $this->messageData['message'] ?? '',
            $this->messageData['media'] ?? '',
            $now
        );
        $message = (new InMemoryMessageRepository())->insert($message);

        return $message->jsonSerialize();
    }

    private function validateData(): void
    {
        Assert::keyExists($this->messageData, 'to', 'Destination[to] field is mandatory.');
        Assert::keyExists($this->messageData, 'from', 'Origin[from] field is mandatory.');
        if (!isset($this->messageData['message']) && !isset($this->messageData['media'])) {
            throw new Exception('Message or Media should contain at least one value.');
        }
        Assert::keyExists($this->messageData, 'type', 'Type field is mandatory.');
        Assert::inArray(
            $this->messageData['type'],
            MessageRepository::MESSAGE_TYPES,
            'Message Type field can only be ' . implode(', ', MessageRepository::MESSAGE_TYPES)
        );
    }
}
