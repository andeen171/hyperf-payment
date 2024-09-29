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


use App\Enum\UserTypeEnum;
use App\Model\User;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;
use HyperfTest\HttpTestCase;

class ListUsersTest extends HttpTestCase
{
    protected const ROUTE = '/users';

    protected Collection $users;

    protected User $shopkeeper;

    public function setUp(): void
    {
        $this->users = factory(User::class, 5)->create(['type' => UserTypeEnum::COMMON->value]);
        $this->shopkeeper = factory(User::class)->create(['type' => UserTypeEnum::SHOPKEEPER->value]);

        $this->setAuthenticationToken();
    }

    public function testListUsers()
    {
        $data = [
            'page' => 1,
            'perPage' => 10,
        ];

        $response = $this->get(self::ROUTE, $data);
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'firstName',
                        'lastName',
                        'email',
                        'document'
                    ]
                ]
            ]);

        $this->assertCount(count($this->users), $response->json('data'));
        $this->assertNotContains($this->shopkeeper->id, Arr::flatten($response->json('data.*.id')));
    }

}
