<?php
namespace App\Service\Integration;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class IdfmClient
{
    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly string $idfmApiKey = '',
        private readonly string $idfmApiBase = 'https://prim.iledefrance-mobilites.fr/marketplace/stop-monitoring',
    ) {}

    public function configured(): bool { return $this->idfmApiKey !== ''; }

    public function nextPassages(string $monitoringRef): array
    {
        if (!$this->configured()) return [];
        $url=rtrim($this->idfmApiBase,'/').'/'.rawurlencode($monitoringRef);
        return $this->http->request('GET',$url,[
            'headers'=>['apikey'=>$this->idfmApiKey,'Accept'=>'application/json'],
        ])->toArray(false);
    }
}
