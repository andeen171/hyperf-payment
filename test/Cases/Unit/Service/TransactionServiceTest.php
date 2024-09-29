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

namespace HyperfTest\Cases\Unit\Service;


use App\Enum\UserTypeEnum;
use App\Exception\TransactionFailedException;
use App\Model\Transaction;
use App\Model\User;
use App\Service\Request\AuthorizeTransferService;
use App\Service\Request\NotificationService;
use App\Service\TransactionService;
use Exception;
use Faker;
use Mockery;
use PHPUnit\Framework\TestCase;

class TransactionServiceTest extends TestCase
{
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
    }

    public function testTransfer()
    {
        $this->payer->wallet->update([
            'balance' => $balance = $this->faker->randomNumber(5),
        ]);

        $payeeBalance = $this->payee->wallet->balance;

        $authorizeTransferServiceMock = Mockery::mock(AuthorizeTransferService::class);
        $authorizeTransferServiceMock->shouldReceive('authorize')->once()->andReturn([]);

        $notifyServiceMock = Mockery::mock(NotificationService::class);
        $notifyServiceMock->shouldReceive('notify')->once()->andReturn([]);

        $service = new TransactionService(
            $notifyServiceMock,
            $authorizeTransferServiceMock,
        );

        $data = [
            'value' => $value = intval($balance / 2),
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
        ];

        $response = $service->transfer($data);

        $this->assertInstanceOf(Transaction::class, $response);

        // Assert that the accounts have not been updated
        $this->payer->refresh();
        $this->payee->refresh();
        $this->assertEquals($balance - $value, $this->payer->wallet->balance);
        $this->assertEquals($payeeBalance + $value, $this->payee->wallet->balance);

    }

    public function testTransferResiliency()
    {
        $this->payer->wallet->update([
            'balance' => $balance = $this->faker->randomNumber(5),
        ]);

        $payeeBalance = $this->payee->wallet->balance;

        $authorizeTransferServiceMock = Mockery::mock(AuthorizeTransferService::class);
        $authorizeTransferServiceMock->shouldReceive('authorize')->twice()->andThrow(Exception::class);

        $notifyServiceMock = Mockery::mock(NotificationService::class);
        $notifyServiceMock->shouldReceive('notify')->never();

        $service = new TransactionService(
            $notifyServiceMock,
            $authorizeTransferServiceMock
        );

        $this->expectException(TransactionFailedException::class);
        $service->transfer([
            'value' => intval($balance / 2),
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
        ]);

        // Assert that the accounts have not been updated
        $this->payer->refresh();
        $this->payee->refresh();
        $this->assertEquals($balance, $this->payer->wallet->balance);
        $this->assertEquals($payeeBalance, $this->payee->wallet->balance);
        $this->assertEmpty(Transaction::all());
    }

}
