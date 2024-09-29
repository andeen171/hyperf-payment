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

namespace HyperfTest\Cases\Api;


use App\Enum\ExceptionMessageCodeEnum;
use App\Enum\UserTypeEnum;
use App\Model\User;
use App\Service\Request\AuthorizeTransferService;
use App\Service\Request\NotificationService;
use Faker;
use HyperfTest\HttpTestCase;
use Mockery;
use Swoole\Http\Status;

class TransferTest extends HttpTestCase
{
    protected const ROUTE = '/transfer';

    protected Faker\Generator $faker;
    protected User $payer;
    protected User $payee;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->faker = Faker\Factory::create();
    }

    public function setUp(): void
    {
        $this->payer = factory(User::class)->create(['type' => UserTypeEnum::COMMON->value]);
        $this->payee = factory(User::class)->create(['type' => UserTypeEnum::SHOPKEEPER->value]);

        $this->setAuthenticationToken($this->tokenBuilder->forUser($this->payer)->get());
        $this->startMockery();
    }

    public function testTransfer()
    {
        $this->payer->wallet->update([
            'balance' => $balance = $this->faker->randomNumber(5),
        ]);

        $authorizeTransferServiceMock = Mockery::mock(AuthorizeTransferService::class);
        $authorizeTransferServiceMock->shouldReceive('authorize')->once()->andReturn([]);

        $this->defineMockInApplicationContainer(AuthorizeTransferService::class, $authorizeTransferServiceMock);

        $notifyServiceMock = Mockery::mock(NotificationService::class);
        $notifyServiceMock->shouldReceive('notify')->once()->andReturn([]);

        $this->defineMockInApplicationContainer(NotificationService::class, $notifyServiceMock);

        $data = [
            'value' => intval($balance / 2),
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

    public function testTransferWithInsufficientFunds()
    {
        $this->payer->wallet->update([
            'balance' => $balance = $this->faker->randomNumber(5),
        ]);

        $data = [
            'value' => $balance + 1,
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
        ];

        $response = $this->post(self::ROUTE, $data);
        $response->assertForbidden()
            ->assertJson([
                'code' => Status::FORBIDDEN,
                'message' => ExceptionMessageCodeEnum::INSUFFICIENT_FUNDS->value,
            ]);
    }
}
