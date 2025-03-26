<?php

declare(strict_types=1);

namespace App\Domain\Objects\Media;

use App\Domain\Objects\DomainObject;

interface MediaRepository
{
    public const array MEDIA_TYPES =  ['image/jpeg', 'image/png', 'image/gif'];
    public function insert(DomainObject $domain): DomainObject;
    public function findAll(): array;

    public function findById(int $id): Media;
}
