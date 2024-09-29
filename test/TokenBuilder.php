<?php

namespace HyperfTest;

use App\Model\User;
use App\Service\AuthService;

class TokenBuilder
{
    protected User $user;

    public function __construct(
        protected readonly AuthService $authService,
    )
    {
    }

    public function forUser(User $user): TokenBuilder
    {
        $this->user = $user;

        return $this;
    }

    public function get(): object
    {
        $this->user ??= factory(User::class)->create();

        return (object)$this->authService->getTokenData($this->user);
    }
}