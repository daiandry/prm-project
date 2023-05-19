<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DroitPromoteAllRoleCommand extends Command
{
    protected static $defaultName = 'app:droit:promote-all-role';

    private $em;
    private $container;
    public function __construct(?string $name = null, EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->em = $entityManager;
        $this->container = $container;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {

            $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$arg1]);
            if ($user) {

                $droits = $this->container->getParameter('droits');
                $user->setRoles($droits);
                foreach ($droits as $droit) {
                    $io->writeln("Ajout droit $droit");
//                    $user->addRole($droit);
                }
                $this->em->persist($user);
                $this->em->flush();
            } else {
                $io->warning("User introuvable");
            }
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
