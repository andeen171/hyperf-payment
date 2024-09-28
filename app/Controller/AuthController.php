<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\Auth\SignInRequest;
use App\Request\Auth\SignUpRequest;
use App\Resource\UserResource;
use App\Service\AuthService;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Swoole\Http\Status;

class AuthController
{
    public function __construct(
        protected readonly AuthService $authService
    )
    {
    }

    public function signUp(SignUpRequest $request): PsrResponseInterface
    {
        $user = $this->authService->signUp($request->all());

        return UserResource::make($user)->toResponse()->withStatus(Status::CREATED);
    }

    public function signIn(SignInRequest $request, ResponseInterface $response): PsrResponseInterface
    {
        $jwt = $this->authService->signIn($request->getItem(), $request->validated());

        return $response->json(['token' => $jwt]);
    }

    public function user(): PsrResponseInterface
    {
        return UserResource::make($this->authService->getLoggedUser())->toResponse();
    }
}
