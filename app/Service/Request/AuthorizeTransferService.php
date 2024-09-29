<?php

namespace App\Service\Request;

use App\Service\Abstract\AbstractRequestClientService;
use GuzzleHttp\Exception\GuzzleException;
use function Hyperf\Config\config;

class AuthorizeTransferService extends AbstractRequestClientService
{
    public function __construct()
    {
        $this->baseUrl = config('authorization-service.url');

        parent::__construct();
    }

    /**
     * @throws GuzzleException
     */
    public function authorize(array $data): array
    {
        return $this->sendRequest('GET', 'authorize');
    }
}