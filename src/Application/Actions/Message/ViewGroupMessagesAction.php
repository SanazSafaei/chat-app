<?php

declare(strict_types=1);

namespace App\Application\Actions\Message;

use Psr\Http\Message\ResponseInterface as Response;

class ViewGroupMessagesAction extends MessageAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $this->validateUserIsLoggedIn();

        $groupId = (int) $this->resolveArg('id');

        $allMessages = $this->messageRepository->findMessagesToGroupId($groupId);

        $this->logger->info("User of id `{$groupId}` was viewed.");

        return $this->respondWithData($allMessages);
    }
}
