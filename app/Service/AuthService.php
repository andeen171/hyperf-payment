<?php

namespace App\Service;

use App\Exception\Auth\UnauthorizedException;
use App\Exception\Auth\UserNotFoundException;
use App\Exception\Auth\WrongCredentialsException;
use App\Model\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Hyperf\Context\Context;
use Throwable;
use function Hyperf\Config\config;

class AuthService
{
    public const CONTEXT_KEY = 'jwt_token';

    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('app_secret');
    }

    public function signUp(array $data): User
    {
        return User::create([
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
            'document' => $data['document'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
        ])->load('wallet');
    }

    public function signIn(?User $user, array $data): string
    {
        if (!$user || !password_verify($data['password'], $user->password)) {
            throw new WrongCredentialsException();
        }

        return $this->issueToken($user);
    }

    public function issueToken(User $user): string
    {
        $payload = $this->getTokenData($user);

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function getTokenData(User $user): array
    {
        return [
            'iss' => config('app_url'),
            'aud' => config('app_url'),
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600,
            'sub' => $user->id,
        ];
    }

    public function decodeToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return User
     * @throws UnauthorizedException
     * @throws UserNotFoundException
     */
    public function getLoggedUser(): User
    {
        try {
            /** @var ?object $token */
            $token = Context::get(self::CONTEXT_KEY);

            return User::findOrFail($token->sub)->load('wallet');
        } catch (Throwable) {
            throw new UnauthorizedException();
        }
    }
}