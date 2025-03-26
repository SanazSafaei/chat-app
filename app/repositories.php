<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Domain\Objects\Group\GroupMemberRepository;
use App\Domain\Objects\Group\GroupRepository;
use App\Domain\Objects\Media\MediaRepository;
use App\Domain\Objects\Message\MessageRepository;
use App\Domain\Objects\User\UserRepository;
use App\Infrastructure\Persistence\DB;
use App\Infrastructure\Persistence\Group\InMemoryGroupMembersRepository;
use App\Infrastructure\Persistence\Group\InMemoryGroupRepository;
use App\Infrastructure\Persistence\Media\InMemoryMediaRepository;
use App\Infrastructure\Persistence\Message\InMemoryMessageRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Tests\Infrastructure\Persistence\FakeDB;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([

//        UserRepository::class => \DI\autowire(InMemoryUserRepository::class),
        UserRepository::class => \DI\factory(function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            return new InMemoryUserRepository($settings->get('mode') == 'test' ? new FakeDB() : new DB());
        }),

//        MessageRepository::class => \DI\autowire(InMemoryMessageRepository::class),
        MessageRepository::class => \DI\factory(function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            return new InMemoryMessageRepository($settings->get('mode') == 'test' ? new FakeDB() : new DB());
        }),

//        GroupRepository::class => \DI\autowire(InMemoryGroupRepository::class),
        GroupRepository::class => \DI\factory(function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            return new InMemoryGroupRepository($settings->get('mode') == 'test' ? new FakeDB() : new DB());
        }),


//        GroupMemberRepository::class => \DI\autowire(InMemoryGroupMembersRepository::class),
        GroupMemberRepository::class => \DI\factory(function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            return new InMemoryGroupMembersRepository($settings->get('mode') == 'test' ? new FakeDB() : new DB());
        }),

        MediaRepository::class => \DI\factory(function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            return new InMemoryMediaRepository($settings->get('mode') == 'test' ? new FakeDB() : new DB());
        }),

    ]);
};
