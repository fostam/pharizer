<?php

namespace Pharizer\Command;

use Exception;
use Pharizer\Builder\Builder;
use Pharizer\Config\Loader;
use Pharizer\TargetSelector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Build extends Command {
    protected function configure(): void {
        $this->setName('build');
        $this->setDescription('build targets');
        $this->setDefinition(
            new InputDefinition([
                new InputArgument('target', InputArgument::OPTIONAL | InputArgument::IS_ARRAY),
                new InputOption('config-file', 'c', InputOption::VALUE_REQUIRED),
            ]
        ));
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $config = Loader::fromFile($input->getOption('config-file'));
        $builder = new Builder($input, $output, $config);

        $targetList = TargetSelector::select($config, $input->getArgument('target'));

        foreach($targetList as $target) {
            $builder->build($target);
        }

        return 0;
    }
}