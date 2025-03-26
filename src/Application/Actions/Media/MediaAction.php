<?php

namespace App\Application\Actions\Media;

use App\Application\Actions\Action;
use App\Domain\Objects\Media\MediaRepository;
use App\Domain\Objects\Message\MessageRepository;
use Psr\Log\LoggerInterface;

abstract class MediaAction extends Action
{
    protected MediaRepository $mediaRepository;
    protected MessageRepository $messageRepository;

    public function __construct(
        LoggerInterface $logger,
        MediaRepository $mediaRepository,
        MessageRepository $messageRepository
    ) {
        parent::__construct($logger);
        $this->mediaRepository = $mediaRepository;
        $this->messageRepository = $messageRepository;
    }
}
