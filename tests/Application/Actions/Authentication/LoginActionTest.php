<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Authentication;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Objects\User\User;
use App\Domain\Objects\User\UserRepository;
use DateTime;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class LoginActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $dateTime = $this->prophesize(DateTime::class);
        $dateTime->willBeConstructedWith(['2025-03-25 21:40:56']);
        $now = new DateTime();
        $pass = password_hash('Ppassword1234!', PASSWORD_BCRYPT);
        $user = new User(1, 'username123', $pass, 'first_name', 'last_name', 'email@example.com', '', $now, $now, $now);

        $userRepositoryProphecy = $this->prophesize(UserRepository::class);
        $userRepositoryProphecy
            ->findUserOfUsername('username123')
            ->willReturn($user)
            ->shouldBeCalledOnce();

        $userRepositoryProphecy
            ->updateField('last_seen', (new DateTime())->format('Y-m-d H:i:s'), 1);

        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());
        $container->set(DateTime::class, $dateTime->reveal());
        $request = $this->createRequest('POST', '/auth/login')
            ->withParsedBody([
                'username' => 'username123',
                'password' => 'Ppassword1234!'
            ]);
        $response = $app->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNotEmpty($response->getHeader('Authorization'));
    }

    public function testActionThrowsValidationException()
    {
        $app = $this->getAppInstance();

        $callableResolver = $app->getCallableResolver();
        $responseFactory = $app->getResponseFactory();

        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
        $errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, false, false);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        $app->add($errorMiddleware);

        $request = $this->createRequest('POST', '/auth/login')
            ->withParsedBody([
                'username' => '',
                'password' => 'password'
            ]);

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::RESOURCE_NOT_FOUND, 'The user you requested does not exist.');
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}