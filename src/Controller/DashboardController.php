<?php
namespace App\Controller;

use App\Entity\User;
use App\Service\DashboardService;
use App\Service\UserSettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    public function index(DashboardService $dashboard, UserSettingsService $settings): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('dashboard/index.html.twig', [
            'user'=>$user,
            'settings'=>$settings->get($user),
            'dashboard'=>$dashboard->forUser($user),
        ]);
    }
}
