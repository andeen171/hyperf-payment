<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use App\Request\ListShopkeepersRequest;
use App\Request\ListUsersRequest;
use App\Request\ShowUserRequest;
use App\Resource\UserResource;
use App\Service\AuthService;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class UserController
{
    public function __construct(
        protected readonly UserRepository $repository,
    )
    {
    }

    public function loggedUser(AuthService $authService): PsrResponseInterface
    {
        return UserResource::make($authService->getLoggedUser())->toResponse();
    }

    public function listUsers(ListUsersRequest $request): PsrResponseInterface
    {
        return UserResource::collection($this->repository->listUsers($request->validated()))->toResponse();
    }

    public function listShopkeepers(ListShopkeepersRequest $request): PsrResponseInterface
    {
        return UserResource::collection($this->repository->listShopKeepers($request->validated()))->toResponse();
    }

    public function showUser(ShowUserRequest $request): PsrResponseInterface
    {
        return UserResource::make($request->getItem())->toResponse();
    }

}
