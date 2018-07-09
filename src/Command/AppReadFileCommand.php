<?php

namespace App\Command;

use App\Controller\KeywordController;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Controller\KeywordService;

class AppReadFileCommand extends Command
{
    protected static $defaultName = 'app:read-file';

    private $keywordService;
    private $container;

    public function __construct(KeywordController $keywordService, ContainerInterface $container)
    {
        $this->keywordService = $keywordService;
        $this->container = $container;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Sczytaj dane z pliku konfiguracyjnego');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Sczytywanie danych z pliku konfiguracyjnego',
            '============',
            '',
        ]);

        $this->keywordService->readFile();

        $output->writeln('Zakończone');
    }
}
