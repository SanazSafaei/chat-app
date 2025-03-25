<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Authentication;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Objects\User\User;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\Infrastructure\Persistence\FakeDB;
use Tests\TestCase;

class RegisterActionTest extends TestCase
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
        $user = new User(1, 'username123', 'Ppassword1234!', 'first_name','last_name', 'email@example.com', '', $now, $now, $now);

        $request = $this->createRequest('POST', '/auth/register')
            ->withParsedBody([
                'username' => 'username123',
                'password' => 'Ppassword1234!',
                'email' => 'email@example.com'
            ]);
        $response = $app->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $baseUri = $_SERVER['HTTP_HOST'];
        $this->assertEquals('http://' . $baseUri . '/users/' . $user->getId(), $response->getHeader('Location')[0]);
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

        $request = $this->createRequest('POST', '/auth/register')
            ->withParsedBody([
                'username' => '',
                'password' => 'password',
                'email' => 'email@example.com'
            ]);

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::BAD_REQUEST, "Username Must: \n start with letter \n 6-32 characters \n Letters and numbers only");
        $expectedPayload = new ActionPayload(400, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}