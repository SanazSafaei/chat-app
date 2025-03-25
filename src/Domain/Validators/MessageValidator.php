<?php

declare(strict_types=1);

namespace App\Domain\Validators;

use App\Domain\Objects\Message\Message;
use App\Domain\DomainException\InvalidTypeError;
use App\Domain\Objects\Message\MessageRepository;

class MessageValidator
{
    public static function validate(Message $message): void
    {
        if (!in_array($message->getMessageType(), MessageRepository::MESSAGE_TYPES)) {
            throw new InvalidTypeError('Invalid type value. Allowed values are: private_message, group_message');
        }
    }
}