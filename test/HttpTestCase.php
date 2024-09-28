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

use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Db;
use Hyperf\Testing\Client;
use PHPUnit\Framework\TestCase;
use function Hyperf\Config\config;
use function Hyperf\Support\make;

/**
 * Class HttpTestCase.
 * @method get($uri, $data = [], $headers = [])
 * @method post($uri, $data = [], $headers = [])
 * @method json($uri, $data = [], $headers = [])
 * @method file($uri, $data = [], $headers = [])
 * @method request($method, $path, $options = [])
 */
abstract class HttpTestCase extends TestCase
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->client = make(Client::class);
    }

    public function __call($name, $arguments)
    {
        return $this->client->{$name}(...$arguments);
    }

    protected function tearDown(): void
    {
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
}
