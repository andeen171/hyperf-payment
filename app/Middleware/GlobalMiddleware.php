<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\AuthService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GlobalMiddleware implements MiddlewareInterface
{
    public function __construct(protected ContainerInterface $container, protected AuthService $authService)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
}
