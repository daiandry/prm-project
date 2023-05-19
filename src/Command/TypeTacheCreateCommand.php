<?php

namespace App\Command;

use App\Entity\PrmTypeTache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TypeTacheCreateCommand extends Command
{
    protected static $defaultName = 'app:type-tache:create';
    private $container;
    public function __construct(?string $name = null, ContainerInterface $container)
    {
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

        $types = $this->container->getParameter('type_tache');

        if ($types) {
            $em = $this->container->get('doctrine.orm.default_entity_manager');
            $typeRp = $em->getRepository(PrmTypeTache::class);
            foreach ($types as $type) {

                if (!$typeRp->findBy(['libelle' => $type])) {
                    $io->writeln("Ajout type tache :" .$type);
                    $cat = new PrmTypeTache();
                    $cat->setLibelle($type);
                    $em->persist($cat);
                    $em->flush();
                }
            }
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
