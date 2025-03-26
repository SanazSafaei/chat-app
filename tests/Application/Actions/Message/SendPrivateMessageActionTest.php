<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Message;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Objects\Message\MessageRepository;
use App\Domain\Objects\Message\Message;
use App\Domain\UseCase\Authentication\JwtManager;
use DateTime;
use Slim\Middleware\ErrorMiddleware;
use Tests\Infrastructure\Persistence\FakeDB;
use Tests\TestCase;

class SendPrivateMessageActionTest extends TestCase
{
    public function tearDown(): void
    {
        (new FakeDB())->deleteDB();
        parent::tearDown();
    }
    public function testAction()
    {
        $app = $this->getAppInstance();

        $now = new DateTime();

        $message = new Message(1, 1, 2, MessageRepository::TYPE_PRIVATE, $now, 'Hello', null);

        $token = JwtManager::encode(JwtManager::getPayload(1, 'username'));
        $request = $this->createRequest('POST', '/users/2/messages')
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->withParsedBody([
                'message' => 'Hello',
                'media' => ''
            ]);
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, $message);
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

        $request = $this->createRequest('POST', '/users/1/messages')
            ->withParsedBody([
                'from' => 1,
                'to' => 2,
                'messageType' => 'text',
                'message' => 'Hello',
                'media' => ''
            ]);

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::UNAUTHENTICATED, 'Unauthorized.');
        $expectedPayload = new ActionPayload(401, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}