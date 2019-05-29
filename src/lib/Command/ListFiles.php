<?php

namespace Pharizer\Command;

use Exception;
use Pharizer\Builder\FileIterator;
use Pharizer\Config\Loader;
use Pharizer\Config\Target;
use Pharizer\TargetSelector;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListFiles extends Command {
    /**
     *
     */
    protected function configure() {
        $this->setName('list-files');
        $this->setDescription('list files that would be included in targets');
        $this->setDefinition(
            new InputDefinition([
                                    new InputArgument('target', InputArgument::OPTIONAL | InputArgument::IS_ARRAY),
                                    new InputOption('config-file', 'c', InputOption::VALUE_REQUIRED),
                                ]
            ));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void {
        $config = Loader::fromFile($input->getOption('config-file'));
        $targetList = TargetSelector::select($config, $input->getArgument('target'));

        foreach($targetList as $target) {
            /** @var Target $target */
            $dir = realpath($target->getSourceDirectory());
            $output->writeln("=== target '{$target->getName()}' from {$dir} ===");
            $iterator = FileIterator::create($target);
            foreach ($iterator as $file) {
                /** @var SplFileInfo $file */
                $filename = $file->getPathname();
                $filename = preg_replace('#^' . preg_quote($target->getSourceDirectory(), '#') . '/?#', '', $filename);
                print $filename . "\n";
            }
        }
    }
}