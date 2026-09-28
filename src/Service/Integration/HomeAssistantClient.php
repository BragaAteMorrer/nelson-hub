<?php
namespace App\Service\Integration;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HomeAssistantClient
{
    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly string $homeAssistantUrl = '',
        private readonly string $homeAssistantToken = '',
    ) {}

    public function configured(): bool
    {
        return $this->homeAssistantUrl !== '' && $this->homeAssistantToken !== '';
    }

    public function states(): array
    {
        if (!$this->configured()) return [];
        return $this->http->request('GET',rtrim($this->homeAssistantUrl,'/').'/api/states',[
            'headers'=>['Authorization'=>'Bearer '.$this->homeAssistantToken,'Content-Type'=>'application/json'],
        ])->toArray(false);
    }

    public function callClimateService(string $service,array $payload): array
    {
        if (!$this->configured()) return [];
        return $this->http->request('POST',sprintf('%s/api/services/climate/%s',rtrim($this->homeAssistantUrl,'/'),$service),[
            'headers'=>['Authorization'=>'Bearer '.$this->homeAssistantToken,'Content-Type'=>'application/json'],
            'json'=>$payload,
        ])->toArray(false);
    }
}
