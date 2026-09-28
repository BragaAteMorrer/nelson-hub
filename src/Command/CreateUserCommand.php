<?php
namespace App\Command;

use App\Entity\User;
use App\Service\UserSettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name:'app:user:create', description:'Create an independent Nelson Hub account.')]
final class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher
    ) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED)
             ->addArgument('name', InputArgument::REQUIRED)
             ->addOption('admin', null, InputOption::VALUE_NONE, 'Grant ROLE_ADMIN');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input,$output);
        $password = $io->askHidden('Password');
        $confirm = $io->askHidden('Confirm password');

        if (!$password || $password !== $confirm || mb_strlen($password) < 10) {
            $io->error('Passwords must match and contain at least 10 characters.');
            return Command::FAILURE;
        }

        $user=(new User())
            ->setEmail((string)$input->getArgument('email'))
            ->setDisplayName((string)$input->getArgument('name'))
            ->setRoles($input->getOption('admin') ? ['ROLE_ADMIN'] : [])
            ->setSettings(UserSettingsService::DEFAULTS);

        $user->setPassword($this->hasher->hashPassword($user,$password));
        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('Account created for %s.', $user->getEmail()));
        return Command::SUCCESS;
    }
}
