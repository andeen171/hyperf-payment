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

use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Database\Commands\Migrations\FreshCommand;
use Hyperf\Database\Model\Factory;
use Hyperf\Database\Model\FactoryBuilder;
use function Hyperf\Config\config;
use function Hyperf\Coroutine\run;

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

Swoole\Runtime::enableCoroutine(true);

!defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

require BASE_PATH . '/vendor/autoload.php';

!defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', Hyperf\Engine\DefaultOption::hookFlags());

Hyperf\Di\ClassLoader::init();

$container = require BASE_PATH . '/config/container.php';

$config = $container->get(ConfigInterface::class);

if ($config->get('app_env') !== 'pipeline') {
    $config->set('databases.default.database', config('app_name') . '-testing');
}

$config->set('app_env', 'testing');
$config->set(Hyperf\Contract\StdoutLoggerInterface::class, [
    'log_level' => [
        Psr\Log\LogLevel::ALERT,
        Psr\Log\LogLevel::CRITICAL,
        Psr\Log\LogLevel::EMERGENCY,
        Psr\Log\LogLevel::ERROR,
        Psr\Log\LogLevel::INFO,
        Psr\Log\LogLevel::NOTICE,
        Psr\Log\LogLevel::WARNING,
    ],
]);

$container->get(Hyperf\Contract\ApplicationInterface::class);

run(function () use ($container) {
    $container->get(FreshCommand::class)->run(
        new Symfony\Component\Console\Input\StringInput(''),
        new Symfony\Component\Console\Output\ConsoleOutput()
    );
});

if (!function_exists('factory')) {
    function factory(...$arguments): FactoryBuilder
    {
        $factory = ApplicationContext::getContainer()->get(Factory::class);
        if (isset($arguments[1]) && is_string($arguments[1])) {
            return $factory->of($arguments[0], $arguments[1])->times($arguments[2] ?? null);
        }
        if (isset($arguments[1])) {
            return $factory->of($arguments[0])->times($arguments[1]);
        }
        return $factory->of($arguments[0]);
    }
}
