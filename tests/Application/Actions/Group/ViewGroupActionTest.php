<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Group;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Objects\Group\GroupRepository;
use App\Domain\Objects\Group\Group;
use App\Domain\UseCase\Authentication\JwtManager;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class ViewGroupActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $groups = new Group(1, 'Group 1', '', 'Description 1', 1, new \DateTime(), new \DateTime());

        $groupRepositoryProphecy = $this->prophesize(GroupRepository::class);
        $groupRepositoryProphecy
            ->findById(1)
            ->willReturn($groups)
            ->shouldBeCalledOnce();

        $container->set(GroupRepository::class, $groupRepositoryProphecy->reveal());
        $token = JwtManager::encode(JwtManager::getPayload(1, 'username'));
        $request = $this->createRequest('GET', '/groups/1')->withHeader('Authorization', 'Bearer ' . $token);
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, $groups);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }

    public function testActionThrowsUnauthorizedException()
    {
        $app = $this->getAppInstance();

        $callableResolver = $app->getCallableResolver();
        $responseFactory = $app->getResponseFactory();

        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
        $errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, false, false);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        $app->add($errorMiddleware);

        $request = $this->createRequest('GET', '/groups/1');

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::UNAUTHENTICATED, 'Unauthorized.');
        $expectedPayload = new ActionPayload(401, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}