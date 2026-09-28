<?php
namespace App\Controller;

use App\Entity\User;
use App\Service\UserSettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/settings', name:'app_account_settings', methods:['GET','POST'])]
    public function settings(Request $request, UserSettingsService $settings, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('account_settings', (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Invalid CSRF token.');
            }

            $user->setDisplayName((string) $request->request->get('display_name', $user->getDisplayName()));

            $teams = [];
            foreach (['fleury'=>'FC Fleury 91','lens'=>'RC Lens','racing-cff'=>'Racing CFF','braga'=>'SC Braga'] as $key=>$name) {
                $teams[] = ['key'=>$key,'name'=>$name,'enabled'=>$request->request->has('team_'.$key)];
            }

            $favoriteStops = array_values(array_filter(array_map('trim', explode("\n", (string) $request->request->get('favorite_stops', '')))));
            $favoriteStations = array_values(array_filter(array_map('trim', explode("\n", (string) $request->request->get('favorite_stations', '')))));
            $favoriteEntities = array_values(array_filter(array_map('trim', explode("\n", (string) $request->request->get('favorite_entities', '')))));

            $settings->patch($user, [
                'locale' => (string) $request->request->get('locale', 'fr'),
                'timezone' => (string) $request->request->get('timezone', 'Europe/Paris'),
                'theme' => (string) $request->request->get('theme', 'dark'),
                'accent' => (string) $request->request->get('accent', 'sky'),
                'sports' => [
                    'teams' => $teams,
                    'notify_before_minutes' => max(0, (int) $request->request->get('notify_before_minutes', 30)),
                ],
                'transport' => [
                    'favorite_stops' => $favoriteStops,
                    'favorite_stations' => $favoriteStations,
                    'show_disruptions' => $request->request->has('show_disruptions'),
                ],
                'home' => ['favorite_entities' => $favoriteEntities],
                'calendar' => [
                    'include_matches' => $request->request->has('include_matches'),
                    'include_personal' => $request->request->has('include_personal'),
                ],
            ]);

            $em->flush();
            $this->addFlash('success', 'Tes réglages ont été enregistrés.');
            return $this->redirectToRoute('app_account_settings');
        }

        return $this->render('account/settings.html.twig', [
            'user' => $user,
            'settings' => $settings->get($user),
        ]);
    }

    #[Route('/settings/password', name:'app_account_password', methods:['POST'])]
    public function password(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$this->isCsrfTokenValid('change_password', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $current = (string) $request->request->get('current_password', '');
        $new = (string) $request->request->get('new_password', '');
        $confirm = (string) $request->request->get('confirm_password', '');

        if (!$hasher->isPasswordValid($user, $current)) {
            $this->addFlash('error', 'Mot de passe actuel incorrect.');
        } elseif ($new !== $confirm || mb_strlen($new) < 10) {
            $this->addFlash('error', 'Le nouveau mot de passe doit être identique dans les deux champs et contenir au moins 10 caractères.');
        } else {
            $user->setPassword($hasher->hashPassword($user, $new));
            $em->flush();
            $this->addFlash('success', 'Mot de passe modifié.');
        }

        return $this->redirectToRoute('app_account_settings');
    }
}
