<?php

declare(strict_types=1);

use App\Domain\Objects\Group\GroupMemberRepository;
use App\Domain\Objects\Group\GroupRepository;
use App\Domain\Objects\Message\MessageRepository;
use App\Domain\Objects\User\UserRepository;
use App\Infrastructure\Persistence\Group\InMemoryGroupMembersRepository;
use App\Infrastructure\Persistence\Group\InMemoryGroupRepository;
use App\Infrastructure\Persistence\Message\InMemoryMessageRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(InMemoryUserRepository::class),
        MessageRepository::class => \DI\autowire(InMemoryMessageRepository::class),
        GroupRepository::class => \DI\autowire(InMemoryGroupRepository::class),
        GroupMemberRepository::class => \DI\autowire(InMemoryGroupMembersRepository::class)
    ]);
};
