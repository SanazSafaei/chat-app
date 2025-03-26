<?php

namespace App\Domain\Objects\Message;

use App\Domain\Objects\DomainObject;

interface MessageRepository
{
    public const string TYPE_PRIVATE = 'private_message';
    public const string TYPE_GROUP = 'group_message';
    public const array MESSAGE_TYPES = [self::TYPE_PRIVATE, self::TYPE_GROUP];
    public function findMessagesFromToId(int $to, int $from, string $type): array;

    public function findMessagesToGroupId(int $to): array;

    public function findMessageOfMediaId(int $to, int $from, string $type, int $mediaId): array;
    public function findMessageWithUserIdAndMediaId(int $to, int $mediaId): array;

    public function insert(DomainObject $domain): DomainObject;
}
