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
use Tests\Infrastructure\Persistence\FakeDB;
use Tests\TestCase;

class CreateGroupActionTest extends TestCase
{
    public function tearDown(): void
    {
        (new FakeDB())->deleteDB();
        parent::tearDown();
    }
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $now = new \DateTime();
        $group = new Group(1, 'New Group', '', 'New Description', 1, $now, $now);

        $token = JwtManager::encode(JwtManager::getPayload(1, 'username'));
        $request = $this->createRequest('POST', '/groups')
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->withParsedBody([
                'name' => 'New Group',
                'description' => 'New Description'
            ]);
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, $group);
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

        $request = $this->createRequest('POST', '/groups')
            ->withParsedBody([
                'name' => 'New Group',
                'description' => 'New Description'
            ]);

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::UNAUTHENTICATED, 'Unauthorized.');
        $expectedPayload = new ActionPayload(401, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}