<?php

declare(strict_types=1);

namespace App\Application\Actions\Media;

use App\Domain\UseCase\Media\ViewMedia;
use Psr\Http\Message\ResponseInterface as Response;

class ViewMediaAction extends MediaAction
{
    protected function action(): Response
    {
        $this->validateUserIsLoggedIn();

        $mediaId = (int) $this->resolveArg('id');

        $media = (new ViewMedia(
            $mediaId,
            $this->getUserId(),
            $this->mediaRepository,
            $this->messageRepository
        ))->execute();
        $this->logger->info("Media of id `{$mediaId}` was viewed by user {$this->getUserId()}.");

        $this->response->getBody()->write(file_get_contents($media->getPath()));
        return $this->response->withHeader('Content-Type', $media->getFileType());
    }
}
