<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Objects\User\User;
use App\Domain\Objects\User\UserNotFoundException;
use App\Domain\Objects\User\UserRepository;
use App\Domain\UseCase\Authentication\JwtManager;
use Cassandra\Exception\UnauthorizedException;
use DateTime;
use DI\Container;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class ViewUserActionTest extends TestCase
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
            ->findUserOfId(1)
            ->willReturn($user)
            ->shouldBeCalledOnce();

        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());
        $token = JwtManager::encode(JwtManager::getPayload($user->getId(), $user->getUsername()));
        $request = $this->createRequest('GET', '/users/1')->withHeader('Authorization', 'Bearer '.$token);
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $data = $user->jsonSerialize();
        unset($data['password']);
        $expectedPayload = new ActionPayload(200, $data);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }

    public function testActionThrowsUserNotFoundException()
    {
        $app = $this->getAppInstance();

        $callableResolver = $app->getCallableResolver();
        $responseFactory = $app->getResponseFactory();

        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
        $errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, false, false);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        $app->add($errorMiddleware);

        /** @var Container $container */
        $container = $app->getContainer();

//        $request = $this->createRequest('GET', '/users/1');
        $token = JwtManager::encode(JwtManager::getPayload(1, 'sanazz'));
        $request = $this->createRequest('GET', '/users/1')->withHeader('Authorization', 'Bearer '.$token);

        $userRepositoryProphecy = $this->prophesize(UserRepository::class);
        $userRepositoryProphecy
            ->findUserOfId(1)
            ->willThrow(new UserNotFoundException())
            ->shouldBeCalledOnce();

        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::RESOURCE_NOT_FOUND, 'The user you requested does not exist.');
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
