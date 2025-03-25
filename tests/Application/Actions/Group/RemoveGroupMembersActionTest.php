<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Group;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Objects\Group\GroupMember;
use App\Domain\Objects\Group\GroupMemberRepository;
use App\Domain\UseCase\Authentication\JwtManager;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class RemoveGroupMembersActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $groupMemberRepositoryProphecy = $this->prophesize(GroupMemberRepository::class);
        $groupMemberRepositoryProphecy
            ->deleteByUserIdAndGroupId(2, 2)
            ->shouldBeCalledOnce();

        $groupMember = new GroupMember(2, 1, 1, GroupMemberRepository::ROLE_ADMIN);
        $groupMemberRepositoryProphecy
            ->getByUserIdAndGroupId(1, 2)
            ->willReturn($groupMember)
            ->shouldBeCalledOnce();

        $container->set(GroupMemberRepository::class, $groupMemberRepositoryProphecy->reveal());
        $token = JwtManager::encode(JwtManager::getPayload(1, 'username'));
        $request = $this->createRequest('DELETE', '/groups/2/members')
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->withParsedBody([
                'user_id' => 2
            ]);
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, ['User Removed.']);
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

        $request = $this->createRequest('DELETE', '/groups/1/members')
            ->withParsedBody([
                'members' => [2]
            ]);

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::UNAUTHENTICATED, 'Unauthorized.');
        $expectedPayload = new ActionPayload(401, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}