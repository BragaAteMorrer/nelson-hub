<?php
namespace App\Service\Integration;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FootballApiClient
{
    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly string $footballApiKey = '',
        private readonly string $footballApiBase = 'https://v3.football.api-sports.io',
    ) {}

    public function configured(): bool { return $this->footballApiKey !== ''; }

    public function nextFixtures(int $teamId, int $count = 5): array
    {
        if (!$this->configured()) return [];
        $response=$this->http->request('GET',rtrim($this->footballApiBase,'/').'/fixtures',[
            'headers'=>['x-apisports-key'=>$this->footballApiKey],
            'query'=>['team'=>$teamId,'next'=>$count],
        ]);
        return $response->toArray(false)['response'] ?? [];
    }
}
