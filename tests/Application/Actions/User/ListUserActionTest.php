<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Application\Actions\ActionPayload;
use App\Domain\Objects\User\User;
use App\Domain\Objects\User\UserRepository;
use DateTime;
use DI\Container;
use Tests\TestCase;

class ListUserActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $now = new DateTime();
        $user = new User(1, 'bill.gates', "12345", 'Bill', 'Gates', 'bill@gate.com', "test/photo", $now, $now, $now);

        $userRepositoryProphecy = $this->prophesize(UserRepository::class);
        $userRepositoryProphecy
            ->findAll()
            ->willReturn([$user])
            ->shouldBeCalledOnce();

        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/users');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, [$user]);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
