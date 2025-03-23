<?php

namespace App\Application\Middleware;

use App\Domain\UseCase\Authentication\JwtManager;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Response;

class AuthenticationMiddleware implements Middleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

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
                    if($e->getMessage() == 'Expired token') {
                        $response = (new Response());
                        return $response
                            ->withHeader('Location', 'http://' . $_SERVER['HTTP_HOST'] . '/aut/login')
                            ->withHeader('Authorization', '')
                            ->withHeader('set-cookie', '')
                            ->withStatus(302);
                    }
                    $response = (new Response(401));
                    $response->getBody()->write('Unauthorized');
                    return $response;
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
                    $logger = new \slim\Logger();
                    $logger->log('info', $e->getMessage());
                    throw new HttpUnauthorizedException($request);
                }
            }
        }
        return $handler->handle($request);
    }
}
