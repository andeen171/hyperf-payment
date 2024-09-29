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

namespace HyperfTest\Cases;


use App\Enum\UserTypeEnum;
use App\Model\User;
use HyperfTest\HttpTestCase;

class TransferTest extends HttpTestCase
{
    protected const ROUTE = '/transfer';

    protected User $payer;
    protected User $payee;

    public function setUp(): void
    {
        $this->payer = factory(User::class)->create(['type' => UserTypeEnum::COMMON->value]);

        $this->payee = factory(User::class)->create(['type' => UserTypeEnum::SHOPKEEPER->value]);

        $this->setAuthenticationToken($this->tokenBuilder->forUser($this->payer)->get());
    }

    public function testTransfer()
    {
        $this->payer->wallet->update([
            'balance' => 10000,
        ]);

        $data = [
            'value' => 100,
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
        ];

        $response = $this->post(self::ROUTE, $data);
        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'amount',
                    'payerUserId',
                    'payeeUserId',
                    'createdAt',
                ]
            ]);
    }

}
