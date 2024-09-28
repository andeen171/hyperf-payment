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

use App\Controller\AuthController;
use App\Middleware\AuthMiddleware;
use Hyperf\HttpServer\Router\Router;

Router::addGroup('/auth', function () {
    Router::post('/sign-up', [AuthController::class, 'signUp']);
    Router::post('/sign-in', [AuthController::class, 'signIn']);
});

Router::get('/user', [AuthController::class, 'user'], [
    'middleware' => [AuthMiddleware::class],
]);