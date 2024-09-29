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

namespace HyperfTest\Cases\Api\Users;


use App\Enum\UserTypeEnum;
use App\Model\User;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;
use HyperfTest\HttpTestCase;

class ListShopkeepersTest extends HttpTestCase
{
    protected const ROUTE = '/users/shopkeepers';

    protected Collection $shopkeepers;

    protected User $commonUser;

    public function setUp(): void
    {
        $this->shopkeepers = factory(User::class, 5)->create(['type' => UserTypeEnum::SHOPKEEPER->value]);
        $this->commonUser = factory(User::class)->create(['type' => UserTypeEnum::COMMON->value]);

        $token = $this->tokenBuilder->forUser($this->shopkeepers->first())->get();
        $this->setAuthenticationToken($token);
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

        $this->assertCount(count($this->shopkeepers), $response->json('data'));
        $this->assertNotContains($this->commonUser->id, Arr::flatten($response->json('data.*.id')));
    }

}
