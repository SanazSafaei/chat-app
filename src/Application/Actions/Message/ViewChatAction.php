<?php

namespace App\Application\Actions\Message;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpUnauthorizedException;

class ViewChatAction extends MessageAction
{
    protected function action(): Response
    {
        $guest = (int) $this->resolveArg('id');
        $owner = $this->getUserId();

        if (!$owner) {
            throw new HttpUnauthorizedException($this->request);
        }

        $messagesListData = $this->messageRepository->findMessagesFromToId(
            $guest,
            $owner,
            $this->messageRepository::TYPE_PRIVATE
        );

        return $this->respondWithData($messagesListData);
    }
}
