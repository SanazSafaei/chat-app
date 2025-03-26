<?php

namespace App\Domain\UseCase\Media;

use App\Domain\Objects\Media\Media;
use App\Domain\Objects\Media\MediaRepository;
use App\Domain\Objects\Message\MessageRepository;
use App\Domain\UseCase\UseCase;
use App\Domain\Validators\MediaAccessValidator;
use Webmozart\Assert\Assert;

class ViewMedia extends UseCase
{
    private int $mediaId;
    private MediaRepository $mediaRepository;
    private MessageRepository $messageRepository;
    private int $userId;

    public function __construct(
        int $mediaId,
        int $userId,
        MediaRepository $mediaRepository,
        MessageRepository $messageRepository
    ) {
        $this->userId = $userId;
        $this->mediaId = $mediaId;
        $this->mediaRepository = $mediaRepository;
        $this->messageRepository = $messageRepository;
        parent::__construct();
    }
    public function execute(): Media
    {
        return $this->mediaRepository->findById($this->mediaId);
    }

    protected function validateData()
    {
        MediaAccessValidator::hasViewAccess(
            $this->userId,
            $this->mediaId,
            $this->messageRepository
        );
    }
}
