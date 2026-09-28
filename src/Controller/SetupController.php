<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserSettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class SetupController extends AbstractController
{
    #[Route('/setup', name:'app_setup', methods:['GET','POST'])]
    public function __invoke(
        Request $request,
        UserRepository $users,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
    ): Response {
        if ($users->count([]) > 0) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('initial_setup', (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Invalid CSRF token.');
            }

            $payload = [];
            for ($i=1; $i<=3; $i++) {
                $email = trim((string) $request->request->get('email_'.$i));
                $name = trim((string) $request->request->get('name_'.$i));
                $password = (string) $request->request->get('password_'.$i);
                $confirm = (string) $request->request->get('confirm_'.$i);

                if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->addFlash('error', sprintf('E-mail invalide pour le compte %d.', $i));
                    return $this->redirectToRoute('app_setup');
                }
                if ($name === '') {
                    $this->addFlash('error', sprintf('Nom manquant pour le compte %d.', $i));
                    return $this->redirectToRoute('app_setup');
                }
                if ($password !== $confirm || mb_strlen($password) < 10) {
                    $this->addFlash('error', sprintf('Mot de passe invalide pour le compte %d : 10 caractères minimum et confirmation identique.', $i));
                    return $this->redirectToRoute('app_setup');
                }

                $payload[] = [$email, $name, $password];
            }

            foreach ($payload as $index => [$email, $name, $password]) {
                $user = (new User())
                    ->setEmail($email)
                    ->setDisplayName($name)
                    ->setRoles($index === 0 ? ['ROLE_ADMIN'] : [])
                    ->setSettings(UserSettingsService::DEFAULTS);
                $user->setPassword($hasher->hashPassword($user, $password));
                $em->persist($user);
            }

            $em->flush();
            $this->addFlash('success', 'Les trois comptes ont été créés. Le premier compte est administrateur.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('setup/index.html.twig');
    }
}
