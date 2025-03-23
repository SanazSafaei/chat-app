<?php

namespace App\Application\Middleware;

use App\Domain\UseCase\Authentication\JwtManager;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class AuthenticationMiddleware implements Middleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->hasHeader('Authorization')) {
            $token = $request->getHeader('Authorization');
            $token = explode(' ', $token[0]);
            if($token[0] === 'Bearer') {
                $jwt = $token[1];
                try {
                    $decoded = JwtManager::decode($jwt);
                    $request = $request->withAttribute('token', $decoded);
                } catch (Exception $e) {
                    $response = (new Response(401));
                    $response->getBody()->write('Unauthorized');
                    return $response;
                }
            }
        }
        return $handler->handle($request);
    }
}