<?php

namespace App\Domain\Objects\Message;

interface MessageRepository
{
    public const string TYPE_PRIVATE = 'private';
    public const string TYPE_GROUP = 'group';
    public const array MESSAGE_TYPE = [self::TYPE_PRIVATE, self::TYPE_GROUP];
    public function findMessagesFromToId(int $to, int $from, ?string $type = null): array;

    public function findMessagesToGroupId(int $to): array;

}