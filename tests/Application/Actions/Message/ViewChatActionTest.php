<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Message;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Objects\Message\MessageRepository;
use App\Domain\Objects\Message\Message;
use App\Domain\UseCase\Authentication\JwtManager;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class ViewChatActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $messages = [
            new Message(1, 1, 2, MessageRepository::TYPE_PRIVATE, 'Hello', '', new \DateTime()),
            new Message(2, 2, 1, MessageRepository::TYPE_PRIVATE, 'Hi', '', new \DateTime())
        ];

        $messageRepositoryProphecy = $this->prophesize(MessageRepository::class);
        $messageRepositoryProphecy
            ->findMessagesFromToId(1, 2, MessageRepository::TYPE_PRIVATE)
            ->willReturn($messages)
            ->shouldBeCalledOnce();

        $container->set(MessageRepository::class, $messageRepositoryProphecy->reveal());
        $token = JwtManager::encode(JwtManager::getPayload(2, 'username'));
        $request = $this->createRequest('GET', '/users/1/messages')->withHeader('Authorization', 'Bearer ' . $token);
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, $messages);
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

        $request = $this->createRequest('GET', '/users/1/messages');

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::UNAUTHENTICATED, 'Unauthorized.');
        $expectedPayload = new ActionPayload(401, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}