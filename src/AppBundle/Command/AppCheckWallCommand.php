<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppCheckWallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:check-wall')
            ->setAliases(['app:cw'])
            ->setDescription('Check wall for new posts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parser = $this->getContainer()->get('app.parser_service');
        $parser->parse();
    }

}
