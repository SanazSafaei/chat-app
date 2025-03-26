<?php

namespace App\Application\Middleware;

use App\Domain\UseCase\Authentication\JwtManager;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Logger;
use Slim\Psr7\Response;

class AuthenticationMiddleware implements Middleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $logger = new Logger();

        if ($request->getUri()->getPath() === '/auth/login' || $request->getUri()->getPath() === '/auth/register') {
            return $handler->handle($request);
        }
        if ($request->hasHeader('Authorization')) {
            $token = $request->getHeader('Authorization');
            $token = explode(' ', $token[0]);
            if ($token[0] === 'Bearer') {
                $jwt = $token[1];
                try {
                    $decoded = JwtManager::decode($jwt);
                    $request = $request->withAttribute('token', $decoded);
                } catch (Exception $e) {
                    return $this->getTokenExpieredResponse();
                }
            }
        } elseif ($request->hasHeader('Cookie')) {
            $cookieParams = $request->getCookieParams();
            if (isset($cookieParams['token'])) {
                $token = $cookieParams['token'];
                $jwt = $token;
                try {
                    $decoded = JwtManager::decode($jwt);
                    $request = $request->withAttribute('token', $decoded);
                } catch (Exception $e) {
                    return $this->getTokenExpieredResponse();
                }
            }
        }
        return $handler->handle($request);
    }

    public function getTokenExpieredResponse(): ResponseInterface|\Psr\Http\Message\MessageInterface|Response
    {
        $response = (new Response());
        $response->getBody()->write('Unauthorized.');
        return $response
            ->withHeader('Authorization', '')
            ->withHeader('set-cookie', '')
            ->withStatus(400);
    }
}
