<?php
namespace App\Service\Profile;

use Symfony\Component\HttpFoundation\RequestStack;

final class ProfileService
{
    private const DEFAULT_PROFILES = [
        'nelson' => ['name' => 'Nelson', 'emoji' => '👤'],
        'profile-2' => ['name' => 'Profil 2', 'emoji' => '👤'],
        'profile-3' => ['name' => 'Profil 3', 'emoji' => '👤'],
    ];

    public function __construct(private readonly RequestStack $requestStack) {}

    public function all(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get('profiles', self::DEFAULT_PROFILES);
    }

    public function activeKey(): string
    {
        $session = $this->requestStack->getSession();
        $key = (string) $session->get('active_profile', 'nelson');
        return array_key_exists($key, $this->all()) ? $key : 'nelson';
    }

    public function active(): array
    {
        $key = $this->activeKey();
        return ['key' => $key] + $this->all()[$key];
    }

    public function switch(string $key): void
    {
        if (!array_key_exists($key, $this->all())) {
            throw new \InvalidArgumentException('Unknown profile.');
        }
        $this->requestStack->getSession()->set('active_profile', $key);
    }

    public function rename(string $key, string $name): void
    {
        $profiles = $this->all();
        if (!isset($profiles[$key])) {
            throw new \InvalidArgumentException('Unknown profile.');
        }
        $name = trim($name);
        if ($name === '') {
            throw new \InvalidArgumentException('Profile name cannot be empty.');
        }
        $profiles[$key]['name'] = mb_substr($name, 0, 30);
        $this->requestStack->getSession()->set('profiles', $profiles);
    }
}