<?php

namespace App\Command;

use App\Entity\PrmDroit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProjetApdateCodeCommand extends Command
{
    protected static $defaultName = 'app:droit:update-code';

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

        $droits = $this->em->getRepository(PrmDroit::class)->findAll();
        foreach ($droits as $droit) {
            $code = $droit->getCode();
            $code = str_replace(" ","_", $code);
            $code = str_replace("(","_", $code);
            $code = str_replace(")","_", $code);
            $code = str_replace("__","_", $code);
            $droit->setCode('ROLE_'.mb_strtoupper($code));
            $io->writeln(mb_strtoupper($code));
            $this->em->persist($droit);
            $this->em->flush();
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
