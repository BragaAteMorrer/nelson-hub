<?php
namespace App\Controller;

use App\Entity\User;
use App\Service\UserSettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class SettingsController extends AbstractController
{
    #[Route('/api/me/settings', name:'app_settings_get', methods:['GET'])]
    public function getSettings(UserSettingsService $settings): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->json($settings->get($user));
    }

    #[Route('/api/me/settings', name:'app_settings_patch', methods:['PATCH'])]
    public function patchSettings(Request $request, UserSettingsService $settings): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        return $this->json($settings->patch($user, is_array($payload) ? $payload : []));
    }
}
