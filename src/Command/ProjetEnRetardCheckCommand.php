<?php

namespace App\Command;

use App\Entity\PrmDroit;
use App\Service\ProjetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProjetEnRetardCheckCommand extends Command
{
    private $projetService;

    /**
     * UpdateRatingInElasticCommand constructor.
     * @param null $name
     * @param ProjetService $detailService
     */
    public function __construct($name = null, ProjetService $projetService)
    {
        $this->projetService = $projetService;
        parent::__construct($name);
    }


    protected function configure()
    {
        $this
            ->setName('projet:check:retard')
            ->setDescription('Check project delay')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Debut check des projets en retard</comment>');
        $this->projetService->checkProjetEnRetard($output);
        $output->writeln('<comment>Fin check des projets en retard.</comment>');
    }
}
