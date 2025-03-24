<?php

namespace App\Application\Actions\Message;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpUnauthorizedException;

class ViewChatAction extends MessageAction
{
    protected function action(): Response
    {
        $this->validateUserIsLoggedIn();

        $guest = (int) $this->resolveArg('id');
        $owner = $this->getUserId();

        $messagesListData = $this->messageRepository->findMessagesFromToId(
            $guest,
            $owner,
            $this->messageRepository::TYPE_PRIVATE
        );

        return $this->respondWithData($messagesListData);
    }
}
