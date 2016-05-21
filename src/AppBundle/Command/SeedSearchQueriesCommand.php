<?php

namespace AppBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SeedSearchQueriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:seed-search_queries')
            ->setAliases(['app:ssq'])
            ->setDescription('Seeds search queries')
//            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Connection */
        $doctrine = $this->getContainer()->get('doctrine')->getConnection();

        $queries = [
            "Приморская", "Васька", "Васильевский остров", "ВО", "Спортивная", "Чкаловская", "Петроградская", "Горьковская", "Черная речка",
        ];

        $queries = array_combine(range(1, count($queries)), $queries);

        foreach ($queries as $id => $text) {
            $doctrine->insert('search_query', [
                'id' => $id,
                'text' => $text
            ]);
        }
    }

}
