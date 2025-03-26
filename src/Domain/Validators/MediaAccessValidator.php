<?php

namespace App\Domain\Validators;

use App\Domain\DomainException\MediaAccessError;
use App\Domain\Objects\Message\MessageRepository;
use App\Domain\Objects\User\UserRepository;

class MediaAccessValidator
{
    public static function hasUploadAccess(int $userId, array $requestData, MessageRepository $repository): bool
    {
        $messages = $repository->findMessagesFromToId($userId, $requestData['destination_id'], $requestData['type']);
        if (empty($messages)) {
            throw new MediaAccessError('You should start a conversion first.');
        }
        return true;
    }

    public static function hasViewAccess(
        int $userId,
        int $mediaId,
        MessageRepository $repository
    ): bool {
        $messages = $repository->findMessageWithUserIdAndMediaId($userId, $mediaId);
        if (empty($messages)) {
            throw new MediaAccessError('You don\'t have access to this media.');
        }
        return true;
    }
}
