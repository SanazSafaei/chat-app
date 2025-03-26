<?php

namespace App\Application\Actions\Media;

use App\Domain\Objects\Media\Media;
use App\Domain\UseCase\Media\UploadMedia;
use http\Exception\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Logger;
use Slim\Psr7\UploadedFile;
use Webmozart\Assert\Assert;

class UploadMediaAction extends MediaAction
{
    protected function action(): Response
    {
        $this->validateUserIsLoggedIn();

        $files = $this->request->getUploadedFiles();
        $inputData = $this->getFormData();

        $logger = new Logger();
        foreach ($inputData as $key => $data) {
            $logger->log('info', '----->' . $key . ' ' . $data);
        }

        if (count($files) != 1) {
            throw new InvalidArgumentException('Only one file can be uploaded at a time.');
        }

        foreach ($files as $file) {
            /** @var UploadedFile $file */
            $media = (new UploadMedia(
                $file,
                $this->getUserId(),
                $inputData,
                $this->mediaRepository,
                $this->messageRepository
            ))
                ->execute();
        }
        return $this->respondWithData(['media_id' => $media->getId()]);
    }
}
