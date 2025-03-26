<?php

declare(strict_types=1);

namespace App\Domain\Objects\Message;

use App\Domain\Objects\DomainObject;
use App\Domain\Validators\MessageValidator;
use DateTime;
use DateTimeInterface;
use JsonSerializable;

class Message extends DomainObject implements JsonSerializable
{
    private int $from;
    private int $to;
    private string $messageType;
    private ?string $message;
    private ?string $media;
    private ?DateTime $createdAt;

    public function __construct(
        ?int $id,
        int $from,
        int $to,
        string $messageType,
        ?DateTime $createdAt,
        ?string $message,
        ?string $media = ''
    ) {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
        $this->messageType = $messageType;
        $this->message = $message;
        $this->media = $media;
        $this->createdAt = $createdAt;
        MessageValidator::validate($this);
    }

    public function getFrom(): int
    {
        return $this->from;
    }

    public function getTo(): int
    {
        return $this->to;
    }

    public function getMessageType(): string
    {
        return $this->messageType;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'from_id' => $this->getFrom(),
            'to_id' => $this->getTo(),
            'type' => $this->getMessageType(),
            'message' => $this->getMessage(),
            'media' => $this->getMedia(),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }

    public static function jsonDeserialize($values): Message
    {
        return new self(
            $values['id'] ?? null,
            $values['from_id'],
            $values['to_id'],
            $values['type'],
            isset($values['created_at']) ? new DateTime($values['created_at']) : null,
            $values['message'],
            $values['media']
        );
    }
}
