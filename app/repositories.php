<?php

declare(strict_types=1);

use App\Domain\Objects\Message\MessageRepository;
use App\Domain\Objects\User\UserRepository;
use App\Infrastructure\Persistence\Message\InMemoryMessageRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(InMemoryUserRepository::class),
        MessageRepository::class => \DI\autowire(InMemoryMessageRepository::class)
    ]);
};
