<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\Auth\UnauthorizedException;
use App\Service\AuthService;
use Hyperf\Context\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(protected ContainerInterface $container, protected AuthService $authService)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if (!$decoded = $this->authService->decodeToken($token)) {
            throw new UnauthorizedException();
        }

        Context::set('jwt_token', $decoded);

        return $handler->handle($request);
    }
}
