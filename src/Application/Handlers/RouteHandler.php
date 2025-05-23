<?php

namespace App\Application\Handlers;

use App\Application\Actions\Authentication\LoginAction;
use App\Application\Actions\Authentication\LogoutAction;
use App\Application\Actions\Authentication\RegisterAction;
use App\Application\Actions\Group\AddGroupMembersAction;
use App\Application\Actions\Group\CreateGroupAction;
use App\Application\Actions\Group\ListGroupsAction;
use App\Application\Actions\Group\RemoveGroupMembersAction;
use App\Application\Actions\Group\ViewGroupAction;
use App\Application\Actions\Group\ViewGroupMembersAction;
use App\Application\Actions\Media\UploadMediaAction;
use App\Application\Actions\Media\ViewMediaAction;
use App\Application\Actions\Message\SendGroupMessageAction;
use App\Application\Actions\Message\SendPrivateMessageAction;
use App\Application\Actions\Message\ViewChatAction;
use App\Application\Actions\Message\ViewGroupMessagesAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\App;


class RouteHandler
{
    public function getApplicationRouts(App $app): void
    {
        $app->options('/{routes:.*}', function (Request $request, Response $response) {
            // CORS Pre-Flight OPTIONS Request Handler
            return $response;
        });

        $app->get('/', function (Request $request, Response $response) {
            $response->getBody()->write('Hello world!');
            return $response;
        });

        $app->group('/auth', function (Group $group) {
            $group->post('/register', RegisterAction::class);
            $group->post('/login', LoginAction::class);
            $group->post('/logout', LogoutAction::class);
        });

        $app->group('/users', function (Group $group) {
            $group->get('', ListUsersAction::class);
            $group->get('/{id}', ViewUserAction::class); // view a user profile or itself
            $group->get('/{id}/messages', ViewChatAction::class); //see private messages to a user
            $group->post('/{id}/messages', SendPrivateMessageAction::class); //send private message to a user
        });

        $app->group('/groups', function (Group $group) {
            $group->get('', ListGroupsAction::class); // List of all groups
            $group->post('', CreateGroupAction::class); // create group
            $group->get('/{id}', ViewGroupAction::class); // view group details
            $group->get('/{id}/members', ViewGroupMembersAction::class); // view group members
            $group->post('/{id}/members', AddGroupMembersAction::class); // add group members
            $group->delete('/{id}/members', RemoveGroupMembersAction::class); // remove group members
            $group->get('/{id}/messages', ViewGroupMessagesAction::class); // view groups messages
            $group->post('/{id}/messages', SendGroupMessageAction::class); // send message to group
        });

        $app->group('/media', function (Group $group) {
            $group->get('/{id}', ViewMediaAction::class);
            $group->post('/upload', UploadMediaAction::class);
        });
    }

}