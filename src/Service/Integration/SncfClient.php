<?php
namespace App\Service\Integration;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SncfClient
{
    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly string $sncfApiToken = '',
        private readonly string $sncfApiBase = 'https://api.sncf.com/v1',
    ) {}

    public function configured(): bool { return $this->sncfApiToken !== ''; }

    public function searchPlaces(string $query): array
    {
        $query = trim($query);
        if (!$this->configured() || mb_strlen($query) < 2) return [];

        $data = $this->http->request('GET', rtrim($this->sncfApiBase,'/').'/coverage/sncf/places', [
            'auth_basic' => [$this->sncfApiToken,''],
            'query' => ['q' => $query, 'type[]' => 'stop_area'],
        ])->toArray(false);

        return $data['places'] ?? [];
    }

    public function journeys(string $from,string $to,?string $datetime=null): array
    {
        if (!$this->configured()) return [];
        $query=['from'=>$from,'to'=>$to];
        if ($datetime) $query['datetime']=$datetime;
        return $this->http->request('GET',rtrim($this->sncfApiBase,'/').'/coverage/sncf/journeys',[
            'auth_basic'=>[$this->sncfApiToken,''],
            'query'=>$query,
        ])->toArray(false);
    }
}
