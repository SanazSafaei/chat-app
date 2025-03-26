<?php

namespace App\Domain\Objects\Media;

use App\Domain\Objects\DomainObject;

class Media extends DomainObject
{
    private string $path;
    private string $fileName;
    private string $fileType;

    public function __construct(?int $id, ?string $fileName, string $fileType, string $path)
    {
        $this->id = $id;
        $this->fileName = $fileName;
        $this->fileType = $fileType;
        $this->path = $path;
    }

    public function getPath(): mixed
    {
        return $this->path;
    }

    public function getFileType(): string
    {
        return $this->fileType;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'file_name' => $this->fileName,
            'file_type' => $this->fileType,
            'path' => $this->path
        ];
    }

    public static function jsonDeserialize($values): Media
    {
        return (new Media($values['id'], $values['file_name'], $values['file_type'], $values['path']));
    }
}
