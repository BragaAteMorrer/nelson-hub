<?php
namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class UserSettingsService
{
    public const DEFAULTS = [
        'locale' => 'fr',
        'timezone' => 'Europe/Paris',
        'theme' => 'dark',
        'accent' => 'sky',
        'dashboard' => ['sports'=>true,'transport'=>true,'home'=>true,'calendar'=>true],
        'sports' => [
            'teams' => [
                ['key'=>'fleury','name'=>'FC Fleury 91','enabled'=>true],
                ['key'=>'lens','name'=>'RC Lens','enabled'=>true],
                ['key'=>'racing-cff','name'=>'Racing CFF','enabled'=>true],
                ['key'=>'braga','name'=>'SC Braga','enabled'=>true],
            ],
            'notify_before_minutes' => 30,
        ],
        'transport' => ['favorite_stops'=>[],'favorite_stations'=>[],'show_disruptions'=>true],
        'home' => ['favorite_entities'=>[]],
        'calendar' => ['include_matches'=>true,'include_personal'=>true],
    ];

    public function __construct(private readonly EntityManagerInterface $em) {}

    public function get(User $user): array
    {
        return array_replace_recursive(self::DEFAULTS, $user->getSettings());
    }

    public function patch(User $user, array $patch): array
    {
        $merged = array_replace_recursive($this->get($user), $patch);
        $user->setSettings($merged);
        $this->em->flush();
        return $merged;
    }
}
