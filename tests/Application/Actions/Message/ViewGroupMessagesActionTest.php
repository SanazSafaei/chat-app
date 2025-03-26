<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Message;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Objects\Message\Message;
use App\Domain\Objects\Message\MessageRepository;
use App\Domain\UseCase\Authentication\JwtManager;
use DateTime;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class ViewGroupMessagesActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $now = new DateTime();
        $groupMessages = [
            new Message(1, 1, 2, MessageRepository::TYPE_GROUP, $now, 'Message 1', null),
            new Message(2, 2, 2, MessageRepository::TYPE_GROUP, $now, 'Message 2', null)
        ];

        $groupMessageRepositoryProphecy = $this->prophesize(MessageRepository::class);
        $groupMessageRepositoryProphecy
            ->findMessagesToGroupId(1)
            ->willReturn($groupMessages)
            ->shouldBeCalledOnce();

        $container->set(MessageRepository::class, $groupMessageRepositoryProphecy->reveal());
        $token = JwtManager::encode(JwtManager::getPayload(1, 'username'));
        $request = $this->createRequest('GET', '/groups/1/messages')->withHeader('Authorization', 'Bearer ' . $token);
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, $groupMessages);
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

        $request = $this->createRequest('GET', '/groups/1/messages');

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::UNAUTHENTICATED, 'Unauthorized.');
        $expectedPayload = new ActionPayload(401, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
