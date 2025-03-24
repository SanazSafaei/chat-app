<?php

namespace App\Application\Actions\Message;

use App\Domain\UseCase\Message\SendMessage;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpUnauthorizedException;

class SendPrivateMessageAction extends MessageAction
{
    protected function action(): Response
    {
        $this->validateUserIsLoggedIn();

        $owner = $this->getUserId();

        $data = $this->getFormData();
        $data['from'] = $owner;
        $data['to'] = (int) $this->resolveArg('id');
        $data['type'] = $this->messageRepository::TYPE_PRIVATE;

        $messages = (new SendMessage($data, $this->messageRepository))->execute();
        return $this->respondWithData($messages);
    }
}
