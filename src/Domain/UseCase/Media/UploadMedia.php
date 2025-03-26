<?php

namespace App\Domain\UseCase\Media;

use App\Domain\Objects\Media\Media;
use App\Domain\Objects\Media\MediaRepository;
use App\Domain\Objects\Message\MessageRepository;
use App\Domain\UseCase\UseCase;
use App\Domain\Validators\MediaAccessValidator;
use Slim\Psr7\UploadedFile;
use Webmozart\Assert\Assert;

class UploadMedia extends UseCase
{
    private UploadedFile $content;
    private MediaRepository $mediaRepository;
    private array $requestData;
    private int $userId;
    private MessageRepository $messageRepository;

    public function __construct(
        UploadedFile $content,
        int $userId,
        array $requestData,
        MediaRepository $mediaRepository,
        MessageRepository $messageRepository
    ) {
        $this->userId = $userId;
        $this->content = $content;
        $this->requestData = $requestData;
        $this->mediaRepository = $mediaRepository;
        $this->messageRepository = $messageRepository;
        parent::__construct();
    }
    public function execute(): Media
    {
        $path = $this->uploadMedia($this->content);
        $media = new Media(null, $this->content->getClientFilename(), $this->content->getClientMediaType(), $path);
        /** @var Media $media */
        $media = $this->mediaRepository->insert($media);
        return $media;
    }

    private function uploadMedia($media): string
    {
        $path = __DIR__ . '/../../../../var/Media/';
        $filename = $media->getClientFilename();
        $fileInfo = pathinfo($filename);
        $hash = hash('sha256', $filename . time());
        $filename = $fileInfo['filename'] . '_' . $hash . '.' . $fileInfo['extension'];
        $filePath = $path . $filename;
        $media->moveTo($filePath);
        return $filePath;
    }

    protected function validateData()
    {
        //is in conv?
        Assert::eq($this->content->getError(), UPLOAD_ERR_OK, 'Error uploading file.');
        Assert::lessThanEq($this->content->getSize(), 1000000, 'File should be less than 1MB.');
        Assert::inArray(
            $this->content->getClientMediaType(),
            $this->mediaRepository::MEDIA_TYPES,
            "File type should be an " . implode('or', $this->mediaRepository::MEDIA_TYPES)
        );
        Assert::keyExists($this->requestData, 'destination_id', 'destination_id should be provided');
        Assert::keyExists($this->requestData, 'type', 'type should be provided');
        MediaAccessValidator::hasUploadAccess($this->userId, $this->requestData, $this->messageRepository);
    }
}
