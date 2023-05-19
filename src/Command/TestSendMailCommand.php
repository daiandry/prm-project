<?php

namespace App\Command;

use App\Entity\PrmCategorieTache;
use App\Service\Mailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TestSendMailCommand extends Command
{
    private $mailService;

    /**
     * UpdateRatingInElasticCommand constructor.
     * @param null $name
     * @param Mailer $mailService
     */
    public function __construct($name = null, Mailer $mailService)
    {
        $this->mailService = $mailService;
        parent::__construct($name);
    }


    protected function configure()
    {
        $this
            ->setName('test:send:mail')
            ->setDescription('Test end mail')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Debut envoie mail</comment>');
        $this->mailService->sendMailWithoutParm('Andry.RAKOTONDRAVOLA@pulse.mg', 'test_send_mail', 'Ceci est une test d envoie mail');
        $output->writeln('<comment>Fin envoie mail.</comment>');
    }
}
