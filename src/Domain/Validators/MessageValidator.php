<?php

declare(strict_types=1);

namespace App\Domain\Validators;

use App\Domain\Objects\Group\GroupMember;
use App\Domain\Objects\Group\GroupMemberRepository;
use App\Domain\Objects\Message\Message;
use App\Domain\Objects\Message\MessageRepository;
use Exception;

class MessageValidator
{
    public static function validate(Message $message): void
    {
        if (!in_array($message->getMessageType(), MessageRepository::MESSAGE_TYPES)) {
            throw new Exception('Invalid type value. Allowed values are: private_message, group_message');
        }
    }
}