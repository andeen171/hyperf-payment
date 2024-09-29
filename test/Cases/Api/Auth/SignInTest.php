<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace HyperfTest\Cases\Api\Auth;


use App\Enum\ExceptionMessageCodeEnum;
use App\Model\User;
use Faker;
use HyperfTest\HttpTestCase;
use Swoole\Http\Status;

class SignInTest extends HttpTestCase
{
    protected const ROUTE = '/auth/sign-in';
    protected const PROFILE_ROUTE = '/users/profile';

    protected Faker\Generator $faker;
    protected User $user;
    protected string $password;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->faker = Faker\Factory::create();
    }

    public function setUp(): void
    {
        $this->password = $this->faker->password();

        $this->user = factory(User::class)->create([
            'password' => password_hash($this->password, PASSWORD_DEFAULT),
        ]);
    }

    public function testSignIn()
    {
        $data = [
            'email' => $this->user->email,
            'password' => $this->password,
        ];

        $response = $this->post(self::ROUTE, $data);
        $response->assertOk()->assertJsonStructure(['token']);

        $profileResponse = $this->get(self::PROFILE_ROUTE,
            headers: ['Authorization' => $response->json('token')]
        );

        $profileResponse->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'type',
                'firstName',
                'lastName',
                'document',
                'email',
            ]
        ]);
    }

    public function testWrongCredentials()
    {
        $data = [
            'email' => $this->user->email,
            'password' => $this->faker->password, // Wrong password
        ];

        $response = $this->post(self::ROUTE, $data);
        $response->assertUnauthorized()->assertJson([
            'code' => Status::UNAUTHORIZED,
            'message' => ExceptionMessageCodeEnum::INVALID_CREDENTIALS->value,
        ]);
    }
}
