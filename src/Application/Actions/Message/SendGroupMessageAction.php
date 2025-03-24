<?php

namespace App\Application\Actions\Message;

use App\Domain\UseCase\Message\SendMessage;
use Psr\Http\Message\ResponseInterface as Response;

class SendGroupMessageAction extends MessageAction
{
    protected function action(): Response
    {
        $this->validateUserIsLoggedIn();

        $senderId = $this->getUserId();

        $data = $this->getFormData();
        $data['from'] = $senderId;
        $data['to'] = (int) $this->resolveArg('id');
        $data['type'] = $this->messageRepository::TYPE_GROUP;

        $messages = (new SendMessage($data, $this->messageRepository))->execute();
        return $this->respondWithData($messages);
    }
}
