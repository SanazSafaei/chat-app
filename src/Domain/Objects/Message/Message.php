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
    private string $message;
    private string $media;
    private ?DateTime $createdAt;

    public function __construct(
        ?int $id,
        int $from,
        int $to,
        string $messageType,
        string $message,
        string $media,
        ?DateTime $createdAt
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

    public function getMedia(): string
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
            'id' => $this->id,
            'from_id' => $this->from,
            'to_id' => $this->to,
            'type' => $this->messageType,
            'message' => $this->message,
            'media' => $this->media,
            'created_at' => $this->createdAt?->format(DateTimeInterface::ATOM),
        ];
    }

    public static function jsonDeserialize($values): Message
    {
        return new self(
            $values['id'] ?? null,
            $values['from_id'],
            $values['to_id'],
            $values['type'],
            $values['message'],
            $values['media'],
            isset($values['createdAt']) ? new DateTime($values['createdAt']) : null
        );
    }
}
