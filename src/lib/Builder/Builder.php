<?php

namespace Pharizer\Builder;

use Pharizer\Config\Main;
use Pharizer\Config\Target;
use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Builder {
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var Main */
    private $config;

    /**
     * Builder constructor.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Main $config
     */
    public function __construct(InputInterface $input, OutputInterface $output, Main $config) {
        $this->input = $input;
        $this->output = $output;
        $this->config = $config;
    }

    public function build(Target $target) {
        // TODO test
        $it = FileIterator::create($target);
        foreach($it as $file) {
            /** @var SplFileInfo $file */
            print $file->getPathname() . "\n";
        }
    }
}