<?php

namespace App\Command;

use App\Entity\PrmUserStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StatusCreateCommand extends Command
{
    protected static $defaultName = 'app:status:create';

    private $em;

    public function __construct(?string $name = null, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
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
        $status = ["active","desactive"];
        foreach ($status as $sta) {
            $stat = new PrmUserStatus();
            $stat->setCode(mb_strtoupper($sta));
            $stat->setLibelle(mb_strtoupper($sta));
            $this->em->persist($stat);
            $this->em->flush();
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
