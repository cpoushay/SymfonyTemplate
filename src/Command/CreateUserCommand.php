<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: '',
)]
class CreateUserCommand extends Command
{
    public function __construct(private UserRepository $userRepository, private UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $this->createUser($email, $password);

        $io->success('User created');

        return Command::SUCCESS;
    }

    private function createUser(string $email, string $plainTextPassword)
    {

        $user = new User();
        $user->setEmail($email);

        $password =  $this->userPasswordHasher->hashPassword($user, $plainTextPassword);
        $user->setPassword($password);
        
        $this->userRepository->save($user, true);
    }
}
