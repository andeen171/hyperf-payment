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

namespace HyperfTest\Cases\Users;


use App\Model\User;
use HyperfTest\HttpTestCase;

class ShowUserTest extends HttpTestCase
{
    protected const ROUTE = '/users/%s';

    protected User $user;

    public function setUp(): void
    {
        $this->user = factory(User::class)->create();

        $this->setAuthenticationToken();
    }

    public function testShowUser()
    {
        $response = $this->get(sprintf(self::ROUTE, $this->user->id));
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'firstName',
                    'lastName',
                    'email',
                    'document',
                ]
            ]);
    }

}
