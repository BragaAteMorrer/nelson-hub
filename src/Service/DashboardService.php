<?php
namespace App\Service;

use App\Entity\User;
use App\Service\Integration\FootballApiClient;
use App\Service\Integration\HomeAssistantClient;
use App\Service\Integration\IdfmClient;
use App\Service\Integration\SncfClient;

final class DashboardService
{
    public function __construct(
        private readonly UserSettingsService $settings,
        private readonly FootballApiClient $football,
        private readonly IdfmClient $idfm,
        private readonly SncfClient $sncf,
        private readonly HomeAssistantClient $home,
    ) {}

    public function forUser(User $user): array
    {
        $settings=$this->settings->get($user);
        return [
            'integrationStatus'=>[
                'football'=>$this->football->configured(),
                'idfm'=>$this->idfm->configured(),
                'sncf'=>$this->sncf->configured(),
                'homeAssistant'=>$this->home->configured(),
            ],
            'sports'=>['teams'=>array_values(array_filter($settings['sports']['teams'] ?? [],static fn(array $t):bool=>(bool)($t['enabled'] ?? false)))],
            'transport'=>$settings['transport'] ?? [],
            'home'=>$settings['home'] ?? [],
            'calendar'=>$settings['calendar'] ?? [],
        ];
    }
}
