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

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Status;

class IndexController extends AbstractController
{
    public function index(): ResponseInterface
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        return $this->response->json([
            'method' => $method,
            'message' => "Hello $user.",
        ])->withStatus(Status::OK);
    }
}
