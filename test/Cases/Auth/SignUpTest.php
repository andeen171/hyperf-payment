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

namespace HyperfTest\Cases\Auth;


use App\Enum\UserTypeEnum;
use Faker;
use Hyperf\Testing\TestCase;

class SignUpTest extends TestCase
{
    protected const ROUTE = '/auth/sign-up';
    protected const SIGN_IN_ROUTE = '/auth/sign-in';

    protected Faker\Generator $faker;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->faker = Faker\Factory::create('pt_BR');
    }

    public function testSignUp()
    {
        $data = [
            'type' => UserTypeEnum::COMMON->value,
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'document' => $this->faker->cpf,
            'email' => $email = $this->faker->email,
            'password' => $password = $this->faker->password,
            'passwordConfirmation' => $password,
        ];

        $response = $this->post(self::ROUTE, $data);
        $response->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'type',
                'firstName',
                'lastName',
                'document',
                'email',
            ]
        ]);

        $response = $this->post(self::SIGN_IN_ROUTE, [
            'email' => $email,
            'password' => $password,
        ]);
        $response->assertOk()->assertJsonStructure(['token']);
    }

    public function testSignUpPasswordValidation()
    {
        $data = [
            'type' => UserTypeEnum::COMMON->value,
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'document' => $this->faker->numerify('###########'),
            'email' => $this->faker->email,
            'password' => $this->faker->password,
            'passwordConfirmation' => $this->faker->password, // Different passwords
        ];

        $response = $this->post(self::ROUTE, $data);
        $response->assertStatus(422);
    }

    public function testSignUpDocumentValidation()
    {
        $data = [
            'type' => UserTypeEnum::COMMON->value, // Should have a valid cpf as document
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'document' => $this->faker->cnpj,
            'email' => $this->faker->email,
            'password' => $password = $this->faker->password,
            'passwordConfirmation' => $password,
        ];

        $response = $this->post(self::ROUTE, $data);
        $response->assertStatus(422);

        $data = [
            'type' => UserTypeEnum::SHOPKEEPER->value, // Should have a valid cnpj as document
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'document' => $this->faker->cnpj,
            'email' => $this->faker->email,
            'password' => $password = $this->faker->password,
            'passwordConfirmation' => $password,
        ];

        $response = $this->post(self::ROUTE, $data);
        $response->assertCreated();

        $data = [
            'type' => UserTypeEnum::SHOPKEEPER->value, // Should have a valid cnpj as document
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'document' => $this->faker->cpf,
            'email' => $this->faker->email,
            'password' => $password = $this->faker->password,
            'passwordConfirmation' => $password,
        ];

        $response = $this->post(self::ROUTE, $data);
        $response->assertStatus(422);
    }
}
