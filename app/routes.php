<?php

declare(strict_types=1);

use App\Application\Actions\Authentication\LoginAction;
use App\Application\Actions\Authentication\LogoutAction;
use App\Application\Actions\Authentication\RegisterAction;
use App\Application\Actions\Message\SendPrivateMessageAction;
use App\Application\Actions\Message\ViewChatAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\App;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
        $group->get('/{id}/messages', ViewChatAction::class);
        $group->post('/{id}/messages', SendPrivateMessageAction::class);
    });

    $app->group('/auth', function (Group $group) {
        $group->post('/register', RegisterAction::class);
        $group->post('/login', LoginAction::class);
        $group->post('/logout', LogoutAction::class);
    });
};
