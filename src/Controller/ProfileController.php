<?php
namespace App\Controller;

use App\Service\Profile\ProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile/switch/{key}', name: 'app_profile_switch', methods: ['POST'])]
    public function switch(string $key, ProfileService $profiles): RedirectResponse
    {
        $profiles->switch($key);
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/profile/{key}/rename', name: 'app_profile_rename', methods: ['POST'])]
    public function rename(string $key, Request $request, ProfileService $profiles): RedirectResponse
    {
        $profiles->rename($key, (string) $request->request->get('name', ''));
        return $this->redirectToRoute('app_dashboard');
    }
}