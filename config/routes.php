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
use App\Controller\TransactionController;
use App\Controller\UserController;
use App\Middleware\AuthMiddleware;
use Hyperf\HttpServer\Router\Router;

Router::addGroup('/auth', function () {
    Router::post('/sign-up', [AuthController::class, 'signUp']);
    Router::post('/sign-in', [AuthController::class, 'signIn']);
});

Router::addGroup('/users', function () {
    Router::get('', [UserController::class, 'listUsers']);
    Router::get('/shopkeepers', [UserController::class, 'listShopKeepers']);
    Router::get('/profile', [UserController::class, 'loggedUser']);
    Router::addGroup('/{id}', function () {
        Router::get('', [UserController::class, 'showUser']);
        Router::post('/transfer', [UserController::class, 'transfer']);
    });
}, [
    'middleware' => [AuthMiddleware::class],
]);

Router::post('/transfer', [TransactionController::class, 'transfer'],
    ['middleware' => [AuthMiddleware::class]]
);
