<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Group;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Objects\Group\GroupMemberRepository;
use App\Domain\Objects\Group\GroupRepository;
use App\Domain\Objects\Group\GroupMember;
use App\Domain\Objects\User\User;
use App\Domain\Objects\User\UserRepository;
use App\Domain\UseCase\Authentication\JwtManager;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class ViewGroupMembersActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $now = new \DateTime();
        $user = new User(1, "bill.gates", "12345", "Bill", "Gates", 'bill@gate.com', "test/photo", $now, $now, $now);
        $user2 = new User(2, 'steve.jobs', "12345", 'Steve', 'Jobs', 'bill@gate.com', "test/photo", $now, $now, $now);

        $gpm1 = new GroupMember(1, 1, 1, GroupMemberRepository::ROLE_MEMBER);
        $gpm1->setUserData($user);
        $gpm2 = new GroupMember(2, 2, 1, GroupMemberRepository::ROLE_MEMBER);
        $gpm2->setUserData($user2);
        $groupMembers = [
            $gpm1,
            $gpm2
        ];

        $groupRepositoryProphecy = $this->prophesize(GroupMemberRepository::class);
        $groupRepositoryProphecy
            ->findGroupMembers(1)
            ->willReturn($groupMembers)
            ->shouldBeCalledOnce();

        $container->set(GroupMemberRepository::class, $groupRepositoryProphecy->reveal());

        $token = JwtManager::encode(JwtManager::getPayload(1, 'username'));
        $request = $this->createRequest('GET', '/groups/1/members')->withHeader('Authorization', 'Bearer ' . $token);
        $response = $app->handle($request);

        $groupMembersView = [];
        foreach ($groupMembers as $member) {
            $user = $member->getUserData();
            $userData = $user->jsonSerialize();
            $memberInfo = $member->jsonSerialize();
            $memberInfo['user'] = ViewUserAction::sanitiseUserData($userData);
            $groupMembersView[] = $memberInfo;
        }
        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, $groupMembersView);
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

        $request = $this->createRequest('GET', '/groups/1/members');

        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::UNAUTHENTICATED, 'Unauthorized.');
        $expectedPayload = new ActionPayload(401, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}