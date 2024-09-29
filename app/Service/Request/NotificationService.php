<?php

namespace App\Service\Request;

use App\Service\Abstract\AbstractRequestClientService;
use GuzzleHttp\Exception\GuzzleException;
use function Hyperf\Config\config;

class NotificationService extends AbstractRequestClientService
{
    public function __construct()
    {
        $this->baseUrl = config('notification-service.url');

        parent::__construct();
    }

    /**
     * @throws GuzzleException
     */
    public function notify(array $data): array
    {
        return $this->sendRequest('POST', 'notify', $data);
    }
}