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

namespace HyperfTest;

use App\Middleware\AuthMiddleware;
use App\Service\AuthService;
use Hyperf\Context\ApplicationContext;
use Hyperf\Context\Context;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Db;
use Hyperf\Testing\Http\Client;
use Hyperf\Testing\Http\TestResponse;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function Hyperf\Config\config;
use function Hyperf\Support\make;

/**
 * Class HttpTestCase.
 * @method TestResponse get($uri, $data = [], $headers = [])
 * @method TestResponse post($uri, $data = [], $headers = [])
 * @method TestResponse put($uri, $data = [], $headers = [])
 * @method TestResponse json($uri, $data = [], $headers = [])
 * @method TestResponse file($uri, $data = [], $headers = [])
 * @method TestResponse request($method, $path, $options = [])
 */
abstract class HttpTestCase extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected Client $client;
    protected TokenBuilder $tokenBuilder;
    /**
     * @var array<class-string>
     */
    protected array $mocks = [];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->client = make(Client::class);
        $this->tokenBuilder = make(TokenBuilder::class);
    }

    public function __call($name, $arguments)
    {
        return new TestResponse($this->client->{$name}(...$arguments));
    }

    protected function tearDown(): void
    {
        $this->cleanUpContainer();

        $this->closeMockery();

        Schema::disableForeignKeyConstraints();

        $tableNameProperty = 'Tables_in_' . config('app_name') . '-testing';
        foreach (Schema::getAllTables() as $table) {
            $tableName = $table->$tableNameProperty;

            if ($tableName == 'migrations') {
                continue;
            }

            DB::table($tableName)->delete();
        }

        Schema::enableForeignKeyConstraints();
    }

    protected function setAuthenticationToken(object $token = null): void
    {
        $token ??= $this->tokenBuilder->get();

        $mock = Mockery::mock(AuthMiddleware::class)
            ->shouldReceive('process')
            ->andReturnUsing(function (
                ServerRequestInterface  $request,
                RequestHandlerInterface $handler,
            ) use ($token) {
                Context::set(AuthService::CONTEXT_KEY, $token);

                return $handler->handle($request);
            });

        $this->defineMockInApplicationContainer(AuthMiddleware::class, $mock->getMock());
    }

    /**
     * @param class-string $className
     * @param Mockery\MockInterface $mock
     * @return void
     */
    protected function defineMockInApplicationContainer(string $className, Mockery\MockInterface $mock): void
    {
        /** @var ContainerInterface $container */
        $container = ApplicationContext::getContainer();

        $container->define($className, fn() => $mock->makePartial());

        $this->mocks[] = $className;
    }

    protected function cleanUpContainer(): void
    {
        /** @var ContainerInterface $container */
        $container = ApplicationContext::getContainer();

        foreach ($this->mocks as $className) {
            $container->unbind($className);
            $container->define($className, $className);
        }
    }

}
