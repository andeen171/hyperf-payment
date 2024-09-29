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
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Db;
use Hyperf\Testing\Concerns\InteractsWithContainer;
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
 * @method get($uri, $data = [], $headers = [])
 * @method post($uri, $data = [], $headers = [])
 * @method put($uri, $data = [], $headers = [])
 * @method json($uri, $data = [], $headers = [])
 * @method file($uri, $data = [], $headers = [])
 * @method request($method, $path, $options = [])
 */
abstract class HttpTestCase extends TestCase
{
    use InteractsWithContainer;

    protected Client $client;
    protected TokenBuilder $tokenBuilder;

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
        Mockery::close();

        $this->cleanUpAuthentication();

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

        ApplicationContext::getContainer()->define(
            AuthMiddleware::class,
            fn() => $mock->getMock()->makePartial(),
        );
    }

    protected function cleanUpAuthentication(): void
    {
        $container = ApplicationContext::getContainer();

        $container->unbind(AuthMiddleware::class);
        $container->define(AuthMiddleware::class, AuthMiddleware::class);
    }

}
